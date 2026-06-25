<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\LoanSchedule;
use App\Models\Organization;
use App\Models\PayrollImport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function __construct(
        private readonly AccountingService $accountingService,
        private readonly AuditService $auditService,
    ) {}

    /**
     * Generate billing data for payroll deduction this month.
     * Returns array of rows for CSV/Excel export.
     */
    public function generateBillingData(Organization $org, int $month, int $year): array
    {
        $members = User::withoutGlobalScopes()
            ->where('organization_id', $org->id)
            ->where('status', 'active')
            ->whereHas('roles', fn($q) => $q->where('name', 'anggota'))
            ->get();

        $rows = [];

        foreach ($members as $member) {
            // Simpanan Wajib — check if already paid for this period
            $simpananWajibPaid = Deposit::withoutGlobalScopes()
                ->where('organization_id', $org->id)
                ->where('user_id', $member->id)
                ->where('type', 'wajib')
                ->where('status', 'completed')
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->exists();

            $simpananWajib = $simpananWajibPaid ? 0 : $org->simpanan_wajib;

            // Active loan installment due this month
            $installment = LoanSchedule::withoutGlobalScopes()
                ->where('organization_id', $org->id)
                ->where('user_id', $member->id)
                ->where('status', 'pending')
                ->whereYear('due_date', $year)
                ->whereMonth('due_date', $month)
                ->first();

            $angsuran = $installment ? $installment->total_amount : 0;

            $rows[] = [
                'employee_id'  => $member->employee_id ?? '-',
                'name'         => $member->name,
                'email'        => $member->email,
                'department'   => $member->department ?? '-',
                'simpanan_wajib' => $simpananWajib,
                'angsuran'     => $angsuran,
                'total'        => $simpananWajib + $angsuran,
                'period'       => $month . '/' . $year,
                'user_id'      => $member->id,
                'schedule_id'  => $installment?->id,
            ];
        }

        return $rows;
    }

    /**
     * Generate CSV export content for billing
     */
    public function generateCsvExport(array $billingData, int $month, int $year): string
    {
        $monthNames = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];

        $csv  = "Dana Karya - Billing Payroll Periode {$monthNames[$month]} {$year}\n";
        $csv .= "NIK,Nama Karyawan,Departemen,Simpanan Wajib,Angsuran Pinjaman,Total Potongan,Periode\n";

        foreach ($billingData as $row) {
            $csv .= implode(',', [
                '"' . $row['employee_id'] . '"',
                '"' . $row['name'] . '"',
                '"' . $row['department'] . '"',
                $row['simpanan_wajib'],
                $row['angsuran'],
                $row['total'],
                '"' . $row['period'] . '"',
            ]) . "\n";
        }

        return $csv;
    }

    /**
     * Process a CSV file uploaded from Finance as confirmation of payroll deductions.
     * Expected columns: employee_id (or email), simpanan_wajib_paid, angsuran_paid
     */
    public function processImportFile(
        Organization $org,
        array $rows,
        int $month,
        int $year,
        int $processedBy,
        string $filePath
    ): PayrollImport {
        return DB::transaction(function () use ($org, $rows, $month, $year, $processedBy, $filePath) {
            $import = PayrollImport::create([
                'organization_id' => $org->id,
                'period_month'    => $month,
                'period_year'     => $year,
                'file_path'       => $filePath,
                'status'          => 'processing',
                'processed_by'    => $processedBy,
            ]);

            $successCount = 0;
            $failedCount  = 0;
            $totalAmount  = 0;

            foreach ($rows as $row) {
                try {
                    $member = User::withoutGlobalScopes()
                        ->where('organization_id', $org->id)
                        ->where(function ($q) use ($row) {
                            $q->where('employee_id', $row['employee_id'] ?? null)
                              ->orWhere('email', $row['email'] ?? null);
                        })
                        ->first();

                    if (!$member) {
                        $failedCount++;
                        continue;
                    }

                    // Mark simpanan wajib as paid
                    if (!empty($row['simpanan_wajib']) && $row['simpanan_wajib'] > 0) {
                        Deposit::withoutGlobalScopes()->updateOrCreate(
                            [
                                'organization_id' => $org->id,
                                'user_id'         => $member->id,
                                'type'            => 'wajib',
                                'period_month'    => $month,
                                'period_year'     => $year,
                            ],
                            [
                                'amount'           => $row['simpanan_wajib'],
                                'status'           => 'completed',
                                'transaction_type' => 'credit',
                                'notes'            => "Payroll {$month}/{$year}",
                                'processed_by'     => $processedBy,
                            ]
                        );
                        $totalAmount += $row['simpanan_wajib'];
                    }

                    // Mark loan installment as paid
                    if (!empty($row['angsuran']) && $row['angsuran'] > 0) {
                        $schedule = LoanSchedule::withoutGlobalScopes()
                            ->where('organization_id', $org->id)
                            ->where('user_id', $member->id)
                            ->where('status', 'pending')
                            ->whereYear('due_date', $year)
                            ->whereMonth('due_date', $month)
                            ->first();

                        if ($schedule) {
                            $schedule->update([
                                'status'       => 'paid',
                                'paid_amount'  => $schedule->total_amount,
                                'paid_at'      => now(),
                                'processed_by' => $processedBy,
                            ]);
                            $totalAmount += $row['angsuran'];
                        }
                    }

                    $successCount++;
                } catch (\Throwable $e) {
                    $failedCount++;
                }
            }

            $import->update([
                'status'          => 'completed',
                'processed_count' => count($rows),
                'success_count'   => $successCount,
                'failed_count'    => $failedCount,
                'total_amount'    => $totalAmount,
                'processed_at'    => now(),
            ]);

            $this->auditService->log(
                action: 'imported_payroll',
                model: 'PayrollImport',
                modelId: $import->id,
                description: "Import payroll {$month}/{$year}: {$successCount} berhasil, {$failedCount} gagal.",
                organizationId: $org->id,
                userId: $processedBy,
            );

            return $import;
        });
    }
}
