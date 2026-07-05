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
use App\Services\LoanService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $accountingService = app(AccountingService::class);
        $loanService       = app(LoanService::class);

        // ── Dates Setup ─────────────────────────────────────────────────────
        // Koperasi berdiri Januari 2026, tepat 1 tahun berjalan s/d Desember 2026
        $startDate   = Carbon::create(2026, 1, 1);
        $currentDate = Carbon::create(2026, 12, 31);

        // ── Create Demo Organization ────────────────────────────────────────
        $org = Organization::create([
            'name'                  => 'KSP Sejahtera Bersama',
            'legal_name'            => 'Koperasi Simpan Pinjam Sejahtera Bersama',
            'address'               => 'Jl. Sudirman No. 1, Jakarta Pusat',
            'phone'                 => '021-12345678',
            'email'                 => 'admin@ksp-sejahtera.co.id',
            'legal_number'          => '001234/BH/KOP/2020',
            'is_configured'         => true,
            'is_active'             => true,
            'simpanan_pokok'        => 400000,
            'simpanan_wajib'        => 200000,
            'loan_interest_rate'    => 1.5,
            'loan_max_tenor'        => 24,
            'loan_max_plafon'       => 10000000,
            'loan_interest_method'  => 'flat',
            'max_loan_salary_pct'   => 30,
            'deposit_date'          => 1,
            'payroll_date'          => 25,
            'shu_dana_cadangan_pct' => 40,
            'shu_anggota_pct'       => 40,
            'shu_pengurus_pct'      => 5,
            'shu_karyawan_pct'      => 5,
            'shu_pendidikan_pct'    => 10,
        ]);

        // Seed default Chart of Accounts
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
            ['name' => 'Reza Firmansyah','salary' => 7000000,  'dept' => 'Operations'],
            ['name' => 'Dewi Lestari',   'salary' => 6000000,  'dept' => 'Marketing'],
        ];

        $members = [];
        foreach ($memberData as $i => $data) {
            $email  = strtolower(str_replace(' ', '.', $data['name'])) . '@demo.danakarya.id';
            $member = User::create([
                'name'            => $data['name'],
                'email'           => $email,
                'password'        => bcrypt('Demo@123!'),
                'organization_id' => $org->id,
                'employee_id'     => 'EMP-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'department'      => $data['dept'],
                'salary'          => $data['salary'],
                'join_date'       => $startDate->toDateString(), // Jan 2026
                'status'          => 'active',
            ]);
            $member->assignRole('anggota');
            $members[] = $member;

            // ── Simpanan Pokok (dibayar di awal bergabung, Januari 2026) ──
            Deposit::create([
                'organization_id'  => $org->id,
                'user_id'          => $member->id,
                'type'             => 'pokok',
                'amount'           => $org->simpanan_pokok,
                'status'           => 'completed',
                'transaction_type' => 'credit',
                'notes'            => 'Simpanan Pokok Awal (Potongan Payroll)',
                'processed_by'     => $admin->id,
                'created_at'       => $startDate,
                'updated_at'       => $startDate,
            ]);

            // ── Simpanan Wajib: 12 bulan (Jan 2026 – Des 2026) semua lunas ──
            $totalMonths = 12; // 12 bulan penuh (1 tahun pas)
            for ($m = 0; $m < $totalMonths; $m++) {
                $depositDate = $startDate->copy()->addMonths($m)->startOfMonth();
                Deposit::create([
                    'organization_id'  => $org->id,
                    'user_id'          => $member->id,
                    'type'             => 'wajib',
                    'amount'           => $org->simpanan_wajib,
                    'status'           => 'completed',
                    'transaction_type' => 'credit',
                    'period_month'     => $depositDate->month,
                    'period_year'      => $depositDate->year,
                    'notes'            => 'Potongan Payroll ' . $depositDate->format('M Y'),
                    'processed_by'     => $admin->id,
                    'created_at'       => $depositDate,
                    'updated_at'       => $depositDate,
                ]);
            }
        }

        // ── Suntikan Modal Awal Koperasi (Jan 2026) ──────────────────────────
        // Debit Kas, Kredit Modal Koperasi
        $kasAccount    = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '1-101')->first();
        $modalAccount  = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '3-101')->first();

        if ($kasAccount && $modalAccount) {
            $modalAwal = JournalEntry::create([
                'organization_id' => $org->id,
                'reference'       => 'JU-202601-0001',
                'description'     => 'Modal Awal Pendirian Koperasi',
                'date'            => $startDate->toDateString(),
                'source_type'     => 'manual',
                'created_by'      => $pengurus->id,
                'created_at'      => $startDate,
                'updated_at'      => $startDate,
            ]);
            JournalEntryLine::create(['journal_entry_id' => $modalAwal->id, 'account_id' => $kasAccount->id,   'description' => 'Kas Masuk Modal Awal', 'debit' => 5000000,  'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $modalAwal->id, 'account_id' => $modalAccount->id, 'description' => 'Modal Koperasi',       'debit' => 0,        'credit' => 5000000]);
        }

        // ── Kas masuk dari simpanan pokok & wajib seluruh anggota ─────────────
        // Akun: Kas (1-101) debit, Simpanan Pokok (2-101) / Simpanan Wajib (2-202) credit
        $simpananPokokAcc = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '2-101')->first();
        $simpananWajibAcc = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '2-202')->first();

        if ($kasAccount && $simpananPokokAcc) {
            $totalPokok = $org->simpanan_pokok * count($members);
            $jPokok = JournalEntry::create([
                'organization_id' => $org->id,
                'reference'       => 'JU-202601-0002',
                'description'     => 'Penerimaan Simpanan Pokok Seluruh Anggota',
                'date'            => $startDate->toDateString(),
                'source_type'     => 'deposit',
                'created_by'      => $admin->id,
                'created_at'      => $startDate,
                'updated_at'      => $startDate,
            ]);
            JournalEntryLine::create(['journal_entry_id' => $jPokok->id, 'account_id' => $kasAccount->id,       'description' => 'Kas Masuk Simpanan Pokok', 'debit' => $totalPokok, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jPokok->id, 'account_id' => $simpananPokokAcc->id, 'description' => 'Simpanan Pokok Anggota',   'debit' => 0,           'credit' => $totalPokok]);
        }

        // Simpanan wajib per bulan (18 bulan x 7 anggota)
        if ($kasAccount && $simpananWajibAcc) {
            for ($m = 0; $m < 12; $m++) {
                $depositDate  = $startDate->copy()->addMonths($m)->startOfMonth();
                $totalWajib   = $org->simpanan_wajib * count($members);
                $jWajib = JournalEntry::create([
                    'organization_id' => $org->id,
                    'reference'       => 'JU-' . $depositDate->format('Ym') . '-WJB',
                    'description'     => 'Penerimaan Simpanan Wajib ' . $depositDate->format('F Y'),
                    'date'            => $depositDate->toDateString(),
                    'source_type'     => 'payroll',
                    'created_by'      => $admin->id,
                    'created_at'      => $depositDate,
                    'updated_at'      => $depositDate,
                ]);
                JournalEntryLine::create(['journal_entry_id' => $jWajib->id, 'account_id' => $kasAccount->id,       'description' => 'Kas Masuk Payroll', 'debit' => $totalWajib, 'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jWajib->id, 'account_id' => $simpananWajibAcc->id, 'description' => 'Simpanan Wajib',   'debit' => 0,           'credit' => $totalWajib]);
            }
        }

        // ── Demo Loan #1: Rina Kusuma – Lunas (Feb–Nov 2026) ────────────────
        $piutangAcc   = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '1-201')->first();
        $pendapatanAcc= Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '4-101')->first();

        $loan1Start = Carbon::create(2026, 2, 1);
        $loan1 = Loan::create([
            'organization_id' => $org->id,
            'user_id'         => $members[0]->id,
            'amount'          => 5000000,
            'interest_rate'   => 1.5,
            'tenor_months'    => 10,
            'interest_method' => 'flat',
            'status'          => 'completed',
            'purpose'         => 'Kebutuhan Rumah Tangga',
            'approved_by'     => $pengurus->id,
            'approved_at'     => $loan1Start,
            'disbursed_at'    => $loan1Start,
            'credit_score'    => 11.9,
            'created_at'      => $loan1Start,
            'updated_at'      => $loan1Start,
        ]);
        // Jurnal pencairan
        if ($kasAccount && $piutangAcc) {
            $jLoan1 = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-202602-LN01', 'description' => 'Pencairan Pinjaman - Rina Kusuma', 'date' => $loan1Start->toDateString(), 'source_type' => 'loan', 'created_by' => $pengurus->id, 'created_at' => $loan1Start, 'updated_at' => $loan1Start]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan1->id, 'account_id' => $piutangAcc->id, 'description' => 'Piutang Pinjaman', 'debit' => 5000000, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan1->id, 'account_id' => $kasAccount->id,  'description' => 'Kas Keluar',       'debit' => 0,       'credit' => 5000000]);
        }
        // Generate schedule & mark semua lunas (10 bulan, Feb-Nov 2025)
        LoanSchedule::withoutGlobalScopes()->where('loan_id', $loan1->id)->delete();
        $principal1 = floor((5000000 / 10) / 1000) * 1000;
        $interest1  = floor((5000000 * 0.015) / 1000) * 1000;
        for ($s = 1; $s <= 10; $s++) {
            $dueDate = $loan1Start->copy()->addMonths($s - 1);
            $isPrincipal = ($s === 10) ? (5000000 - $principal1 * 9) : $principal1;
            $isInterest  = ($s === 10) ? (5000000 * 0.015 * 10 - $interest1 * 9) : $interest1;
            $total = $isPrincipal + $isInterest;
            $sch = LoanSchedule::create([
                'loan_id'            => $loan1->id,
                'organization_id'    => $org->id,
                'user_id'            => $members[0]->id,
                'installment_number' => $s,
                'due_date'           => $dueDate->toDateString(),
                'principal_amount'   => $isPrincipal,
                'interest_amount'    => $isInterest,
                'total_amount'       => $total,
                'remaining_balance'  => max(0, 5000000 - $isPrincipal * $s),
                'status'             => 'paid',
                'paid_amount'        => $total,
                'paid_at'            => $dueDate->copy()->addDays(5),
                'processed_by'       => $pengurus->id,
            ]);
            // Jurnal angsuran
            if ($kasAccount && $piutangAcc && $pendapatanAcc) {
                $jAngs = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-' . $dueDate->format('Ym') . '-ANG1-' . $s, 'description' => 'Angsuran ke-' . $s . ' Rina Kusuma', 'date' => $dueDate->copy()->addDays(5)->toDateString(), 'source_type' => 'loan_schedule', 'created_by' => $pengurus->id, 'created_at' => $dueDate->copy()->addDays(5), 'updated_at' => $dueDate->copy()->addDays(5)]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $kasAccount->id,     'description' => 'Kas Masuk Angsuran',       'debit' => $total,           'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $piutangAcc->id,    'description' => 'Pelunasan Pokok Pinjaman',  'debit' => 0,                'credit' => $isPrincipal]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $pendapatanAcc->id, 'description' => 'Pendapatan Jasa Pinjaman',  'debit' => 0,                'credit' => $isInterest]);
            }
        }

        // ── Demo Loan #2: Doni Prasetyo – Aktif (sedang berjalan) ─────────────
        // Dicairkan Jun 2026, tenor 8 bulan → hampir selesai akhir tahun
        $loan2Start = Carbon::create(2026, 6, 1);
        $loan2 = Loan::create([
            'organization_id' => $org->id,
            'user_id'         => $members[1]->id,
            'amount'          => 8000000,
            'interest_rate'   => 1.5,
            'tenor_months'    => 8,
            'interest_method' => 'flat',
            'status'          => 'active',
            'purpose'         => 'Biaya Pendidikan Anak',
            'approved_by'     => $pengurus->id,
            'approved_at'     => $loan2Start,
            'disbursed_at'    => $loan2Start,
            'credit_score'    => 18.5,
            'created_at'      => $loan2Start,
            'updated_at'      => $loan2Start,
        ]);
        if ($kasAccount && $piutangAcc) {
            $jLoan2 = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-202606-LN02', 'description' => 'Pencairan Pinjaman - Doni Prasetyo', 'date' => $loan2Start->toDateString(), 'source_type' => 'loan', 'created_by' => $pengurus->id, 'created_at' => $loan2Start, 'updated_at' => $loan2Start]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan2->id, 'account_id' => $piutangAcc->id, 'description' => 'Piutang Pinjaman', 'debit' => 8000000, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan2->id, 'account_id' => $kasAccount->id,  'description' => 'Kas Keluar',       'debit' => 0,       'credit' => 8000000]);
        }
        LoanSchedule::withoutGlobalScopes()->where('loan_id', $loan2->id)->delete();
        $principal2 = floor((8000000 / 8) / 1000) * 1000;
        $interest2  = floor((8000000 * 0.015) / 1000) * 1000;
        // 7 bulan sudah berjalan (Jun–Des 2026), sisa 1 bulan lagi
        for ($s = 1; $s <= 8; $s++) {
            $dueDate  = $loan2Start->copy()->addMonths($s - 1);
            $isPaid   = $dueDate->lessThanOrEqualTo(Carbon::create(2026, 11, 30)); // Jun–Nov lunas
            $isPrincipal = ($s === 8) ? (8000000 - $principal2 * 7) : $principal2;
            $isInterest  = ($s === 8) ? (8000000 * 0.015 * 8 - $interest2 * 7) : $interest2;
            $total = $isPrincipal + $isInterest;
            LoanSchedule::create([
                'loan_id'            => $loan2->id,
                'organization_id'    => $org->id,
                'user_id'            => $members[1]->id,
                'installment_number' => $s,
                'due_date'           => $dueDate->toDateString(),
                'principal_amount'   => $isPrincipal,
                'interest_amount'    => $isInterest,
                'total_amount'       => $total,
                'remaining_balance'  => max(0, 8000000 - $isPrincipal * $s),
                'status'             => $isPaid ? 'paid' : 'pending',
                'paid_amount'        => $isPaid ? $total : 0,
                'paid_at'            => $isPaid ? $dueDate->copy()->addDays(25) : null,
                'processed_by'       => $isPaid ? $pengurus->id : null,
            ]);
            if ($isPaid && $kasAccount && $piutangAcc && $pendapatanAcc) {
                $jAngs = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-' . $dueDate->format('Ym') . '-ANG2-' . $s, 'description' => 'Angsuran ke-' . $s . ' Doni Prasetyo', 'date' => $dueDate->copy()->addDays(25)->toDateString(), 'source_type' => 'loan_schedule', 'created_by' => $pengurus->id, 'created_at' => $dueDate->copy()->addDays(25), 'updated_at' => $dueDate->copy()->addDays(25)]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $kasAccount->id,     'description' => 'Kas Masuk Angsuran',      'debit' => $total,       'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $piutangAcc->id,    'description' => 'Pelunasan Pokok Pinjaman', 'debit' => 0,            'credit' => $isPrincipal]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $pendapatanAcc->id, 'description' => 'Pendapatan Jasa Pinjaman', 'debit' => 0,            'credit' => $isInterest]);
            }
        }

        // ── Demo Loan #3: Lisa Andiani – Aktif (baru dicairkan Sep 2026) ──────
        $loan3Start = Carbon::create(2026, 9, 1);
        $loan3 = Loan::create([
            'organization_id' => $org->id,
            'user_id'         => $members[2]->id,
            'amount'          => 5000000,
            'interest_rate'   => 1.5,
            'tenor_months'    => 6,
            'interest_method' => 'flat',
            'status'          => 'active',
            'purpose'         => 'Renovasi Rumah',
            'approved_by'     => $pengurus->id,
            'approved_at'     => $loan3Start,
            'disbursed_at'    => $loan3Start,
            'credit_score'    => 16.7,
            'created_at'      => $loan3Start,
            'updated_at'      => $loan3Start,
        ]);
        if ($kasAccount && $piutangAcc) {
            $jLoan3 = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-202609-LN03', 'description' => 'Pencairan Pinjaman - Lisa Andiani', 'date' => $loan3Start->toDateString(), 'source_type' => 'loan', 'created_by' => $pengurus->id, 'created_at' => $loan3Start, 'updated_at' => $loan3Start]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan3->id, 'account_id' => $piutangAcc->id, 'description' => 'Piutang Pinjaman', 'debit' => 6000000, 'credit' => 0]);
            JournalEntryLine::create(['journal_entry_id' => $jLoan3->id, 'account_id' => $kasAccount->id,  'description' => 'Kas Keluar',       'debit' => 0,       'credit' => 6000000]);
        }
        LoanSchedule::withoutGlobalScopes()->where('loan_id', $loan3->id)->delete();
        $principal3 = floor((5000000 / 6) / 1000) * 1000;
        $interest3  = floor((5000000 * 0.015) / 1000) * 1000;
        // Sep–Des 2026 sudah bayar (4 bulan), sisa 2
        for ($s = 1; $s <= 6; $s++) {
            $dueDate  = $loan3Start->copy()->addMonths($s - 1);
            $isPaid   = $dueDate->lessThanOrEqualTo(Carbon::create(2026, 11, 30));
            $isPrincipal = ($s === 6) ? (5000000 - $principal3 * 5) : $principal3;
            $isInterest  = ($s === 6) ? (5000000 * 0.015 * 6 - $interest3 * 5) : $interest3;
            $total = $isPrincipal + $isInterest;
            LoanSchedule::create([
                'loan_id'            => $loan3->id,
                'organization_id'    => $org->id,
                'user_id'            => $members[2]->id,
                'installment_number' => $s,
                'due_date'           => $dueDate->toDateString(),
                'principal_amount'   => $isPrincipal,
                'interest_amount'    => $isInterest,
                'total_amount'       => $total,
                'remaining_balance'  => max(0, 5000000 - $isPrincipal * $s),
                'status'             => $isPaid ? 'paid' : 'pending',
                'paid_amount'        => $isPaid ? $total : 0,
                'paid_at'            => $isPaid ? $dueDate->copy()->addDays(25) : null,
                'processed_by'       => $isPaid ? $pengurus->id : null,
            ]);
            if ($isPaid && $kasAccount && $piutangAcc && $pendapatanAcc) {
                $jAngs = JournalEntry::create(['organization_id' => $org->id, 'reference' => 'JU-' . $dueDate->format('Ym') . '-ANG3-' . $s, 'description' => 'Angsuran ke-' . $s . ' Lisa Andiani', 'date' => $dueDate->copy()->addDays(25)->toDateString(), 'source_type' => 'loan_schedule', 'created_by' => $pengurus->id, 'created_at' => $dueDate->copy()->addDays(25), 'updated_at' => $dueDate->copy()->addDays(25)]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $kasAccount->id,     'description' => 'Kas Masuk Angsuran',      'debit' => $total,       'credit' => 0]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $piutangAcc->id,    'description' => 'Pelunasan Pokok Pinjaman', 'debit' => 0,            'credit' => $isPrincipal]);
                JournalEntryLine::create(['journal_entry_id' => $jAngs->id, 'account_id' => $pendapatanAcc->id, 'description' => 'Pendapatan Jasa Pinjaman', 'debit' => 0,            'credit' => $isInterest]);
            }
        }

        // ── Demo Loan #4: Hendra Wijaya – Pending ────────────────────────────
        Loan::create([
            'organization_id' => $org->id,
            'user_id'         => $members[3]->id,
            'amount'          => 9000000,
            'interest_rate'   => 1.5,
            'tenor_months'    => 18,
            'interest_method' => 'flat',
            'status'          => 'pending',
            'purpose'         => 'Modal Usaha Kecil',
            'credit_score'    => 24.0,
            'created_at'      => Carbon::create(2026, 12, 20),
            'updated_at'      => Carbon::create(2026, 12, 20),
        ]);

        // ── SHU Tahun 2026 (draft, menunggu RAT) ────────────────────────────
        $pendidikanAcc = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '5-301')->first();
        $danaCadanganAcc = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '3-201')->first();
        $sukarelAcc    = Account::withoutGlobalScopes()->where('organization_id', $org->id)->where('code', '2-201')->first();

        $shu2025 = ShuDistribution::create([
            'organization_id'   => $org->id,
            'year'              => 2026,
            'total_profit'      => 38000000,
            'total_dana_cadangan'=> 15200000,
            'total_anggota'     => 15200000,
            'total_pengurus'    => 1900000,
            'total_karyawan'    => 1900000,
            'total_pendidikan'  => 3800000,
            'total_jasa_modal'  => 9120000,
            'total_jasa_pinjaman'=> 6080000,
            'status'            => 'draft',
            'approved_by'       => null,
            'distributed_at'    => null,
            'created_at'        => Carbon::create(2027, 1, 5),
            'updated_at'        => Carbon::create(2027, 1, 5),
        ]);

        // SHU per anggota (dibagi rata untuk demo)
        $shuPerAnggota = floor(15200000 / count($members));
        foreach ($members as $member) {
            $detail = ShuMemberDetail::withoutGlobalScopes()->create([
                'shu_distribution_id' => $shu2025->id,
                'organization_id'     => $org->id,
                'user_id'             => $member->id,
                'total_simpanan'      => 18 * $org->simpanan_wajib + $org->simpanan_pokok,
                'total_bunga_paid'    => 0,
                'jasa_modal'          => round($shuPerAnggota * 0.6),
                'jasa_pinjaman'       => round($shuPerAnggota * 0.4),
                'total_shu'           => $shuPerAnggota,
                'deposited_at'        => null,
            ]);
            // SHU masih draft, belum didistribusikan ke simpanan sukarela
            // $detail->update(['deposit_id' => null]);
        }

        $this->command->info('');
        $this->command->info('✅ Demo seeded! KSP Sejahtera Bersama – Tepat 1 Tahun Berjalan (Jan–Des 2026)');
        $this->command->info('   ↳ 7 anggota, 12 bulan simpanan wajib, 2 pinjaman aktif, 1 pending, SHU 2026 draft (belum RAT)');
        $this->command->info('');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Superadmin', 'superadmin@danakarya.id',        'SuperAdmin@2024!'],
                ['Admin',      'admin@demo.danakarya.id',         'Demo@123!'],
                ['Pengurus',   'pengurus@demo.danakarya.id',      'Demo@123!'],
                ['Pengawas',   'pengawas@demo.danakarya.id',      'Demo@123!'],
                ['Anggota',    'rina.kusuma@demo.danakarya.id',   'Demo@123!'],
                ['Anggota',    'doni.prasetyo@demo.danakarya.id', 'Demo@123!'],
            ]
        );
    }
}
