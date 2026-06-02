<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $cases = $request->user()
            ->cases()
            ->with(['business', 'result', 'latestPayment.servicePackage', 'assignedAccountant'])
            ->latest()
            ->get();

        return Inertia::render('Owner/Dashboard', [
            'cases' => $cases,
            'stats' => [
                'total' => $cases->count(),
                'open' => $cases->whereNotIn('status', ['completed', 'cancelled'])->count(),
                'needs_payment' => $cases->whereIn('status', ['awaiting_payment', 'payment_review'])->count(),
                'completed' => $cases->where('status', 'completed')->count(),
            ],
            'packages' => ServicePackage::query()->where('active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
