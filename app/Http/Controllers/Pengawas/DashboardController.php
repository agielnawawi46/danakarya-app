<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Deposit;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Loan;
use App\Models\LoanSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $orgId = Auth::user()->organization_id;

        $totalLoans     = Loan::where('status', 'active')->sum('amount');
        $totalSimpanan  = Deposit::where('status','completed')->where('transaction_type','credit')->sum('amount')
                          - Deposit::where('status','completed')->where('transaction_type','debit')->sum('amount');

        $totalBunga     = JournalEntryLine::withoutGlobalScopes()
            ->whereHas('journalEntry', fn($q) => $q->where('organization_id', $orgId)->whereYear('date', now()->year))
            ->whereHas('account', fn($q) => $q->where('code', '4-101'))
            ->sum('credit');

        $overdueAmount  = LoanSchedule::where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->sum('total_amount');

        $nplRatio = $totalLoans > 0 ? ($overdueAmount / $totalLoans) * 100 : 0;

        // Monthly income trend (last 6 months)
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = [
                'label'  => $date->format('M Y'),
                'income' => JournalEntryLine::withoutGlobalScopes()
                    ->whereHas('journalEntry', fn($q) => $q
                        ->where('organization_id', $orgId)
                        ->whereYear('date', $date->year)
                        ->whereMonth('date', $date->month))
                    ->whereHas('account', fn($q) => $q->where('type', 'income'))
                    ->sum('credit'),
            ];
        }

        return view('pengawas.dashboard', compact('totalLoans','totalSimpanan','totalBunga','nplRatio','months','overdueAmount'));
    }
}
