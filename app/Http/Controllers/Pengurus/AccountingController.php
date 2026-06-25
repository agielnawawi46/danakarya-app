<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountingController extends Controller
{
    public function __construct(private readonly AccountingService $accountingService) {}

    public function index(): View
    {
        return view('pengurus.accounting.index');
    }

    public function coa(): View
    {
        $accounts = Account::orderBy('code')->get();
        return view('pengurus.accounting.coa', compact('accounts'));
    }

    public function journals(Request $request): View
    {
        $query = JournalEntry::with('creator', 'lines.account')->latest('date');
        if ($request->filled('from')) $query->whereDate('date', '>=', $request->from);
        if ($request->filled('to'))   $query->whereDate('date', '<=', $request->to);

        $journals = $query->paginate(25);
        return view('pengurus.accounting.journals', compact('journals'));
    }

    public function createJournal(): View
    {
        $accounts = Account::orderBy('code')->get();
        return view('pengurus.accounting.create-journal', compact('accounts'));
    }

    public function storeJournal(Request $request): RedirectResponse
    {
        $request->validate([
            'description'         => ['required', 'string'],
            'date'                => ['required', 'date'],
            'lines'               => ['required', 'array', 'min:2'],
            'lines.*.account_code'=> ['required', 'string'],
            'lines.*.debit'       => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit'      => ['nullable', 'numeric', 'min:0'],
            'lines.*.description' => ['nullable', 'string'],
        ]);

        $lines = array_map(fn($line) => [
            'account_code'=> $line['account_code'],
            'debit'       => (float) ($line['debit'] ?? 0),
            'credit'      => (float) ($line['credit'] ?? 0),
            'description' => $line['description'] ?? null,
        ], $request->lines);

        try {
            $this->accountingService->createJournal([
                'description' => $request->description,
                'date'        => $request->date,
                'source_type' => 'manual',
                'lines'       => $lines,
            ], Auth::user()->organization_id, Auth::id());

            return redirect()->route('pengurus.accounting.journals')
                ->with('success', 'Jurnal berhasil disimpan!');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
