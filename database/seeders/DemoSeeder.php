<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Deposit;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Models\Organization;
use App\Models\ShuDistribution;
use App\Models\ShuMemberDetail;
use App\Models\User;
use App\Services\AccountingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $accountingService = app(AccountingService::class);

        // ── Tanggal: Koperasi berdiri 1 Jan 2025, demo sampai hari ini Juli 2026 ──
        $startDate   = Carbon::create(2025, 1, 1);
        $currentDate = Carbon::create(2026, 7, 1); // Bulan berjalan demo
        $endYear2025 = Carbon::create(2025, 12, 31);

        // ─────────────────────────────────────────────────────────────────────────
        // 1. BUAT ORGANISASI (KOPERASI)
        // ─────────────────────────────────────────────────────────────────────────
        $org = Organization::create([
            'name'                  => 'KSP Mitra Sejahtera',
            'legal_name'            => 'Koperasi Simpan Pinjam Mitra Sejahtera',
            'address'               => 'Jl. Gatot Subroto No. 45, Bekasi Selatan',
            'phone'                 => '021-88887777',
            'email'                 => 'admin@ksp-mitrasejahtera.co.id',
            'legal_number'          => '002567/BH/KOP/2024',
            'is_configured'         => true,
            'is_active'             => true,
            'simpanan_pokok'        => 500000,
            'simpanan_wajib'        => 150000,
            'loan_interest_rate'    => 1.5,
            'loan_max_tenor'        => 24,
            'loan_max_plafon'       => 15000000,
            'loan_interest_method'  => 'flat',
            'max_loan_salary_pct'   => 30,
            'deposit_date'          => 1,
            'payroll_date'          => 25,
            // Porsi SHU: 30% cadangan, 45% anggota, 10% pengurus, 5% karyawan, 10% pendidikan
            'shu_dana_cadangan_pct' => 30,
            'shu_anggota_pct'       => 45,
            'shu_pengurus_pct'      => 10,
            'shu_karyawan_pct'      => 5,
            'shu_pendidikan_pct'    => 10,
        ]);

        // Seed COA (Chart of Accounts default)
        $accountingService->seedDefaultCoa($org->id);

        // ─────────────────────────────────────────────────────────────────────────
        // 2. BUAT PENGGUNA (Admin, Pengurus, Pengawas, Anggota)
        // ─────────────────────────────────────────────────────────────────────────

        // Admin
        $admin = User::create([
            'name'            => 'Firmansyah Adi',
            'email'           => 'admin@demo.danakarya.id',
            'password'        => bcrypt('Demo@123!'),
            'organization_id' => $org->id,
            'status'          => 'active',
        ]);
        $admin->assignRole('admin');

        // Pengurus
        $pengurus = User::create([
            'name'            => 'Sri Mulyani',
            'email'           => 'pengurus@demo.danakarya.id',
            'password'        => bcrypt('Demo@123!'),
            'organization_id' => $org->id,
            'status'          => 'active',
        ]);
        $pengurus->assignRole('pengurus');

        // Pengawas
        $pengawas = User::create([
            'name'            => 'Agus Salim',
            'email'           => 'pengawas@demo.danakarya.id',
            'password'        => bcrypt('Demo@123!'),
            'organization_id' => $org->id,
            'status'          => 'active',
        ]);
        $pengawas->assignRole('pengawas');

        // 7 Anggota dengan profil berbeda-beda (agar terasa nyata)
        $memberData = [
            // Anggota aktif meminjam (pinjaman selesai 2025)
            ['name' => 'Rina Kusuma',     'salary' => 8500000,  'dept' => 'Marketing',  'email' => 'rina.kusuma@demo.danakarya.id'],
            // Anggota aktif meminjam saat ini (2026)
            ['name' => 'Doni Prasetyo',   'salary' => 7000000,  'dept' => 'Finance',    'email' => 'doni.prasetyo@demo.danakarya.id'],
            // Anggota pinjaman sedang berjalan (2026)
            ['name' => 'Lisa Andiani',    'salary' => 9500000,  'dept' => 'HR',         'email' => 'lisa.andiani@demo.danakarya.id'],
            // Anggota pengajuan pending
            ['name' => 'Hendra Wijaya',   'salary' => 8000000,  'dept' => 'IT',         'email' => 'hendra.wijaya@demo.danakarya.id'],
            // Anggota murni penabung (tidak pernah pinjam)
            ['name' => 'Maya Putri',      'salary' => 6000000,  'dept' => 'Admin',      'email' => 'maya.putri@demo.danakarya.id'],
            // Anggota lain
            ['name' => 'Reza Firmansyah', 'salary' => 7500000,  'dept' => 'Operations', 'email' => 'reza.firmansyah@demo.danakarya.id'],
            ['name' => 'Dewi Lestari',    'salary' => 6500000,  'dept' => 'Marketing',  'email' => 'dewi.lestari@demo.danakarya.id'],
        ];

        $members = [];
        foreach ($memberData as $i => $data) {
            $member = User::create([
                'name'            => $data['name'],
                'email'           => $data['email'],
                'password'        => bcrypt('Demo@123!'),
                'organization_id' => $org->id,
                'employee_id'     => 'EMP-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'department'      => $data['dept'],
                'salary'          => $data['salary'],
                'join_date'       => $startDate->toDateString(),
                'status'          => 'active',
            ]);
            $member->assignRole('anggota');
            $members[] = $member;
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 3. AMBIL AKUN-AKUN COA
        // ─────────────────────────────────────────────────────────────────────────
        $kasAcc       = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '1-101')->first();
        $piutangAcc   = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '1-201')->first();
        $modalAcc     = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '3-101')->first();
        $pokAcc       = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '2-101')->first();
        $wajibAcc     = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '2-202')->first();
        $pendapatanAcc= Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '4-101')->first();
        $cadanganAcc  = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '3-201')->first();
        $bebanAdminAcc= Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '5-101')->first();

        // ─────────────────────────────────────────────────────────────────────────
        // 4. MODAL AWAL & SIMPANAN POKOK (Januari 2025)
        // ─────────────────────────────────────────────────────────────────────────

        // Jurnal modal awal operasional koperasi
        $jModal = JournalEntry::create([
            'organization_id' => $org->id, 'reference' => 'JU-202501-0001',
            'description' => 'Modal Awal Operasional Koperasi',
            'date' => $startDate->toDateString(), 'source_type' => 'manual',
            'created_by' => $pengurus->id, 'created_at' => $startDate, 'updated_at' => $startDate,
        ]);
        if ($kasAcc && $modalAcc) {
            JournalEntryLine::create(['journal_entry_id' => $jModal->id, 'account_id' => $kasAcc->id,   'description' => 'Kas Modal Awal Koperasi', 'debit' => 5000000, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jModal->id, 'account_id' => $modalAcc->id, 'description' => 'Modal Koperasi',          'debit' => 0,       'credit' => 5000000]);
        }

        // Simpanan Pokok semua anggota (Januari 2025) + jurnalnya
        $totalPokok = $org->simpanan_pokok * count($members); // 500.000 x 7 = 3.500.000
        foreach ($members as $member) {
            Deposit::create([
                'organization_id' => $org->id, 'user_id' => $member->id, 'type' => 'pokok',
                'amount' => $org->simpanan_pokok, 'status' => 'completed', 'transaction_type' => 'credit',
                'notes' => 'Simpanan Pokok Awal Masuk Koperasi', 'processed_by' => $admin->id,
                'created_at' => $startDate, 'updated_at' => $startDate,
            ]);
        }
        if ($kasAcc && $pokAcc) {
            $jPokok = JournalEntry::create([
                'organization_id' => $org->id, 'reference' => 'JU-202501-0002',
                'description' => 'Penerimaan Simpanan Pokok – 7 Anggota Pendiri',
                'date' => $startDate->toDateString(), 'source_type' => 'deposit',
                'created_by' => $admin->id, 'created_at' => $startDate, 'updated_at' => $startDate,
            ]);
            JournalEntryLine::create(['journal_entry_id' => $jPokok->id, 'account_id' => $kasAcc->id, 'description' => 'Kas Masuk Simpanan Pokok', 'debit' => $totalPokok, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jPokok->id, 'account_id' => $pokAcc->id, 'description' => 'Simpanan Pokok Anggota',   'debit' => 0,           'credit' => $totalPokok]);
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 5. SIMPANAN WAJIB BULANAN (Jan 2025 – Jun 2026 = 18 bulan)
        //    Semua anggota membayar rutin setiap bulan via payroll
        // ─────────────────────────────────────────────────────────────────────────
        $totalBulanWajib = 18; // Jan 2025 - Jun 2026
        for ($m = 0; $m < $totalBulanWajib; $m++) {
            $periodeDate = $startDate->copy()->addMonths($m)->startOfMonth();
            foreach ($members as $member) {
                Deposit::create([
                    'organization_id' => $org->id, 'user_id' => $member->id, 'type' => 'wajib',
                    'amount' => $org->simpanan_wajib, 'status' => 'completed', 'transaction_type' => 'credit',
                    'period_month' => $periodeDate->month, 'period_year' => $periodeDate->year,
                    'notes' => 'Potongan Payroll ' . $periodeDate->format('M Y'),
                    'processed_by' => $admin->id,
                    'created_at' => $periodeDate, 'updated_at' => $periodeDate,
                ]);
            }
            // Jurnal bulanan simpanan wajib
            if ($kasAcc && $wajibAcc) {
                $totalWajib = $org->simpanan_wajib * count($members);
                $jWajib = JournalEntry::create([
                    'organization_id' => $org->id,
                    'reference'       => 'JU-' . $periodeDate->format('Ym') . '-WJB',
                    'description'     => 'Simpanan Wajib Payroll – ' . $periodeDate->format('M Y'),
                    'date'            => $periodeDate->toDateString(), 'source_type' => 'payroll',
                    'created_by'      => $admin->id, 'created_at' => $periodeDate, 'updated_at' => $periodeDate,
                ]);
                JournalEntryLine::create(['journal_entry_id' => $jWajib->id, 'account_id' => $kasAcc->id,   'description' => 'Kas Masuk Payroll', 'debit' => $totalWajib, 'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jWajib->id, 'account_id' => $wajibAcc->id, 'description' => 'Simpanan Wajib',    'debit' => 0,           'credit' => $totalWajib]);
            }
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 6. BEBAN OPERASIONAL BULANAN (Jan 2025 – Jun 2026)
        //    Rapat pengurus, ATK, dll — agar laporan Laba/Rugi tidak kosong
        // ─────────────────────────────────────────────────────────────────────────
        for ($m = 0; $m < $totalBulanWajib; $m++) {
            $periodeDate = $startDate->copy()->addMonths($m)->startOfMonth()->addDays(14);
            if ($kasAcc && $bebanAdminAcc) {
                $jBeban = JournalEntry::create([
                    'organization_id' => $org->id,
                    'reference'       => 'JU-' . $periodeDate->format('Ym') . '-OPS',
                    'description'     => 'Biaya Operasional Rutin – ' . $periodeDate->format('M Y'),
                    'date'            => $periodeDate->toDateString(), 'source_type' => 'manual',
                    'created_by'      => $pengurus->id, 'created_at' => $periodeDate, 'updated_at' => $periodeDate,
                ]);
                JournalEntryLine::create(['journal_entry_id' => $jBeban->id, 'account_id' => $bebanAdminAcc->id, 'description' => 'Biaya Operasional (Rapat, ATK)', 'debit' => 300000, 'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jBeban->id, 'account_id' => $kasAcc->id,        'description' => 'Kas Keluar Operasional',          'debit' => 0,      'credit' => 300000]);
            }
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 7. PINJAMAN #1 – RINA KUSUMA: LUNAS (Feb 2025 – Nov 2025, 10 bulan)
        //    Pinjam Rp 5.000.000 untuk kebutuhan rumah tangga, sudah LUNAS
        // ─────────────────────────────────────────────────────────────────────────
        $loan1Start = Carbon::create(2025, 2, 1);
        $loan1 = Loan::create([
            'organization_id' => $org->id, 'user_id' => $members[0]->id,
            'amount' => 5000000, 'interest_rate' => 1.5, 'tenor_months' => 10,
            'interest_method' => 'flat', 'status' => 'completed',
            'purpose' => 'Kebutuhan Rumah Tangga', 'credit_score' => 11.9,
            'approved_by' => $pengurus->id, 'approved_at' => $loan1Start,
            'disbursed_at' => $loan1Start,
            'created_at' => $loan1Start, 'updated_at' => $loan1Start,
        ]);
        // Jurnal pencairan
        if ($kasAcc && $piutangAcc) {
            $jLoan1 = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-202502-LN01', 'description' => 'Pencairan Pinjaman – Rina Kusuma', 'date' => $loan1Start->toDateString(), 'source_type' => 'loan', 'created_by' => $pengurus->id, 'created_at' => $loan1Start, 'updated_at' => $loan1Start]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan1->id, 'account_id' => $piutangAcc->id, 'description' => 'Piutang Pinjaman Rina', 'debit' => 5000000, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan1->id, 'account_id' => $kasAcc->id,     'description' => 'Kas Keluar Pencairan',   'debit' => 0,       'credit' => 5000000]);
        }
        // 10 angsuran, semua lunas
        $p1 = floor((5000000 / 10) / 1000) * 1000;
        $i1 = floor((5000000 * 0.015));
        for ($s = 1; $s <= 10; $s++) {
            $dueDate = $loan1Start->copy()->addMonths($s - 1);
            $isPrincipal = ($s === 10) ? (5000000 - $p1 * 9) : $p1;
            $isInterest  = $i1;
            $total = $isPrincipal + $isInterest;
            LoanSchedule::create([
                'loan_id' => $loan1->id, 'organization_id' => $org->id, 'user_id' => $members[0]->id,
                'installment_number' => $s, 'due_date' => $dueDate->toDateString(),
                'principal_amount' => $isPrincipal, 'interest_amount' => $isInterest, 'total_amount' => $total,
                'remaining_balance' => max(0, 5000000 - $isPrincipal * $s),
                'status' => 'paid', 'paid_amount' => $total,
                'paid_at' => $dueDate->copy()->addDays(3), 'processed_by' => $pengurus->id,
            ]);
            if ($kasAcc && $piutangAcc && $pendapatanAcc) {
                $jAngs = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-' . $dueDate->format('Ym') . '-ANG1-' . $s, 'description' => 'Angsuran ke-' . $s . ' (Rina Kusuma)', 'date' => $dueDate->copy()->addDays(3)->toDateString(), 'source_type' => 'loan_schedule', 'created_by' => $pengurus->id, 'created_at' => $dueDate->copy()->addDays(3), 'updated_at' => $dueDate->copy()->addDays(3)]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $kasAcc->id,      'description' => 'Kas Masuk Angsuran',      'debit' => $total,       'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $piutangAcc->id,  'description' => 'Pelunasan Pokok',          'debit' => 0,            'credit' => $isPrincipal]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $pendapatanAcc->id,'description' => 'Pendapatan Bunga Pinjaman','debit' => 0,            'credit' => $isInterest]);
            }
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 8. PINJAMAN #2 – DONI PRASETYO: LUNAS (Mar 2025 – Des 2025, 10 bulan)
        //    Pinjam Rp 7.000.000 untuk biaya pendidikan, sudah LUNAS akhir 2025
        // ─────────────────────────────────────────────────────────────────────────
        $loan2Start = Carbon::create(2025, 3, 1);
        $loan2 = Loan::create([
            'organization_id' => $org->id, 'user_id' => $members[1]->id,
            'amount' => 7000000, 'interest_rate' => 1.5, 'tenor_months' => 10,
            'interest_method' => 'flat', 'status' => 'completed',
            'purpose' => 'Biaya Pendidikan Anak', 'credit_score' => 16.2,
            'approved_by' => $pengurus->id, 'approved_at' => $loan2Start,
            'disbursed_at' => $loan2Start,
            'created_at' => $loan2Start, 'updated_at' => $loan2Start,
        ]);
        if ($kasAcc && $piutangAcc) {
            $jLoan2 = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-202503-LN02', 'description' => 'Pencairan Pinjaman – Doni Prasetyo', 'date' => $loan2Start->toDateString(), 'source_type' => 'loan', 'created_by' => $pengurus->id, 'created_at' => $loan2Start, 'updated_at' => $loan2Start]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan2->id, 'account_id' => $piutangAcc->id, 'description' => 'Piutang Pinjaman Doni', 'debit' => 7000000, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan2->id, 'account_id' => $kasAcc->id,     'description' => 'Kas Keluar Pencairan',   'debit' => 0,       'credit' => 7000000]);
        }
        $p2 = floor((7000000 / 10) / 1000) * 1000;
        $i2 = floor((7000000 * 0.015));
        for ($s = 1; $s <= 10; $s++) {
            $dueDate = $loan2Start->copy()->addMonths($s - 1);
            $isPrincipal = ($s === 10) ? (7000000 - $p2 * 9) : $p2;
            $isInterest  = $i2;
            $total = $isPrincipal + $isInterest;
            LoanSchedule::create([
                'loan_id' => $loan2->id, 'organization_id' => $org->id, 'user_id' => $members[1]->id,
                'installment_number' => $s, 'due_date' => $dueDate->toDateString(),
                'principal_amount' => $isPrincipal, 'interest_amount' => $isInterest, 'total_amount' => $total,
                'remaining_balance' => max(0, 7000000 - $isPrincipal * $s),
                'status' => 'paid', 'paid_amount' => $total,
                'paid_at' => $dueDate->copy()->addDays(5), 'processed_by' => $pengurus->id,
            ]);
            if ($kasAcc && $piutangAcc && $pendapatanAcc) {
                $jAngs = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-' . $dueDate->format('Ym') . '-ANG2-' . $s, 'description' => 'Angsuran ke-' . $s . ' (Doni Prasetyo)', 'date' => $dueDate->copy()->addDays(5)->toDateString(), 'source_type' => 'loan_schedule', 'created_by' => $pengurus->id, 'created_at' => $dueDate->copy()->addDays(5), 'updated_at' => $dueDate->copy()->addDays(5)]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $kasAcc->id,       'description' => 'Kas Masuk Angsuran',       'debit' => $total,   'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $piutangAcc->id,   'description' => 'Pelunasan Pokok',           'debit' => 0,        'credit' => $isPrincipal]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $pendapatanAcc->id,'description' => 'Pendapatan Bunga Pinjaman', 'debit' => 0,        'credit' => $isInterest]);
            }
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 9. PINJAMAN #3 – HENDRA WIJAYA: LUNAS (Jun 2025 – Mar 2026, 10 bulan)
        //    Pinjam Rp 6.000.000 untuk renovasi rumah
        // ─────────────────────────────────────────────────────────────────────────
        $loan3Start = Carbon::create(2025, 6, 1);
        $loan3 = Loan::create([
            'organization_id' => $org->id, 'user_id' => $members[3]->id,
            'amount' => 6000000, 'interest_rate' => 1.5, 'tenor_months' => 10,
            'interest_method' => 'flat', 'status' => 'completed',
            'purpose' => 'Renovasi Rumah', 'credit_score' => 14.1,
            'approved_by' => $pengurus->id, 'approved_at' => $loan3Start,
            'disbursed_at' => $loan3Start,
            'created_at' => $loan3Start, 'updated_at' => $loan3Start,
        ]);
        if ($kasAcc && $piutangAcc) {
            $jLoan3 = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-202506-LN03', 'description' => 'Pencairan Pinjaman – Hendra Wijaya', 'date' => $loan3Start->toDateString(), 'source_type' => 'loan', 'created_by' => $pengurus->id, 'created_at' => $loan3Start, 'updated_at' => $loan3Start]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan3->id, 'account_id' => $piutangAcc->id, 'description' => 'Piutang Pinjaman Hendra', 'debit' => 6000000, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan3->id, 'account_id' => $kasAcc->id,     'description' => 'Kas Keluar Pencairan',    'debit' => 0,       'credit' => 6000000]);
        }
        $p3 = floor((6000000 / 10) / 1000) * 1000;
        $i3 = floor((6000000 * 0.015));
        for ($s = 1; $s <= 10; $s++) {
            $dueDate = $loan3Start->copy()->addMonths($s - 1);
            $isPrincipal = ($s === 10) ? (6000000 - $p3 * 9) : $p3;
            $isInterest  = $i3;
            $total = $isPrincipal + $isInterest;
            LoanSchedule::create([
                'loan_id' => $loan3->id, 'organization_id' => $org->id, 'user_id' => $members[3]->id,
                'installment_number' => $s, 'due_date' => $dueDate->toDateString(),
                'principal_amount' => $isPrincipal, 'interest_amount' => $isInterest, 'total_amount' => $total,
                'remaining_balance' => max(0, 6000000 - $isPrincipal * $s),
                'status' => 'paid', 'paid_amount' => $total,
                'paid_at' => $dueDate->copy()->addDays(7), 'processed_by' => $pengurus->id,
            ]);
            if ($kasAcc && $piutangAcc && $pendapatanAcc) {
                $jAngs = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-' . $dueDate->format('Ym') . '-ANG3-' . $s, 'description' => 'Angsuran ke-' . $s . ' (Hendra Wijaya)', 'date' => $dueDate->copy()->addDays(7)->toDateString(), 'source_type' => 'loan_schedule', 'created_by' => $pengurus->id, 'created_at' => $dueDate->copy()->addDays(7), 'updated_at' => $dueDate->copy()->addDays(7)]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $kasAcc->id,       'description' => 'Kas Masuk Angsuran',       'debit' => $total,   'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $piutangAcc->id,   'description' => 'Pelunasan Pokok',           'debit' => 0,        'credit' => $isPrincipal]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $pendapatanAcc->id,'description' => 'Pendapatan Bunga Pinjaman', 'debit' => 0,        'credit' => $isInterest]);
            }
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 10. SHU TAHUN 2025 – SUDAH DIDISTRIBUSIKAN (RAT Feb 2026)
        //     Pendapatan bunga 2025: Loan1 (750k) + Loan2 (1.050k) + sebagian Loan3 (540k) = ~2.340.000
        //     Beban operasional 2025: 300rb x 12 = 3.600.000
        //     Untuk demo, SHU 2025 total profit dibuat realistis dengan mempertimbangkan skala koperasi
        //     Isi laporan ini = hasil akumulasi yang telah disetujui RAT
        // ─────────────────────────────────────────────────────────────────────────
        $shu2025 = ShuDistribution::create([
            'organization_id'    => $org->id,
            'year'               => 2025,
            'total_profit'       => 12500000,  // SHU bersih setelah dikurangi beban
            'total_dana_cadangan'=> 3750000,   // 30%
            'total_anggota'      => 5625000,   // 45%
            'total_pengurus'     => 1250000,   // 10%
            'total_karyawan'     => 625000,    // 5%
            'total_pendidikan'   => 1250000,   // 10%
            'total_jasa_modal'   => 2812500,   // 50% dari total_anggota
            'total_jasa_pinjaman'=> 2812500,   // 50% dari total_anggota
            'status'             => 'distributed', // Sudah dibagikan (RAT Feb 2026)
            'approved_by'        => $pengawas->id,
            'distributed_at'     => Carbon::create(2026, 2, 15),
            'created_at'         => Carbon::create(2026, 1, 10),
            'updated_at'         => Carbon::create(2026, 2, 15),
        ]);

        // Detail SHU per anggota (sudah didistribusikan ke simpanan sukarela)
        $totalSimpananPerAnggota2025 = $org->simpanan_pokok + ($org->simpanan_wajib * 12);
        $totalSimpananAll2025 = $totalSimpananPerAnggota2025 * count($members);
        // Pembayar bunga: Loan1 (Rina), Loan2 (Doni), Loan3 (sebagian Hendra)
        $bungaPerAnggota = [750000, 1050000, 0, 540000, 0, 0, 0]; // Rina, Doni, Lisa, Hendra, Maya, Reza, Dewi
        $totalBungaAll2025 = array_sum($bungaPerAnggota);

        foreach ($members as $idx => $member) {
            $porsiModal   = $totalSimpananAll2025 > 0 ? round(($totalSimpananPerAnggota2025 / $totalSimpananAll2025) * $shu2025->total_jasa_modal) : 0;
            $porsiBunga   = $totalBungaAll2025 > 0 ? round(($bungaPerAnggota[$idx] / max(1, $totalBungaAll2025)) * $shu2025->total_jasa_pinjaman) : 0;
            $totalShu     = $porsiModal + $porsiBunga;
            ShuMemberDetail::withoutGlobalScopes()->create([
                'shu_distribution_id' => $shu2025->id,
                'organization_id'     => $org->id,
                'user_id'             => $member->id,
                'total_simpanan'      => $totalSimpananPerAnggota2025,
                'total_bunga_paid'    => $bungaPerAnggota[$idx],
                'jasa_modal'          => $porsiModal,
                'jasa_pinjaman'       => $porsiBunga,
                'total_shu'           => $totalShu,
                'deposited_at'        => Carbon::create(2026, 2, 15), // Sudah dicairkan
            ]);
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 11. PINJAMAN #4 – LISA ANDIANI: AKTIF (Feb 2026 – Jan 2027, 12 bulan)
        //     Pinjam Rp 10.000.000, saat ini sudah berjalan 5 bulan (Feb-Jun 2026)
        // ─────────────────────────────────────────────────────────────────────────
        $loan4Start = Carbon::create(2026, 2, 1);
        $loan4 = Loan::create([
            'organization_id' => $org->id, 'user_id' => $members[2]->id,
            'amount' => 10000000, 'interest_rate' => 1.5, 'tenor_months' => 12,
            'interest_method' => 'flat', 'status' => 'active',
            'purpose' => 'Modal Usaha Warung Makan', 'credit_score' => 23.1,
            'approved_by' => $pengurus->id, 'approved_at' => $loan4Start,
            'disbursed_at' => $loan4Start,
            'created_at' => $loan4Start, 'updated_at' => $loan4Start,
        ]);
        if ($kasAcc && $piutangAcc) {
            $jLoan4 = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-202602-LN04', 'description' => 'Pencairan Pinjaman – Lisa Andiani', 'date' => $loan4Start->toDateString(), 'source_type' => 'loan', 'created_by' => $pengurus->id, 'created_at' => $loan4Start, 'updated_at' => $loan4Start]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan4->id, 'account_id' => $piutangAcc->id, 'description' => 'Piutang Pinjaman Lisa', 'debit' => 10000000, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan4->id, 'account_id' => $kasAcc->id,     'description' => 'Kas Keluar Pencairan',  'debit' => 0,        'credit' => 10000000]);
        }
        $p4 = floor((10000000 / 12) / 1000) * 1000;
        $i4 = floor((10000000 * 0.015));
        for ($s = 1; $s <= 12; $s++) {
            $dueDate = $loan4Start->copy()->addMonths($s - 1);
            $isPaid  = $dueDate->lessThanOrEqualTo(Carbon::create(2026, 6, 30)); // Feb–Jun sudah bayar
            $isPrincipal = ($s === 12) ? (10000000 - $p4 * 11) : $p4;
            $isInterest  = $i4;
            $total = $isPrincipal + $isInterest;
            LoanSchedule::create([
                'loan_id' => $loan4->id, 'organization_id' => $org->id, 'user_id' => $members[2]->id,
                'installment_number' => $s, 'due_date' => $dueDate->toDateString(),
                'principal_amount' => $isPrincipal, 'interest_amount' => $isInterest, 'total_amount' => $total,
                'remaining_balance' => max(0, 10000000 - $isPrincipal * $s),
                'status' => $isPaid ? 'paid' : 'pending', 'paid_amount' => $isPaid ? $total : 0,
                'paid_at' => $isPaid ? $dueDate->copy()->addDays(4) : null,
                'processed_by' => $isPaid ? $pengurus->id : null,
            ]);
            if ($isPaid && $kasAcc && $piutangAcc && $pendapatanAcc) {
                $jAngs = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-' . $dueDate->format('Ym') . '-ANG4-' . $s, 'description' => 'Angsuran ke-' . $s . ' (Lisa Andiani)', 'date' => $dueDate->copy()->addDays(4)->toDateString(), 'source_type' => 'loan_schedule', 'created_by' => $pengurus->id, 'created_at' => $dueDate->copy()->addDays(4), 'updated_at' => $dueDate->copy()->addDays(4)]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $kasAcc->id,       'description' => 'Kas Masuk Angsuran',       'debit' => $total,   'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $piutangAcc->id,   'description' => 'Pelunasan Pokok',           'debit' => 0,        'credit' => $isPrincipal]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $pendapatanAcc->id,'description' => 'Pendapatan Bunga Pinjaman', 'debit' => 0,        'credit' => $isInterest]);
            }
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 12. PINJAMAN #5 – REZA FIRMANSYAH: AKTIF (Mei 2026 – berjalan, baru 2 bulan)
        //     Pinjam Rp 8.000.000, tenor 8 bulan
        // ─────────────────────────────────────────────────────────────────────────
        $loan5Start = Carbon::create(2026, 5, 1);
        $loan5 = Loan::create([
            'organization_id' => $org->id, 'user_id' => $members[5]->id,
            'amount' => 8000000, 'interest_rate' => 1.5, 'tenor_months' => 8,
            'interest_method' => 'flat', 'status' => 'active',
            'purpose' => 'Biaya Pernikahan', 'credit_score' => 18.5,
            'approved_by' => $pengurus->id, 'approved_at' => $loan5Start,
            'disbursed_at' => $loan5Start,
            'created_at' => $loan5Start, 'updated_at' => $loan5Start,
        ]);
        if ($kasAcc && $piutangAcc) {
            $jLoan5 = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-202605-LN05', 'description' => 'Pencairan Pinjaman – Reza Firmansyah', 'date' => $loan5Start->toDateString(), 'source_type' => 'loan', 'created_by' => $pengurus->id, 'created_at' => $loan5Start, 'updated_at' => $loan5Start]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan5->id, 'account_id' => $piutangAcc->id, 'description' => 'Piutang Pinjaman Reza', 'debit' => 8000000, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan5->id, 'account_id' => $kasAcc->id,     'description' => 'Kas Keluar Pencairan',  'debit' => 0,       'credit' => 8000000]);
        }
        $p5 = floor((8000000 / 8) / 1000) * 1000;
        $i5 = floor((8000000 * 0.015));
        for ($s = 1; $s <= 8; $s++) {
            $dueDate = $loan5Start->copy()->addMonths($s - 1);
            $isPaid  = $dueDate->lessThanOrEqualTo(Carbon::create(2026, 6, 30)); // Mei–Jun sudah bayar
            $isPrincipal = ($s === 8) ? (8000000 - $p5 * 7) : $p5;
            $isInterest  = $i5;
            $total = $isPrincipal + $isInterest;
            LoanSchedule::create([
                'loan_id' => $loan5->id, 'organization_id' => $org->id, 'user_id' => $members[5]->id,
                'installment_number' => $s, 'due_date' => $dueDate->toDateString(),
                'principal_amount' => $isPrincipal, 'interest_amount' => $isInterest, 'total_amount' => $total,
                'remaining_balance' => max(0, 8000000 - $isPrincipal * $s),
                'status' => $isPaid ? 'paid' : 'pending', 'paid_amount' => $isPaid ? $total : 0,
                'paid_at' => $isPaid ? $dueDate->copy()->addDays(6) : null,
                'processed_by' => $isPaid ? $pengurus->id : null,
            ]);
            if ($isPaid && $kasAcc && $piutangAcc && $pendapatanAcc) {
                $jAngs = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-' . $dueDate->format('Ym') . '-ANG5-' . $s, 'description' => 'Angsuran ke-' . $s . ' (Reza Firmansyah)', 'date' => $dueDate->copy()->addDays(6)->toDateString(), 'source_type' => 'loan_schedule', 'created_by' => $pengurus->id, 'created_at' => $dueDate->copy()->addDays(6), 'updated_at' => $dueDate->copy()->addDays(6)]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $kasAcc->id,       'description' => 'Kas Masuk Angsuran',       'debit' => $total,   'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $piutangAcc->id,   'description' => 'Pelunasan Pokok',           'debit' => 0,        'credit' => $isPrincipal]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $pendapatanAcc->id,'description' => 'Pendapatan Bunga Pinjaman', 'debit' => 0,        'credit' => $isInterest]);
            }
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 13. PINJAMAN #6 – DONI PRASETYO: PENDING (baru mengajukan, belum disetujui)
        //     Pengajuan kedua, untuk modal usaha sampingan
        // ─────────────────────────────────────────────────────────────────────────
        Loan::create([
            'organization_id' => $org->id, 'user_id' => $members[1]->id,
            'amount' => 12000000, 'interest_rate' => 1.5, 'tenor_months' => 18,
            'interest_method' => 'flat', 'status' => 'pending',
            'purpose' => 'Modal Usaha Sampingan (Konveksi)', 'credit_score' => 27.5,
            'created_at' => Carbon::create(2026, 7, 1), 'updated_at' => Carbon::create(2026, 7, 1),
        ]);

        // ─────────────────────────────────────────────────────────────────────────
        // 14. SHU TAHUN 2026 – DRAFT (menunggu RAT awal 2027)
        // ─────────────────────────────────────────────────────────────────────────
        $shu2026 = ShuDistribution::create([
            'organization_id'    => $org->id,
            'year'               => 2026,
            'total_profit'       => 8500000,   // Estimasi sementara (baru 6 bulan berjalan)
            'total_dana_cadangan'=> 2550000,
            'total_anggota'      => 3825000,
            'total_pengurus'     => 850000,
            'total_karyawan'     => 425000,
            'total_pendidikan'   => 850000,
            'total_jasa_modal'   => 1912500,
            'total_jasa_pinjaman'=> 1912500,
            'status'             => 'draft',
            'approved_by'        => null,
            'distributed_at'     => null,
            'created_at'         => Carbon::create(2026, 7, 5),
            'updated_at'         => Carbon::create(2026, 7, 5),
        ]);

        $totalSimpananPerAnggota2026 = $org->simpanan_pokok + ($org->simpanan_wajib * 18); // 18 bulan simpanan
        $totalSimpananAll2026 = $totalSimpananPerAnggota2026 * count($members);
        $bungaPerAnggota2026 = [0, 0, 1050000, 0, 0, 960000, 0]; // Lisa, Reza yang aktif pinjam 2026
        $totalBungaAll2026 = array_sum($bungaPerAnggota2026);

        foreach ($members as $idx => $member) {
            $porsiModal   = $totalSimpananAll2026 > 0 ? round(($totalSimpananPerAnggota2026 / $totalSimpananAll2026) * $shu2026->total_jasa_modal) : 0;
            $porsiBunga   = $totalBungaAll2026 > 0 ? round(($bungaPerAnggota2026[$idx] / max(1, $totalBungaAll2026)) * $shu2026->total_jasa_pinjaman) : 0;
            $totalShu     = $porsiModal + $porsiBunga;
            ShuMemberDetail::withoutGlobalScopes()->create([
                'shu_distribution_id' => $shu2026->id,
                'organization_id'     => $org->id,
                'user_id'             => $member->id,
                'total_simpanan'      => $totalSimpananPerAnggota2026,
                'total_bunga_paid'    => $bungaPerAnggota2026[$idx],
                'jasa_modal'          => $porsiModal,
                'jasa_pinjaman'       => $porsiBunga,
                'total_shu'           => $totalShu,
                'deposited_at'        => null, // Belum dibagikan
            ]);
        }

        // ─────────────────────────────────────────────────────────────────────────
        // OUTPUT SUMMARY
        // ─────────────────────────────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅ Demo seeded! KSP Mitra Sejahtera – 2.5 Tahun Berjalan (Jan 2025 – Jul 2026)');
        $this->command->info('');
        $this->command->info('📋 Ringkasan Skenario Demo:');
        $this->command->info('   ├─ 7 anggota aktif sejak Jan 2025');
        $this->command->info('   ├─ 18 bulan simpanan wajib (Jan 2025 – Jun 2026)');
        $this->command->info('   ├─ 3 pinjaman LUNAS (Rina, Doni, Hendra)');
        $this->command->info('   ├─ 2 pinjaman AKTIF (Lisa & Reza sedang berjalan)');
        $this->command->info('   ├─ 1 pengajuan PENDING (Doni - mengajukan kedua kali)');
        $this->command->info('   ├─ SHU 2025: Rp 12.500.000 – SUDAH DIBAGIKAN (RAT Feb 2026)');
        $this->command->info('   └─ SHU 2026: Rp 8.500.000 – DRAFT (estimasi, belum RAT)');
        $this->command->info('');
        $this->command->table(
            ['Role', 'Nama', 'Email', 'Password'],
            [
                ['Superadmin', 'System',          'superadmin@danakarya.id',        'SuperAdmin@2024!'],
                ['Admin',      'Firmansyah Adi',  'admin@demo.danakarya.id',        'Demo@123!'],
                ['Pengurus',   'Sri Mulyani',     'pengurus@demo.danakarya.id',     'Demo@123!'],
                ['Pengawas',   'Agus Salim',      'pengawas@demo.danakarya.id',     'Demo@123!'],
                ['Anggota',    'Rina Kusuma',     'rina.kusuma@demo.danakarya.id',  'Demo@123!'],
                ['Anggota',    'Doni Prasetyo',   'doni.prasetyo@demo.danakarya.id','Demo@123!'],
                ['Anggota',    'Lisa Andiani',    'lisa.andiani@demo.danakarya.id', 'Demo@123!'],
                ['Anggota',    'Hendra Wijaya',   'hendra.wijaya@demo.danakarya.id','Demo@123!'],
                ['Anggota',    'Maya Putri',      'maya.putri@demo.danakarya.id',   'Demo@123!'],
            ]
        );
    }
}
