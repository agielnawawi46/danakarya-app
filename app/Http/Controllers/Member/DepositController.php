<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $transactions = Deposit::where('user_id', $user->id)->latest()->paginate(20);

        $balances = [
            'pokok'    => $user->getTotalSimpananByType('pokok'),
            'wajib'    => $user->getTotalSimpananByType('wajib'),
            'sukarela' => $user->getTotalSimpananByType('sukarela'),
        ];

        return view('member.deposits.index', compact('transactions', 'balances'));
    }

    public function showWithdraw(): View
    {
        $user    = Auth::user();
        $balance = $user->getTotalSimpananByType('sukarela');
        return view('member.deposits.withdraw', compact('balance'));
    }

    public function storeWithdraw(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'amount' => ['required', 'numeric', 'min:10000'],
            'notes'  => ['nullable', 'string'],
        ]);

        $balance = $user->getTotalSimpananByType('sukarela');

        if ($request->amount > $balance) {
            return back()->withErrors(['amount' => "Saldo sukarela tidak mencukupi. Saldo Anda: Rp " . number_format($balance, 0, ',', '.')]);
        }

        // Check for pending withdrawal request
        $hasPending = Deposit::where('user_id', $user->id)
            ->where('type', 'sukarela')
            ->where('transaction_type', 'debit')
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->withErrors(['amount' => 'Anda sudah memiliki permintaan penarikan yang sedang menunggu persetujuan.']);
        }

        Deposit::create([
            'organization_id' => $user->organization_id,
            'user_id'         => $user->id,
            'type'            => 'sukarela',
            'amount'          => $request->amount,
            'status'          => 'pending',
            'transaction_type'=> 'debit',
            'notes'           => $request->notes ?? 'Permintaan penarikan simpanan sukarela',
        ]);

        return redirect()->route('member.deposits.index')
            ->with('success', 'Permintaan penarikan berhasil diajukan. Menunggu persetujuan pengurus.');
    }
}
