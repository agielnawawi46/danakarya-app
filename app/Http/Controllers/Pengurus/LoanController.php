<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loanService) {}

    public function index(Request $request): View
    {
        $now = now();
        $month = $request->input('month', $now->month);
        $year = $request->input('year', $now->year);

        $currentPeriod = \Carbon\Carbon::create($year, $month, 1);

        $query = Loan::with('user')
            ->whereYear('created_at', $currentPeriod->year)
            ->whereMonth('created_at', $currentPeriod->month);

        if ($request->filled('status')) $query->byStatus($request->status);
        if ($request->filled('search')) {
            // Note: Since search is now a user_id from the dropdown, we must match it
            $query->where('user_id', $request->search);
        }

        $loans = $query->latest()->paginate(20);
        
        $prevMonth = $currentPeriod->copy()->subMonth();
        $nextMonth = $currentPeriod->copy()->addMonth();
        
        $members = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'anggota'))->get();
        return view('pengurus.loans.index', compact('loans', 'members', 'currentPeriod', 'prevMonth', 'nextMonth'));
    }

    public function show(Loan $loan): View
    {
        $loan->load('user', 'schedules', 'approvedBy');
        $member = $loan->user;

        $creditInfo = $this->loanService->calculateCreditScore(
            $loan->amount,
            $loan->interest_rate,
            $loan->tenor_months,
            $member->getSalaryDecrypted(),
            $loan->organization
        );

        return view('pengurus.loans.show', compact('loan', 'member', 'creditInfo'));
    }

    public function approve(Request $request, Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->withErrors(['error' => 'Pinjaman sudah diproses.']);
        }

        // Final credit score check
        $member = $loan->user;
        $score  = $this->loanService->calculateCreditScore(
            $loan->amount, $loan->interest_rate, $loan->tenor_months, $member->getSalaryDecrypted(), $loan->organization
        );

        if (!$score['eligible']) {
            return back()->withErrors(['error' => $score['reason']]);
        }

        // Validate Cash Balance (Akun 1-101)
        $kasAccount = \App\Models\Account::where('organization_id', $loan->organization_id)
            ->where('code', '1-101')
            ->first();
            
        $kasBalance = $kasAccount ? $kasAccount->getBalance() : 0;

        if ($kasBalance < $loan->amount) {
            return back()->withErrors(['error' => 'Persetujuan ditolak! Saldo Kas Koperasi tidak mencukupi untuk dicairkan. (Tersedia: Rp ' . number_format($kasBalance, 0, ',', '.') . ')']);
        }

        $this->loanService->approveLoan($loan, Auth::id());

        return redirect()->route('pengurus.loans.index')
            ->with('success', "Pinjaman {$member->name} berhasil disetujui dan dijadwalkan!");
    }

    public function reject(Request $request, Loan $loan): RedirectResponse
    {
        $request->validate(['reason' => ['required', 'string', 'min:10']]);

        $this->loanService->rejectLoan($loan, Auth::id(), $request->reason);

        return redirect()->route('pengurus.loans.index')
            ->with('success', 'Pinjaman ditolak.');
    }

    public function payInstallment(LoanSchedule $schedule): RedirectResponse
    {
        if ($schedule->status === 'paid') {
            return back()->withErrors(['error' => 'Angsuran sudah lunas.']);
        }

        $this->loanService->payInstallment($schedule, Auth::id());

        return back()->with('success', "Angsuran ke-{$schedule->installment_number} berhasil dicatat sebagai lunas!");
    }
}
