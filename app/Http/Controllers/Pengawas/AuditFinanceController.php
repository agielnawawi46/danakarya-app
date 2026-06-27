<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ShuDistribution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function exportLedger(Request $request)
    {
        $accounts = Account::with(['journalLines.journalEntry'])->orderBy('code')->get();
        $pdf = Pdf::loadView('pengawas.audit-finance.pdf.ledger', compact('accounts'));
        return $pdf->download('buku_besar.pdf');
    }

    public function exportNeraca()
    {
        $accounts    = Account::orderBy('code')->get();
        $assets      = $accounts->where('type', 'asset');
        $liabilities = $accounts->where('type', 'liability');
        $equities    = $accounts->where('type', 'equity');

        $pdf = Pdf::loadView('pengawas.audit-finance.pdf.neraca', compact('assets', 'liabilities', 'equities'));
        return $pdf->download('neraca.pdf');
    }

    public function exportLabaRugi(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);
        $orgId = Auth::user()->organization_id;

        $incomeAccounts  = Account::where('type', 'income')->get();
        $expenseAccounts = Account::where('type', 'expense')->get();

        foreach ($incomeAccounts as $acc) {
            $acc->balance = JournalEntryLine::withoutGlobalScopes()
                ->whereHas('journalEntry', fn($q) => $q->where('organization_id', $orgId)->whereYear('date', $year))
                ->where('account_id', $acc->id)->sum('credit');
        }

        foreach ($expenseAccounts as $acc) {
            $acc->balance = JournalEntryLine::withoutGlobalScopes()
                ->whereHas('journalEntry', fn($q) => $q->where('organization_id', $orgId)->whereYear('date', $year))
                ->where('account_id', $acc->id)->sum('debit');
        }

        $pdf = Pdf::loadView('pengawas.audit-finance.pdf.laba-rugi', compact('incomeAccounts', 'expenseAccounts', 'year'));
        return $pdf->download("laba_rugi_{$year}.pdf");
    }

    public function kas(Request $request): View
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $journals = JournalEntry::with('lines.account', 'creator')
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')
            ->get();

        $totalDebit  = $journals->sum(fn($j) => $j->lines->sum('debit'));
        $totalCredit = $journals->sum(fn($j) => $j->lines->sum('credit'));

        return view('pengawas.audit-finance.kas', compact('journals', 'from', 'to', 'totalDebit', 'totalCredit'));
    }

    public function exportKas(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $journals = JournalEntry::with('lines.account', 'creator')
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')
            ->get();

        $totalDebit  = $journals->sum(fn($j) => $j->lines->sum('debit'));
        $totalCredit = $journals->sum(fn($j) => $j->lines->sum('credit'));

        $pdf = Pdf::loadView('pengawas.audit-finance.pdf.kas', compact('journals', 'from', 'to', 'totalDebit', 'totalCredit'));
        return $pdf->download("arus_kas_{$from}_{$to}.pdf");
    }

    public function simpanan(): View
    {
        $orgId   = Auth::user()->organization_id;
        $members = User::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->whereHas('roles', fn($q) => $q->where('name', 'anggota'))
            ->get()
            ->map(function ($m) {
                $m->simpanan_pokok    = $m->getTotalSimpananByType('pokok');
                $m->simpanan_wajib    = $m->getTotalSimpananByType('wajib');
                $m->simpanan_sukarela = $m->getTotalSimpananByType('sukarela');
                $m->total             = $m->getTotalSimpanan();
                return $m;
            });

        $totals = [
            'pokok'    => $members->sum('simpanan_pokok'),
            'wajib'    => $members->sum('simpanan_wajib'),
            'sukarela' => $members->sum('simpanan_sukarela'),
            'grand'    => $members->sum('total'),
        ];

        return view('pengawas.audit-finance.simpanan', compact('members', 'totals'));
    }

    public function exportSimpanan()
    {
        $orgId   = Auth::user()->organization_id;
        $members = User::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->whereHas('roles', fn($q) => $q->where('name', 'anggota'))
            ->get()
            ->map(function ($m) {
                $m->simpanan_pokok    = $m->getTotalSimpananByType('pokok');
                $m->simpanan_wajib    = $m->getTotalSimpananByType('wajib');
                $m->simpanan_sukarela = $m->getTotalSimpananByType('sukarela');
                $m->total             = $m->getTotalSimpanan();
                return $m;
            });

        $totals = [
            'pokok'    => $members->sum('simpanan_pokok'),
            'wajib'    => $members->sum('simpanan_wajib'),
            'sukarela' => $members->sum('simpanan_sukarela'),
            'grand'    => $members->sum('total'),
        ];

        $pdf = Pdf::loadView('pengawas.audit-finance.pdf.simpanan', compact('members', 'totals'));
        return $pdf->download('laporan_simpanan.pdf');
    }

    public function shu(Request $request): View
    {
        $year          = (int) ($request->year ?? now()->year);
        $distributions = ShuDistribution::where('year', $year)->get();

        $incomeAccounts  = Account::where('type', 'income')->get();
        $expenseAccounts = Account::where('type', 'expense')->get();

        $totalIncome  = 0;
        $totalExpense = 0;

        foreach ($incomeAccounts as $acc) {
            $balance = JournalEntryLine::withoutGlobalScopes()
                ->whereHas('journalEntry', fn($q) => $q->where('organization_id', Auth::user()->organization_id)->whereYear('date', $year))
                ->where('account_id', $acc->id)
                ->sum('credit');
            $totalIncome += $balance;
        }

        foreach ($expenseAccounts as $acc) {
            $balance = JournalEntryLine::withoutGlobalScopes()
                ->whereHas('journalEntry', fn($q) => $q->where('organization_id', Auth::user()->organization_id)->whereYear('date', $year))
                ->where('account_id', $acc->id)
                ->sum('debit');
            $totalExpense += $balance;
        }

        return view('pengawas.audit-finance.shu', compact('distributions', 'year', 'totalIncome', 'totalExpense'));
    }

    public function exportShu(Request $request)
    {
        $year          = (int) ($request->year ?? now()->year);
        $distributions = ShuDistribution::where('year', $year)->get();

        $incomeAccounts  = Account::where('type', 'income')->get();
        $expenseAccounts = Account::where('type', 'expense')->get();

        $totalIncome  = 0;
        $totalExpense = 0;

        foreach ($incomeAccounts as $acc) {
            $balance = JournalEntryLine::withoutGlobalScopes()
                ->whereHas('journalEntry', fn($q) => $q->where('organization_id', Auth::user()->organization_id)->whereYear('date', $year))
                ->where('account_id', $acc->id)
                ->sum('credit');
            $totalIncome += $balance;
        }

        foreach ($expenseAccounts as $acc) {
            $balance = JournalEntryLine::withoutGlobalScopes()
                ->whereHas('journalEntry', fn($q) => $q->where('organization_id', Auth::user()->organization_id)->whereYear('date', $year))
                ->where('account_id', $acc->id)
                ->sum('debit');
            $totalExpense += $balance;
        }

        $pdf = Pdf::loadView('pengawas.audit-finance.pdf.shu', compact('distributions', 'year', 'totalIncome', 'totalExpense'));
        return $pdf->download("shu_{$year}.pdf");
    }
}
