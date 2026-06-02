<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Models\TaxCase;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CaseController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Accountant/Cases/index', [
            'cases' => $request->user()
                ->assignedCases()
                ->with(['business', 'user', 'result', 'latestPayment.servicePackage'])
                ->latest()
                ->get(),
        ]);
    }

    public function show(Request $request, TaxCase $taxCase): Response
    {
        $this->ensureAssigned($request, $taxCase);

        return Inertia::render('Accountant/Cases/show', [
            'caseRecord' => $taxCase->load([
                'business',
                'user',
                'result',
                'documents.uploader',
                'checklistItems',
                'notes.user',
                'payments.servicePackage',
            ]),
        ]);
    }

    public function updateStatus(Request $request, TaxCase $taxCase, AuditLogger $audit): RedirectResponse
    {
        $this->ensureAssigned($request, $taxCase);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                TaxCase::STATUS_UNDER_REVIEW,
                TaxCase::STATUS_NEEDS_DOCUMENTS,
                TaxCase::STATUS_READY,
                TaxCase::STATUS_COMPLETED,
            ])],
            'final_recommendation' => ['nullable', 'string', 'max:4000'],
        ]);

        $taxCase->update([
            'status' => $validated['status'],
            'final_recommendation' => $validated['final_recommendation'] ?? $taxCase->final_recommendation,
            'completed_at' => $validated['status'] === TaxCase::STATUS_COMPLETED ? now() : null,
        ]);

        $audit->record('accountant.status_updated', $taxCase, $validated);

        return back()->with('success', 'Case status updated.');
    }

    public function storeNote(Request $request, TaxCase $taxCase, AuditLogger $audit): RedirectResponse
    {
        $this->ensureAssigned($request, $taxCase);

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:4000'],
            'visibility' => ['required', Rule::in(['internal', 'client'])],
        ]);

        $taxCase->notes()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        $audit->record('accountant.note_created', $taxCase, ['visibility' => $validated['visibility']]);

        return back()->with('success', 'Case note added.');
    }

    public function updateChecklist(Request $request, ChecklistItem $checklistItem, AuditLogger $audit): RedirectResponse
    {
        $taxCase = $checklistItem->taxCase;
        $this->ensureAssigned($request, $taxCase);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'in_progress', 'done', 'blocked'])],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $checklistItem->update($validated);
        $audit->record('accountant.checklist_updated', $taxCase, ['checklist_id' => $checklistItem->id]);

        return back()->with('success', 'Checklist updated.');
    }

    public function requestDocument(Request $request, TaxCase $taxCase, AuditLogger $audit): RedirectResponse
    {
        $this->ensureAssigned($request, $taxCase);

        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:120'],
            'request_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $taxCase->documents()->create([
            ...$validated,
            'requested_by' => $request->user()->id,
            'status' => 'requested',
        ]);

        $taxCase->update(['status' => TaxCase::STATUS_NEEDS_DOCUMENTS]);
        $audit->record('accountant.document_requested', $taxCase, ['document_type' => $validated['document_type']]);

        return back()->with('success', 'Document request sent to the client case page.');
    }

    private function ensureAssigned(Request $request, TaxCase $taxCase): void
    {
        abort_unless($taxCase->assigned_accountant_id === $request->user()->id, 403);
    }
}
