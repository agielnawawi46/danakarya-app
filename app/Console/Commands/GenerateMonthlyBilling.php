<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateMonthlyBilling extends Command
{
    protected $signature   = 'billing:generate {--month= : Bulan (1-12)} {--year= : Tahun}';
    protected $description = 'Generate tagihan simpanan wajib bulanan untuk semua anggota aktif';

    public function handle(): int
    {
        $month = (int) ($this->option('month') ?? now()->month);
        $year  = (int) ($this->option('year')  ?? now()->year);

        $this->info("📋 Generate billing simpanan wajib untuk periode {$month}/{$year}...");

        $orgs = Organization::where('is_active', true)
            ->where('is_configured', true)
            ->where('simpanan_wajib', '>', 0)
            ->get();

        $totalGenerated = 0;

        foreach ($orgs as $org) {
            $members = User::withoutGlobalScopes()
                ->where('organization_id', $org->id)
                ->where('status', 'active')
                ->whereHas('roles', fn($q) => $q->where('name', 'anggota'))
                ->get();

            foreach ($members as $member) {
                // Skip if already generated for this period
                $exists = Deposit::withoutGlobalScopes()
                    ->where('organization_id', $org->id)
                    ->where('user_id', $member->id)
                    ->where('type', 'wajib')
                    ->where('period_month', $month)
                    ->where('period_year', $year)
                    ->exists();

                if ($exists) continue;

                Deposit::create([
                    'organization_id' => $org->id,
                    'user_id'         => $member->id,
                    'type'            => 'wajib',
                    'amount'          => $org->simpanan_wajib,
                    'status'          => 'pending',
                    'transaction_type'=> 'credit',
                    'period_month'    => $month,
                    'period_year'     => $year,
                    'notes'           => "Tagihan Simpanan Wajib {$month}/{$year}",
                ]);

                $totalGenerated++;
            }

            $this->line("  ✓ {$org->name}: {$members->count()} tagihan digenerate.");
        }

        $this->info("✅ Selesai. Total {$totalGenerated} tagihan dibuat.");
        return self::SUCCESS;
    }
}
