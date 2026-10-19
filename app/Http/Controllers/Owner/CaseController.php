<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Document;
use App\Models\ServicePackage;
use App\Models\TaxCase;
use App\Services\AuditLogger;
use App\Services\CaseAssessment\AssessCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CaseController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('Owner/Cases/create', [
            'businesses' => $request->user()->businesses()->latest()->get(),
            'packages' => ServicePackage::query()->where('active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, AssessCase $assessor, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'business_id' => ['nullable', 'integer'],
            'registered_name' => ['required', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:40'],
            'rdo_code' => ['nullable', 'string', 'max:50'],
            'business_type' => ['required', 'string', 'max:120'],
            'taxpayer_type' => ['nullable', 'string', 'max:120'],
            'registered_address' => ['nullable', 'string', 'max:1000'],
            'registration_date' => ['nullable', 'date'],
            'business_activity' => ['nullable', 'string', 'max:255'],
            'line_of_business' => ['nullable', 'string', 'max:255'],
            'business_status' => ['required', 'string', 'max:80'],
            'last_year_of_operation' => ['nullable', 'integer', 'min:1900', 'max:'.now()->year],
            'still_operating' => ['required', Rule::in(['yes', 'no'])],
            'primary_goal' => ['nullable', 'string', 'max:120'],
            'case_type' => ['required', 'string', 'max:120'],
            'filed_closure' => ['required', Rule::in(['yes', 'no', 'not_sure'])],
            'received_bir_notice' => ['required', Rule::in(['yes', 'no'])],
            'has_cor' => ['required', Rule::in(['yes', 'no'])],
            'knows_rdo' => ['required', Rule::in(['yes', 'no'])],
            'has_unused_receipts' => ['required', Rule::in(['yes', 'no', 'not_sure'])],
            'has_books' => ['required', Rule::in(['yes', 'no', 'not_sure'])],
            'last_filed_year' => ['nullable', 'integer', 'min:1900', 'max:'.now()->year],
            'tax_types' => ['array'],
            'tax_types.*' => ['string', 'max:50'],
            'documents' => ['array'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
            'document_types' => ['array'],
            'document_types.*' => ['nullable', 'string', 'max:120'],
            'disclaimer_accepted' => ['accepted'],
        ]);

        $case = DB::transaction(function () use ($request, $validated, $assessor, $audit): TaxCase {
            $tinDigits = preg_replace('/\D+/', '', (string) ($validated['tin'] ?? ''));
            $businessPayload = [
                'registered_name' => $validated['registered_name'],
                'trade_name' => $validated['trade_name'] ?? null,
                'tin_encrypted' => $validated['tin'] ?? null,
                'tin_last_four' => $tinDigits ? substr($tinDigits, -4) : null,
                'rdo_code' => $validated['rdo_code'] ?? null,
                'business_type' => $validated['business_type'],
                'taxpayer_type' => $validated['taxpayer_type'] ?? null,
                'registered_address' => $validated['registered_address'] ?? null,
                'registration_date' => $validated['registration_date'] ?? null,
                'business_activity' => $validated['business_activity'] ?? null,
                'line_of_business' => $validated['line_of_business'] ?? null,
                'business_status' => $validated['business_status'],
                'last_year_of_operation' => $validated['last_year_of_operation'] ?? null,
                'still_operating' => $validated['still_operating'] === 'yes',
                'primary_goal' => $validated['primary_goal'] ?? null,
            ];

            $business = Business::query()
                ->where('user_id', $request->user()->id)
                ->when($validated['business_id'] ?? null, fn ($query, $id) => $query->whereKey($id))
                ->first();

            $business = $business
                ? tap($business)->update($businessPayload)
                : $request->user()->businesses()->create($businessPayload);

            $case = TaxCase::create([
                'user_id' => $request->user()->id,
                'business_id' => $business->id,
                'case_type' => $validated['case_type'],
                'status' => TaxCase::STATUS_AWAITING_PAYMENT,
            ]);

            $answers = collect($validated)
                ->only([
                    'case_type',
                    'still_operating',
                    'filed_closure',
                    'received_bir_notice',
                    'has_cor',
                    'knows_rdo',
                    'has_unused_receipts',
                    'has_books',
                    'last_filed_year',
                    'tax_types',
                    'primary_goal',
                ]);

            $answers->each(fn ($value, $key) => $case->answers()->create([
                'question_key' => $key,
                'answer_value' => $value,
            ]));

            $assessment = $assessor->handle($case);

            $case->update([
                'risk_level' => $assessment['risk_level'],
                'summary' => $assessment['generated_summary'],
            ]);

            $case->result()->create([
                'result_type' => $assessment['result_type'],
                'risk_level' => $assessment['risk_level'],
                'risk_score' => $assessment['risk_score'],
                'generated_summary' => $assessment['generated_summary'],
                'recommendations' => $assessment['recommendations'],
                'disclaimer_accepted' => true,
            ]);

            foreach ($assessment['checklist'] as $item) {
                $case->checklistItems()->create($item);
            }

            $this->storeUploadedDocuments($request, $case);
            $audit->record('owner.case_created', $case);

            return $case;
        });

        return redirect()->route('owner.cases.show', $case)
            ->with('success', 'Case created. Review your preliminary result and choose the service package that fits the case.');
    }

    public function show(Request $request, TaxCase $taxCase): Response
    {
        $this->ensureOwner($request, $taxCase);

        return Inertia::render('Owner/Cases/show', [
            'caseRecord' => $taxCase->load([
                'business',
                'result',
                'documents.requester',
                'checklistItems',
                'latestPayment.servicePackage',
                'assignedAccountant',
            ]),
            'packages' => ServicePackage::query()->where('active', true)->orderBy('sort_order')->get(),
            'clientNotes' => $taxCase->notes()->where('visibility', 'client')->with('user')->latest()->get(),
        ]);
    }

    public function uploadDocument(Request $request, TaxCase $taxCase, AuditLogger $audit): RedirectResponse
    {
        $this->ensureOwner($request, $taxCase);

        $request->validate([
            'document_type' => ['required', 'string', 'max:120'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        $documentType = $request->string('document_type')->toString();

        $this->createDocumentFromUpload($request, $taxCase, $request->file('document'), $documentType);
        $audit->record('owner.document_uploaded', $taxCase, ['document_type' => $documentType]);

        return back()->with('success', 'Document uploaded to the private case file.');
    }

    public function fulfillDocumentRequest(Request $request, Document $document, AuditLogger $audit): RedirectResponse
    {
        $taxCase = $document->taxCase;
        $this->ensureOwner($request, $taxCase);

        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        $file = $request->file('document');
        $path = $file->store("cases/{$taxCase->case_number}", 'local');

        $document->update([
            'uploaded_by' => $request->user()->id,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'uploaded',
            'fulfilled_at' => now(),
        ]);

        $audit->record('owner.document_request_fulfilled', $taxCase, ['document_id' => $document->id]);

        return back()->with('success', 'Requested document uploaded.');
    }

    public function selectPackage(Request $request, TaxCase $taxCase, AuditLogger $audit): RedirectResponse
    {
        $this->ensureOwner($request, $taxCase);

        $validated = $request->validate([
            'service_package_id' => ['required', 'exists:service_packages,id'],
        ]);

        $package = ServicePackage::query()->where('active', true)->findOrFail($validated['service_package_id']);

        $taxCase->payments()->updateOrCreate(
            ['status' => 'pending'],
            [
                'user_id' => $request->user()->id,
                'service_package_id' => $package->id,
                'amount' => $package->price,
                'provider' => 'manual',
            ],
        );

        $taxCase->update(['status' => TaxCase::STATUS_AWAITING_PAYMENT]);
        $audit->record('owner.package_selected', $taxCase, ['package' => $package->name]);

        return back()->with('success', 'Package selected. Add your payment reference once the Open Case service fee has been sent.');
    }

    public function submitPaymentReference(Request $request, TaxCase $taxCase, AuditLogger $audit): RedirectResponse
    {
        $this->ensureOwner($request, $taxCase);

        $validated = $request->validate([
            'provider_reference' => ['required', 'string', 'max:120'],
        ]);

        $payment = $taxCase->payments()->where('status', 'pending')->latest()->firstOrFail();
        $payment->update([
            'provider_reference' => $validated['provider_reference'],
            'status' => 'pending_review',
        ]);

        $taxCase->update(['status' => TaxCase::STATUS_PAYMENT_REVIEW]);
        $audit->record('owner.payment_reference_submitted', $taxCase, ['payment_id' => $payment->id]);

        return back()->with('success', 'Payment reference submitted for admin verification.');
    }

    private function storeUploadedDocuments(Request $request, TaxCase $case): void
    {
        foreach ($request->file('documents', []) as $index => $file) {
            $type = $request->input("document_types.{$index}") ?: 'Supporting document';
            $this->createDocumentFromUpload($request, $case, $file, $type);
        }
    }

    private function createDocumentFromUpload(Request $request, TaxCase $case, mixed $file, string $type): void
    {
        $path = $file->store("cases/{$case->case_number}", 'local');

        $case->documents()->create([
            'uploaded_by' => $request->user()->id,
            'document_type' => $type,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'uploaded',
            'fulfilled_at' => now(),
        ]);
    }

    private function ensureOwner(Request $request, TaxCase $case): void
    {
        abort_unless($case->user_id === $request->user()->id, 403);
    }
}
