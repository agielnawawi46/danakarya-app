<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function __construct(private readonly AuditService $auditService) {}

    public function setup(): View
    {
        $org = Auth::user()->organization;
        return view('admin.organization.setup', compact('org'));
    }

    public function index(): View
    {
        $org = Auth::user()->organization;
        return view('admin.organization.index', compact('org'));
    }

    public function update(Request $request): RedirectResponse
    {
        $org = Auth::user()->organization;

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'legal_name'   => ['nullable', 'string', 'max:255'],
            'address'      => ['nullable', 'string'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email'],
            'legal_number' => ['nullable', 'string', 'max:100'],
            'logo'         => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($org->logo) Storage::disk('public')->delete($org->logo);
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $validated['is_configured'] = true;

        $org->update($validated);

        $this->auditService->log('updated_organization', "Profil koperasi diperbarui: {$org->name}");

        return redirect()->route('admin.dashboard')
            ->with('success', 'Profil koperasi berhasil disimpan!');
    }
}
