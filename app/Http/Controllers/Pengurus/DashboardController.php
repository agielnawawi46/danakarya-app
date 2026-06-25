<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'pending_loans'       => Loan::where('status', 'pending')->count(),
            'active_loans'        => Loan::where('status', 'active')->count(),
            'pending_withdrawals' => Deposit::where('status','pending')->where('transaction_type','debit')->count(),
            'overdue_schedules'   => LoanSchedule::where('status','pending')->where('due_date','<',now()->toDateString())->count(),
            'total_kas'           => Deposit::where('status','completed')->where('transaction_type','credit')->sum('amount')
                                     - Deposit::where('status','completed')->where('transaction_type','debit')->sum('amount'),
        ];

        $pendingLoans   = Loan::with('user')->where('status','pending')->latest()->take(5)->get();
        $overdueScheds  = LoanSchedule::with('user','loan')
            ->where('status','pending')
            ->where('due_date','<',now()->toDateString())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        return view('pengurus.dashboard', compact('stats', 'pendingLoans', 'overdueScheds'));
    }
}
