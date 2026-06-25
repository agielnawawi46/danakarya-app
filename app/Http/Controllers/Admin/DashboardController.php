<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $org = Auth::user()->organization;

        $stats = [
            'total_members'   => User::withoutGlobalScopes()->where('organization_id', $org->id)->whereHas('roles', fn($q)=>$q->where('name','anggota'))->count(),
            'total_simpanan'  => Deposit::where('status','completed')->where('transaction_type','credit')->sum('amount')
                                 - Deposit::where('status','completed')->where('transaction_type','debit')->sum('amount'),
            'total_pinjaman'  => Loan::whereIn('status',['active','approved'])->sum('amount'),
            'pending_loans'   => Loan::where('status','pending')->count(),
            'pending_deposits'=> Deposit::where('status','pending')->count(),
        ];

        $recentLoans    = Loan::with('user')->latest()->take(5)->get();
        $recentDeposits = Deposit::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('org', 'stats', 'recentLoans', 'recentDeposits'));
    }
}
