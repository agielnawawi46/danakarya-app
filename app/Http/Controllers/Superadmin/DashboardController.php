<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_tenants'  => Organization::count(),
            'active_tenants' => Organization::where('is_active', true)->count(),
        ];

        $recentTenants = Organization::latest()->take(5)->get();

        return view('superadmin.dashboard', compact('stats', 'recentTenants'));
    }
}
