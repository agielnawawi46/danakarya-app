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
        $query = Loan::with('user');
        if ($request->filled('status')) $query->byStatus($request->status);
        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $loans = $query->latest()->paginate(20);
        return view('pengurus.loans.index', compact('loans'));
    }

    public function show(Loan $loan): View
    {
        $loan->load('user', 'schedules', 'approvedBy');
        $member = $loan->user;

        $creditInfo = $this->loanService->calculateCreditScore(
            $loan->amount,
            $loan->interest_rate,
            $loan->tenor_months,
            $member->getSalaryDecrypted()
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
            $loan->amount, $loan->interest_rate, $loan->tenor_months, $member->getSalaryDecrypted()
        );

        if (!$score['eligible']) {
            return back()->withErrors(['error' => $score['reason']]);
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
