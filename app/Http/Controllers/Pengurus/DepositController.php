<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function __construct(
        private readonly AccountingService $accountingService,
        private readonly AuditService $auditService,
    ) {}

    public function index(Request $request): View
    {
        $query = Deposit::with('user')
            ->where('transaction_type', 'credit')
            ->whereIn('type', ['sukarela', 'pokok', 'wajib']);

        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $deposits = $query->latest()->paginate(20);
        return view('pengurus.deposits.index', compact('deposits'));
    }

    public function create(): View
    {
        $members = User::withoutGlobalScopes()
            ->where('organization_id', Auth::user()->organization_id)
            ->whereHas('roles', fn($q) => $q->where('name', 'anggota'))
            ->get();
        return view('pengurus.deposits.create', compact('members'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'type'    => ['required', 'in:sukarela,pokok,wajib'],
            'amount'  => ['required', 'numeric', 'min:1000'],
            'notes'   => ['nullable', 'string'],
        ]);

        $org    = Auth::user()->organization;
        $member = User::withoutGlobalScopes()->findOrFail($validated['user_id']);

        // Validate simpanan pokok uniqueness
        if ($validated['type'] === 'pokok') {
            $exists = Deposit::withoutGlobalScopes()
                ->where('user_id', $validated['user_id'])
                ->where('type', 'pokok')
                ->where('status', 'completed')
                ->exists();
            if ($exists) {
                return back()->withErrors(['type' => 'Anggota sudah memiliki simpanan pokok.'])->withInput();
            }
        }

        $deposit = Deposit::create([
            'organization_id' => $org->id,
            'user_id'         => $validated['user_id'],
            'type'            => $validated['type'],
            'amount'          => $validated['amount'],
            'status'          => 'completed',
            'transaction_type'=> 'credit',
            'notes'           => $validated['notes'],
            'processed_by'    => Auth::id(),
        ]);

        // Auto-journal
        $this->accountingService->journalDeposit($validated['amount'], $org->id, Auth::id(), $member->name);
        $this->auditService->log('processed_deposit', "Setoran {$validated['type']} Rp " . number_format($validated['amount']) . " untuk {$member->name}", 'Deposit', $deposit->id);

        return redirect()->route('pengurus.deposits.index')
            ->with('success', 'Setoran berhasil dicatat!');
    }

    public function withdrawals(Request $request): View
    {
        $withdrawals = Deposit::with('user')
            ->where('transaction_type', 'debit')
            ->where('type', 'sukarela')
            ->latest()
            ->paginate(20);
        return view('pengurus.deposits.withdrawals', compact('withdrawals'));
    }

    public function approve(Deposit $deposit): RedirectResponse
    {
        if ($deposit->transaction_type !== 'debit' || $deposit->status !== 'pending') {
            return back()->withErrors(['error' => 'Permintaan tidak valid.']);
        }

        $member  = $deposit->user;
        $balance = $member->getTotalSimpananByType('sukarela');

        if ($deposit->amount > $balance) {
            return back()->withErrors(['error' => "Saldo tidak mencukupi. Saldo sukarela: Rp " . number_format($balance, 0, ',', '.')]);
        }

        $deposit->update(['status' => 'completed', 'processed_by' => Auth::id()]);

        $this->accountingService->journalWithdrawal($deposit->amount, $deposit->organization_id, Auth::id(), $member->name);
        $this->auditService->log('processed_withdrawal', "Penarikan sukarela disetujui untuk {$member->name}", 'Deposit', $deposit->id);

        return back()->with('success', 'Penarikan berhasil disetujui!');
    }

    public function reject(Deposit $deposit): RedirectResponse
    {
        $deposit->update(['status' => 'rejected', 'processed_by' => Auth::id()]);
        return back()->with('success', 'Penarikan ditolak.');
    }
}
