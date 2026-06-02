<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class AccountantController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Accountants/index', [
            'accountants' => User::query()
                ->where('role', 'accountant')
                ->withCount('assignedCases')
                ->orderBy('first_name')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Accountants/create');
    }

    public function store(Request $request, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()],
        ]);

        $accountant = User::create([
            ...$validated,
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'role' => 'accountant',
            'email_verified_at' => now(),
        ]);

        $audit->record('admin.accountant_created', null, ['accountant_id' => $accountant->id]);

        return redirect()->route('admin.accountants.index')->with('success', 'Accountant account created.');
    }
}
