<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loanService) {}

    public function index(): View
    {
        $loans = Loan::where('user_id', Auth::id())->with('schedules')->latest()->get();
        return view('member.loans.index', compact('loans'));
    }

    public function apply(): View
    {
        $user    = Auth::user();
        $org     = $user->organization;
        $hasLoan = $user->hasActiveLoan();

        return view('member.loans.apply', compact('user', 'org', 'hasLoan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $org  = $user->organization;

        if ($user->hasActiveLoan()) {
            return back()->withErrors(['error' => 'Anda masih memiliki pinjaman aktif.']);
        }

        $validated = $request->validate([
            'amount'  => ['required', 'numeric', 'min:100000', 'max:' . $org->loan_max_plafon],
            'tenor'   => ['required', 'integer', 'min:1', 'max:' . $org->loan_max_tenor],
            'purpose' => ['required', 'string', 'min:10'],
        ]);

        // Credit score check
        $salary = $user->getSalaryDecrypted();
        $score  = $this->loanService->calculateCreditScore(
            $validated['amount'],
            $org->loan_interest_rate,
            $validated['tenor'],
            $salary
        );

        if (!$score['eligible']) {
            return back()->withErrors(['amount' => $score['reason']])->withInput();
        }

        Loan::create([
            'organization_id' => $user->organization_id,
            'user_id'         => $user->id,
            'amount'          => $validated['amount'],
            'interest_rate'   => $org->loan_interest_rate,
            'tenor_months'    => $validated['tenor'],
            'interest_method' => $org->loan_interest_method,
            'status'          => 'pending',
            'purpose'         => $validated['purpose'],
            'credit_score'    => $score['score'],
        ]);

        return redirect()->route('member.loans.index')
            ->with('success', 'Pengajuan pinjaman berhasil dikirim. Menunggu review pengurus.');
    }

    /**
     * AJAX calculator for loan simulation
     */
    public function calculate(Request $request): JsonResponse
    {
        $user = Auth::user();
        $org  = $user->organization;

        $amount = (float) ($request->amount ?? 0);
        $tenor  = (int) ($request->tenor ?? 1);
        $rate   = $org->loan_interest_rate;
        $salary = $user->getSalaryDecrypted();

        if ($amount <= 0 || $tenor <= 0) {
            return response()->json(['error' => 'Input tidak valid.'], 422);
        }

        $score = $this->loanService->calculateCreditScore($amount, $rate, $tenor, $salary);

        // Generate schedule preview (Flat)
        $monthlyPrincipal = $amount / $tenor;
        $monthlyInterest  = $amount * ($rate / 100);
        $monthlyTotal     = $monthlyPrincipal + $monthlyInterest;
        $totalRepayment   = $monthlyTotal * $tenor;
        $totalInterest    = $monthlyInterest * $tenor;

        return response()->json([
            'monthly_installment' => $monthlyTotal,
            'total_repayment'     => $totalRepayment,
            'total_interest'      => $totalInterest,
            'credit_score'        => $score['score'],
            'eligible'            => $score['eligible'],
            'reason'              => $score['reason'],
            'max_allowed'         => $score['max_allowed'],
        ]);
    }

    public function card(Loan $loan): View
    {
        if ($loan->user_id !== Auth::id()) abort(403);
        $loan->load('schedules', 'user');
        $paidCount    = $loan->schedules->where('status', 'paid')->count();
        $pendingCount = $loan->schedules->where('status', 'pending')->count();
        $remaining    = $loan->getRemainingPrincipal();
        return view('member.loans.card', compact('loan', 'paidCount', 'pendingCount', 'remaining'));
    }
}
