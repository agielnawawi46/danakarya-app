<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Deposit;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ShuDistribution;
use App\Models\User;
use App\Services\ShuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly ShuService $shuService) {}

    public function index(): View
    {
        return view('pengurus.reports.index');
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

        return view('pengurus.reports.kas', compact('journals', 'from', 'to', 'totalDebit', 'totalCredit'));
    }

    public function neraca(): View
    {
        $accounts = Account::with('journalLines')->orderBy('code')->get();

        $assets      = $accounts->where('type', 'asset');
        $liabilities = $accounts->where('type', 'liability');
        $equities    = $accounts->where('type', 'equity');

        $totalAssets      = $assets->sum(fn($a) => $a->getBalance());
        $totalLiabilities = $liabilities->sum(fn($a) => $a->getBalance());
        $totalEquities    = $equities->sum(fn($a) => $a->getBalance());

        return view('pengurus.reports.neraca', compact(
            'assets', 'liabilities', 'equities',
            'totalAssets', 'totalLiabilities', 'totalEquities'
        ));
    }

    public function labaRugi(Request $request): View
    {
        $year = (int) ($request->year ?? now()->year);

        $incomeAccounts  = Account::where('type', 'income')->get();
        $expenseAccounts = Account::where('type', 'expense')->get();

        $totalIncome  = 0;
        $totalExpense = 0;

        foreach ($incomeAccounts as $acc) {
            $acc->balance = JournalEntryLine::withoutGlobalScopes()
                ->whereHas('journalEntry', fn($q) => $q->where('organization_id', Auth::user()->organization_id)->whereYear('date', $year))
                ->where('account_id', $acc->id)
                ->sum('credit');
            $totalIncome += $acc->balance;
        }

        foreach ($expenseAccounts as $acc) {
            $acc->balance = JournalEntryLine::withoutGlobalScopes()
                ->whereHas('journalEntry', fn($q) => $q->where('organization_id', Auth::user()->organization_id)->whereYear('date', $year))
                ->where('account_id', $acc->id)
                ->sum('debit');
            $totalExpense += $acc->balance;
        }

        $netProfit = $totalIncome - $totalExpense;

        return view('pengurus.reports.laba-rugi', compact(
            'incomeAccounts', 'expenseAccounts', 'totalIncome', 'totalExpense', 'netProfit', 'year'
        ));
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

        return view('pengurus.reports.simpanan', compact('members', 'totals'));
    }

    public function shu(Request $request): View
    {
        $year          = (int) ($request->year ?? now()->year);
        $distributions = ShuDistribution::where('year', $year)->get();
        return view('pengurus.reports.shu', compact('distributions', 'year'));
    }

    public function calculateShu(Request $request): RedirectResponse
    {
        $request->validate([
            'year'          => ['required', 'integer', 'min:2000', 'max:2100'],
            'total_income'  => ['required', 'numeric', 'min:0'],
            'total_expense' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $org = Auth::user()->organization;
            $dist = $this->shuService->calculateAnnualShu(
                $org,
                (int) $request->year,
                (float) $request->total_income,
                (float) $request->total_expense,
            );

            return redirect()->route('pengurus.reports.shu', ['year' => $request->year])
                ->with('success', "SHU tahun {$request->year} berhasil dihitung. Total laba: Rp " . number_format($dist->total_profit, 0, ',', '.'));
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function distributeShu(Request $request, ShuDistribution $distribution): RedirectResponse
    {
        $distribution->update(['status' => 'approved']);

        $this->shuService->distributeToMembers($distribution, Auth::id());

        return redirect()->route('pengurus.reports.shu', ['year' => $distribution->year])
            ->with('success', "SHU berhasil didistribusikan kepada seluruh anggota!");
    }
}
