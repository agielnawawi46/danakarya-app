<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\LoanSchedule;
use App\Models\Organization;
use App\Models\ShuDistribution;
use App\Models\ShuMemberDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShuService
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    /**
     * Calculate SHU for an organization for a given year.
     * Creates a draft ShuDistribution record.
     */
    public function calculateAnnualShu(Organization $org, int $year, float $totalIncome, float $totalExpense): ShuDistribution
    {
        return DB::transaction(function () use ($org, $year, $totalIncome, $totalExpense) {
            $totalProfit = $totalIncome - $totalExpense;

            if ($totalProfit <= 0) {
                throw new \RuntimeException('Tidak ada SHU untuk dibagikan: total keuntungan ≤ 0.');
            }

            // Allocate based on percentages from org settings
            $danaCadangan = $totalProfit * ($org->shu_dana_cadangan_pct / 100);
            $bagianAnggota= $totalProfit * ($org->shu_anggota_pct / 100);
            $bagianPengurus= $totalProfit * ($org->shu_pengurus_pct / 100);
            $bagianKaryawan= $totalProfit * ($org->shu_karyawan_pct / 100);
            $bagianPendidikan = $totalProfit * ($org->shu_pendidikan_pct / 100);

            // Jasa Modal = 60% of bagianAnggota, Jasa Pinjaman = 40%
            $totalJasaModal   = $bagianAnggota * 0.60;
            $totalJasaPinjaman= $bagianAnggota * 0.40;

            // Remove existing draft for this year if any
            ShuDistribution::withoutGlobalScopes()
                ->where('organization_id', $org->id)
                ->where('year', $year)
                ->where('status', 'draft')
                ->delete();

            $distribution = ShuDistribution::create([
                'organization_id'   => $org->id,
                'year'              => $year,
                'total_profit'      => $totalProfit,
                'total_dana_cadangan' => $danaCadangan,
                'total_anggota'     => $bagianAnggota,
                'total_pengurus'    => $bagianPengurus,
                'total_karyawan'    => $bagianKaryawan,
                'total_pendidikan'  => $bagianPendidikan,
                'total_jasa_modal'  => $totalJasaModal,
                'total_jasa_pinjaman' => $totalJasaPinjaman,
                'status'            => 'draft',
            ]);

            // Calculate per-member allocation
            $this->calculateMemberAllocations($distribution, $year, $totalJasaModal, $totalJasaPinjaman);

            return $distribution;
        });
    }

    private function calculateMemberAllocations(
        ShuDistribution $distribution,
        int $year,
        float $totalJasaModal,
        float $totalJasaPinjaman
    ): void {
        $orgId   = $distribution->organization_id;
        $members = User::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->whereHas('roles', fn($q) => $q->where('name', 'anggota'))
            ->get();

        // Total base values across all members
        $grandTotalSimpanan = 0;
        $grandTotalBunga    = 0;
        $memberData         = [];

        foreach ($members as $member) {
            $totalSimpanan = (float) Deposit::withoutGlobalScopes()
                ->where('organization_id', $orgId)
                ->where('user_id', $member->id)
                ->where('status', 'completed')
                ->where('transaction_type', 'credit')
                ->whereYear('created_at', $year)
                ->sum('amount');

            $totalBungaPaid = (float) LoanSchedule::withoutGlobalScopes()
                ->where('organization_id', $orgId)
                ->where('user_id', $member->id)
                ->where('status', 'paid')
                ->whereYear('paid_at', $year)
                ->sum('interest_amount');

            $grandTotalSimpanan += $totalSimpanan;
            $grandTotalBunga    += $totalBungaPaid;

            $memberData[$member->id] = [
                'user'           => $member,
                'total_simpanan' => $totalSimpanan,
                'total_bunga'    => $totalBungaPaid,
            ];
        }

        // Distribute to each member
        foreach ($memberData as $memberId => $data) {
            $jasaModal = $grandTotalSimpanan > 0
                ? ($data['total_simpanan'] / $grandTotalSimpanan) * $totalJasaModal
                : 0;

            $jasaPinjaman = $grandTotalBunga > 0
                ? ($data['total_bunga'] / $grandTotalBunga) * $totalJasaPinjaman
                : 0;

            ShuMemberDetail::withoutGlobalScopes()->updateOrCreate(
                ['shu_distribution_id' => $distribution->id, 'user_id' => $memberId],
                [
                    'organization_id' => $distribution->organization_id,
                    'total_simpanan'  => $data['total_simpanan'],
                    'total_bunga_paid'=> $data['total_bunga'],
                    'jasa_modal'      => round($jasaModal, 2),
                    'jasa_pinjaman'   => round($jasaPinjaman, 2),
                    'total_shu'       => round($jasaModal + $jasaPinjaman, 2),
                ]
            );
        }
    }

    /**
     * Distribute SHU to member savings accounts (Simpanan Sukarela)
     */
    public function distributeToMembers(ShuDistribution $distribution, int $approvedBy): ShuDistribution
    {
        return DB::transaction(function () use ($distribution, $approvedBy) {
            if ($distribution->status !== 'approved') {
                throw new \RuntimeException('SHU harus disetujui sebelum didistribusikan.');
            }

            $details = ShuMemberDetail::withoutGlobalScopes()
                ->where('shu_distribution_id', $distribution->id)
                ->with('user')
                ->get();

            foreach ($details as $detail) {
                if ($detail->total_shu <= 0) continue;

                // Create deposit (simpanan sukarela) for this member
                $deposit = Deposit::create([
                    'organization_id' => $distribution->organization_id,
                    'user_id'         => $detail->user_id,
                    'type'            => 'sukarela',
                    'amount'          => $detail->total_shu,
                    'status'          => 'completed',
                    'transaction_type'=> 'credit',
                    'notes'           => "SHU Tahun {$distribution->year}",
                    'processed_by'    => $approvedBy,
                ]);

                $detail->update([
                    'deposit_id'   => $deposit->id,
                    'deposited_at' => now(),
                ]);
            }

            $distribution->update([
                'status'         => 'distributed',
                'approved_by'    => $approvedBy,
                'distributed_at' => now(),
            ]);

            $this->auditService->log(
                action: 'distributed_shu',
                model: 'ShuDistribution',
                modelId: $distribution->id,
                description: "SHU tahun {$distribution->year} telah didistribusikan kepada " . $details->count() . " anggota.",
                organizationId: $distribution->organization_id,
                userId: $approvedBy,
            );

            return $distribution->fresh();
        });
    }
}
