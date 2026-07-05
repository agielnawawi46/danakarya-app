<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FinancialRuleController extends Controller
{
    public function __construct(private readonly AuditService $auditService) {}

    public function index(): View
    {
        $org = Auth::user()->organization;

        // Ambil riwayat perubahan aturan keuangan (20 terakhir)
        $auditLogs = AuditLog::where('organization_id', $org->id)
            ->where('action', 'updated_financial_rules')
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('admin.rules.index', compact('org', 'auditLogs'));
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
            'max_loan_salary_pct'  => ['required', 'integer', 'min:1', 'max:100'],
            'loan_interest_method' => ['required', 'in:flat,annuity'],
            'shu_dana_cadangan_pct'=> ['required', 'numeric', 'min:0', 'max:100'],
            'shu_anggota_pct'      => ['required', 'numeric', 'min:0', 'max:100'],
            'shu_pengurus_pct'     => ['required', 'numeric', 'min:0', 'max:100'],
            'shu_karyawan_pct'     => ['required', 'numeric', 'min:0', 'max:100'],
            'shu_pendidikan_pct'   => ['required', 'numeric', 'min:0', 'max:100'],
            'deposit_date'         => ['required', 'integer', 'min:1', 'max:28'],
            'payroll_date'         => ['required', 'integer', 'min:1', 'max:28'],
        ]);

        // Validate SHU percentages sum to 100
        $shuSum = $validated['shu_dana_cadangan_pct'] + $validated['shu_anggota_pct']
                + $validated['shu_pengurus_pct'] + $validated['shu_karyawan_pct']
                + $validated['shu_pendidikan_pct'];

        if (abs($shuSum - 100) > 0.01) {
            return back()->withErrors(['shu_total' => "Total alokasi SHU harus 100%. Saat ini: {$shuSum}%"])->withInput();
        }

        // Label tampilan untuk setiap field
        $fieldLabels = [
            'simpanan_pokok'       => 'Simpanan Pokok',
            'simpanan_wajib'       => 'Simpanan Wajib/Bulan',
            'loan_interest_rate'   => 'Bunga Pinjaman (%)',
            'loan_max_tenor'       => 'Tenor Maksimum (bln)',
            'loan_max_plafon'      => 'Plafon Maksimum',
            'max_loan_salary_pct'  => 'Batas Angsuran/Gaji (%)',
            'loan_interest_method' => 'Metode Bunga',
            'shu_dana_cadangan_pct'=> 'SHU Dana Cadangan (%)',
            'shu_anggota_pct'      => 'SHU Bagian Anggota (%)',
            'shu_pengurus_pct'     => 'SHU Pengurus (%)',
            'shu_karyawan_pct'     => 'SHU Karyawan (%)',
            'shu_pendidikan_pct'   => 'SHU Pendidikan (%)',
            'deposit_date'         => 'Tanggal Tagihan',
            'payroll_date'         => 'Tanggal Payroll',
        ];

        // Deteksi field yang benar-benar berubah sebelum update
        $oldValues    = [];
        $newValues    = [];
        $changedFields = [];

        foreach ($validated as $key => $newVal) {
            $oldVal = $org->$key;
            if ((string) $oldVal !== (string) $newVal) {
                $label = $fieldLabels[$key] ?? $key;
                $oldValues[$label] = $oldVal;
                $newValues[$label] = $newVal;
                $changedFields[]   = $label;
            }
        }

        $org->update($validated);

        // Catat ke audit trail hanya jika ada field yang berubah
        if (!empty($changedFields)) {
            $this->auditService->log(
                action: 'updated_financial_rules',
                description: 'Aturan keuangan diubah: ' . implode(', ', $changedFields),
                model: 'Organization',
                modelId: $org->id,
                oldValues: $oldValues,
                newValues: $newValues,
                organizationId: $org->id,
            );
        }

        return back()->with('success', 'Aturan keuangan koperasi berhasil disimpan!');
    }
}
