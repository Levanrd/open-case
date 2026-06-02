<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\TaxCase;
use App\Models\User;
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
        $cases = TaxCase::query()
            ->with(['business', 'user', 'result', 'latestPayment.servicePackage', 'assignedAccountant'])
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Cases/index', [
            'cases' => $cases,
            'filters' => $request->only('status'),
            'accountants' => User::query()->where('role', 'accountant')->orderBy('first_name')->get(),
        ]);
    }

    public function show(TaxCase $taxCase): Response
    {
        return Inertia::render('Admin/Cases/show', [
            'caseRecord' => $taxCase->load([
                'business',
                'user',
                'result',
                'documents.uploader',
                'checklistItems',
                'notes.user',
                'payments.servicePackage',
                'assignedAccountant',
                'auditLogs.user',
            ]),
            'accountants' => User::query()->where('role', 'accountant')->orderBy('first_name')->get(),
        ]);
    }

    public function assign(Request $request, TaxCase $taxCase, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_accountant_id' => ['required', Rule::exists('users', 'id')->where('role', 'accountant')],
        ]);

        $taxCase->update([
            'assigned_accountant_id' => $validated['assigned_accountant_id'],
            'status' => TaxCase::STATUS_ASSIGNED,
        ]);

        $audit->record('admin.case_assigned', $taxCase, $validated);

        return back()->with('success', 'Case assigned to the selected accountant.');
    }

    public function verifyPayment(Payment $payment, AuditLogger $audit): RedirectResponse
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $payment->taxCase->update(['status' => TaxCase::STATUS_PAID]);
        $audit->record('admin.payment_verified', $payment->taxCase, ['payment_id' => $payment->id]);

        return back()->with('success', 'Payment verified. The case is ready for assignment.');
    }
}
