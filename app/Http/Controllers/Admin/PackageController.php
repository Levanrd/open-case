<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function store(Request $request, AuditLogger $audit): RedirectResponse
    {
        $validated = $this->validated($request);
        $package = ServicePackage::create($validated);

        $audit->record('admin.package_created', null, ['package_id' => $package->id]);

        return back()->with('success', 'Service package created.');
    }

    public function update(Request $request, ServicePackage $servicePackage, AuditLogger $audit): RedirectResponse
    {
        $servicePackage->update($this->validated($request));
        $audit->record('admin.package_updated', null, ['package_id' => $servicePackage->id]);

        return back()->with('success', 'Service package updated.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'case_type' => ['nullable', 'string', 'max:120'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:255'],
            'active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['active'] = (bool) ($validated['active'] ?? false);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        return $validated;
    }
}
