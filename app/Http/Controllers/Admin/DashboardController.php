<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ServicePackage;
use App\Models\TaxCase;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $cases = TaxCase::query()
            ->with(['business', 'user', 'result', 'latestPayment.servicePackage', 'assignedAccountant'])
            ->latest()
            ->limit(40)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_cases' => TaxCase::count(),
                'pending_payments' => Payment::where('status', 'pending_review')->count(),
                'unassigned_paid' => TaxCase::whereIn('status', ['paid', 'payment_review'])->whereNull('assigned_accountant_id')->count(),
                'active_accountants' => User::where('role', 'accountant')->count(),
            ],
            'cases' => $cases,
            'pendingPayments' => Payment::query()
                ->with(['taxCase.business', 'taxCase.user', 'servicePackage'])
                ->where('status', 'pending_review')
                ->latest()
                ->get(),
            'accountants' => User::query()->where('role', 'accountant')->orderBy('first_name')->get(),
            'packages' => ServicePackage::query()->orderBy('sort_order')->get(),
        ]);
    }
}
