<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FinancialRuleController extends Controller
{
    public function index(): View
    {
        $org = Auth::user()->organization;
        return view('admin.rules.index', compact('org'));
    }

    public function update(Request $request): RedirectResponse
    {
        $org = Auth::user()->organization;

        $validated = $request->validate([
            'simpanan_pokok'       => ['required', 'numeric', 'min:0'],
            'simpanan_wajib'       => ['required', 'numeric', 'min:0'],
            'loan_interest_rate'   => ['required', 'numeric', 'min:0', 'max:100'],
            'loan_max_tenor'       => ['required', 'integer', 'min:1', 'max:360'],
            'loan_max_plafon'      => ['required', 'numeric', 'min:0'],
            'loan_interest_method' => ['required', 'in:flat,annuity'],
            'shu_dana_cadangan_pct'=> ['required', 'numeric', 'min:0', 'max:100'],
            'shu_anggota_pct'      => ['required', 'numeric', 'min:0', 'max:100'],
            'shu_pengurus_pct'     => ['required', 'numeric', 'min:0', 'max:100'],
            'shu_karyawan_pct'     => ['required', 'numeric', 'min:0', 'max:100'],
            'shu_pendidikan_pct'   => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        // Validate SHU percentages sum to 100
        $shuSum = $validated['shu_dana_cadangan_pct'] + $validated['shu_anggota_pct']
                + $validated['shu_pengurus_pct'] + $validated['shu_karyawan_pct']
                + $validated['shu_pendidikan_pct'];

        if (abs($shuSum - 100) > 0.01) {
            return back()->withErrors(['shu_total' => "Total alokasi SHU harus 100%. Saat ini: {$shuSum}%"])->withInput();
        }

        $org->update($validated);

        return back()->with('success', 'Aturan keuangan koperasi berhasil disimpan!');
    }
}
