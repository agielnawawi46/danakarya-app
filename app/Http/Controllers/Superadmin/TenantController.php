<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(): View
    {
        $tenants = Organization::withCount('users')->latest()->paginate(15);
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function show(Organization $tenant): View
    {
        return view('superadmin.tenants.show', compact('tenant'));
    }

    public function toggleActive(Organization $tenant): RedirectResponse
    {
        $tenant->update(['is_active' => !$tenant->is_active]);
        $status = $tenant->is_active ? 'diaktifkan' : 'ditangguhkan';
        return back()->with('success', "Koperasi {$tenant->name} berhasil {$status}.");
    }

    public function destroy(Organization $tenant): RedirectResponse
    {
        $tenant->delete();
        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Koperasi berhasil dihapus.');
    }
}
