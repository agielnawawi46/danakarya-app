<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ShuDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuditFinanceController extends Controller
{
    public function index(): View
    {
        return view('pengawas.audit-finance.index');
    }

    public function ledger(Request $request): View
    {
        $accounts = Account::with(['journalLines.journalEntry'])->orderBy('code')->get();
        return view('pengawas.audit-finance.ledger', compact('accounts'));
    }

    public function neraca(): View
    {
        $accounts    = Account::orderBy('code')->get();
        $assets      = $accounts->where('type', 'asset');
        $liabilities = $accounts->where('type', 'liability');
        $equities    = $accounts->where('type', 'equity');

        return view('pengawas.audit-finance.neraca', compact('assets', 'liabilities', 'equities'));
    }

    public function labaRugi(Request $request): View
    {
        $year = (int) ($request->year ?? now()->year);
        $orgId = Auth::user()->organization_id;

        $incomeAccounts  = Account::where('type', 'income')->get();
        $expenseAccounts = Account::where('type', 'expense')->get();

        $totalIncome = 0; $totalExpense = 0;

        foreach ($incomeAccounts as $acc) {
            $acc->balance = JournalEntryLine::withoutGlobalScopes()
                ->whereHas('journalEntry', fn($q) => $q->where('organization_id', $orgId)->whereYear('date', $year))
                ->where('account_id', $acc->id)->sum('credit');
            $totalIncome += $acc->balance;
        }

        foreach ($expenseAccounts as $acc) {
            $acc->balance = JournalEntryLine::withoutGlobalScopes()
                ->whereHas('journalEntry', fn($q) => $q->where('organization_id', $orgId)->whereYear('date', $year))
                ->where('account_id', $acc->id)->sum('debit');
            $totalExpense += $acc->balance;
        }

        return view('pengawas.audit-finance.laba-rugi', compact('incomeAccounts', 'expenseAccounts', 'totalIncome', 'totalExpense', 'year'));
    }
}
