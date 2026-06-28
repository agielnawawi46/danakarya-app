<?php

namespace Database\Seeders;

use App\Models\Deposit;
use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Models\Organization;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\LoanService;
use App\Services\AuditService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $accountingService = app(AccountingService::class);
        $loanService       = app(LoanService::class);

        // ── Create Demo Organization ────────────────────────────────────────
        $org = Organization::create([
            'name'                => 'KSP Sejahtera Bersama',
            'legal_name'          => 'Koperasi Simpan Pinjam Sejahtera Bersama',
            'address'             => 'Jl. Sudirman No. 1, Jakarta Pusat',
            'phone'               => '021-12345678',
            'email'               => 'admin@ksp-sejahtera.co.id',
            'legal_number'        => '001234/BH/KOP/2020',
            'is_configured'       => true,
            'is_active'           => true,
            'simpanan_pokok'      => 500000,
            'simpanan_wajib'      => 100000,
            'loan_interest_rate'  => 1.5,
            'loan_max_tenor'      => 24,
            'loan_max_plafon'     => 50000000,
            'loan_interest_method'=> 'flat',
            'shu_dana_cadangan_pct'=> 40,
            'shu_anggota_pct'     => 40,
            'shu_pengurus_pct'    => 5,
            'shu_karyawan_pct'    => 5,
            'shu_pendidikan_pct'  => 10,
        ]);

        // Seed default COA
        $accountingService->seedDefaultCoa($org->id);

        // ── Create Admin ────────────────────────────────────────────────────
        $admin = User::create([
            'name'            => 'Ahmad Fauzan',
            'email'           => 'admin@demo.danakarya.id',
            'password'        => bcrypt('Demo@123!'),
            'organization_id' => $org->id,
            'status'          => 'active',
        ]);
        $admin->assignRole('admin');

        // ── Create Pengurus ─────────────────────────────────────────────────
        $pengurus = User::create([
            'name'            => 'Siti Rahayu',
            'email'           => 'pengurus@demo.danakarya.id',
            'password'        => bcrypt('Demo@123!'),
            'organization_id' => $org->id,
            'status'          => 'active',
        ]);
        $pengurus->assignRole('pengurus');

        // ── Create Pengawas ─────────────────────────────────────────────────
        $pengawas = User::create([
            'name'            => 'Budi Santoso',
            'email'           => 'pengawas@demo.danakarya.id',
            'password'        => bcrypt('Demo@123!'),
            'organization_id' => $org->id,
            'status'          => 'active',
        ]);
        $pengawas->assignRole('pengawas');

        // ── Create Members ──────────────────────────────────────────────────
        $memberData = [
            ['name' => 'Rina Kusuma',    'salary' => 8000000,  'dept' => 'Marketing'],
            ['name' => 'Doni Prasetyo',  'salary' => 6500000,  'dept' => 'Finance'],
            ['name' => 'Lisa Andiani',   'salary' => 9000000,  'dept' => 'HR'],
            ['name' => 'Hendra Wijaya',  'salary' => 7500000,  'dept' => 'IT'],
            ['name' => 'Maya Putri',     'salary' => 5500000,  'dept' => 'Admin'],
        ];

        $members = [];
        foreach ($memberData as $i => $data) {
            $email = strtolower(str_replace(' ', '.', $data['name'])) . '@demo.danakarya.id';
            $member = User::create([
                'name'            => $data['name'],
                'email'           => $email,
                'password'        => bcrypt('Demo@123!'),
                'organization_id' => $org->id,
                'employee_id'     => 'EMP-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'department'      => $data['dept'],
                'salary'          => $data['salary'],
                'join_date'       => now()->subYears(2)->toDateString(),
                'status'          => 'active',
            ]);
            $member->assignRole('anggota');
            $members[] = $member;

            // Simpanan Pokok
            Deposit::create([
                'organization_id' => $org->id,
                'user_id'         => $member->id,
                'type'            => 'pokok',
                'amount'          => $org->simpanan_pokok,
                'status'          => 'completed',
                'transaction_type'=> 'credit',
                'notes'           => 'Simpanan Pokok Awal',
                'processed_by'    => $pengurus->id,
            ]);

            // Simpanan Wajib (last 6 months)
            for ($m = 6; $m >= 1; $m--) {
                $date = now()->subMonths($m);
                Deposit::create([
                    'organization_id' => $org->id,
                    'user_id'         => $member->id,
                    'type'            => 'wajib',
                    'amount'          => $org->simpanan_wajib,
                    'status'          => 'completed',
                    'transaction_type'=> 'credit',
                    'period_month'    => $date->month,
                    'period_year'     => $date->year,
                    'processed_by'    => $pengurus->id,
                    'created_at'      => $date,
                    'updated_at'      => $date,
                ]);
            }
        }

        // Demo active loan for first member
        $borrower = $members[0];
        $loan = Loan::create([
            'organization_id' => $org->id,
            'user_id'         => $borrower->id,
            'amount'          => 5000000,
            'interest_rate'   => 1.5,
            'tenor_months'    => 12,
            'interest_method' => 'flat',
            'status'          => 'active',
            'purpose'         => 'Kebutuhan Rumah Tangga',
            'approved_by'     => $pengurus->id,
            'approved_at'     => now()->subMonths(3),
            'disbursed_at'    => now()->subMonths(3),
            'credit_score'    => 11.9,
        ]);

        $loanService->generateSchedule($loan);

        // Mark first 3 installments as paid
        LoanSchedule::withoutGlobalScopes()
            ->where('loan_id', $loan->id)
            ->where('installment_number', '<=', 3)
            ->update(['status' => 'paid', 'paid_amount' => DB::raw('total_amount'), 'paid_at' => now()->subMonth()]);

        $this->command->info('✅ Demo organization & users seeded!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Superadmin', 'superadmin@danakarya.id', 'SuperAdmin@2024!'],
                ['Admin',      'admin@demo.danakarya.id', 'Demo@123!'],
                ['Pengurus',   'pengurus@demo.danakarya.id', 'Demo@123!'],
                ['Pengawas',   'pengawas@demo.danakarya.id', 'Demo@123!'],
                ['Anggota',    'rina.kusuma@demo.danakarya.id', 'Rina@123!'],
            ]
        );
    }
}
