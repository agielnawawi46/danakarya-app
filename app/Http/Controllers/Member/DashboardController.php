<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\LoanSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $simpananPokok    = $user->getTotalSimpananByType('pokok');
        $simpananWajib    = $user->getTotalSimpananByType('wajib');
        $simpananSukarela = $user->getTotalSimpananByType('sukarela');
        $totalSimpanan    = $simpananPokok + $simpananWajib + $simpananSukarela;

        $activeLoan = Loan::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('schedules')
            ->first();

        $nextInstallment = null;
        if ($activeLoan) {
            $nextInstallment = LoanSchedule::where('loan_id', $activeLoan->id)
                ->where('status', 'pending')
                ->orderBy('installment_number')
                ->first();
        }

        $recentTransactions = Deposit::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('member.dashboard', compact(
            'user', 'simpananPokok', 'simpananWajib', 'simpananSukarela', 'totalSimpanan',
            'activeLoan', 'nextInstallment', 'recentTransactions'
        ));
    }
}
