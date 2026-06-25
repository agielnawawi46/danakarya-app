<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Create a balanced double-entry journal entry.
     *
     * @param array{
     *   description: string,
     *   date: string,
     *   source_type?: string,
     *   source_id?: int,
     *   lines: array<array{account_code: string, debit: float, credit: float, description?: string}>
     * } $data
     */
    public function createJournal(array $data, int $organizationId, int $createdBy): JournalEntry
    {
        return DB::transaction(function () use ($data, $organizationId, $createdBy) {
            $reference = $this->generateReference($organizationId);

            $entry = JournalEntry::create([
                'organization_id' => $organizationId,
                'reference'       => $reference,
                'description'     => $data['description'],
                'date'            => $data['date'] ?? now()->toDateString(),
                'source_type'     => $data['source_type'] ?? null,
                'source_id'       => $data['source_id'] ?? null,
                'created_by'      => $createdBy,
            ]);

            foreach ($data['lines'] as $line) {
                $account = Account::withoutGlobalScopes()
                    ->where('organization_id', $organizationId)
                    ->where('code', $line['account_code'])
                    ->first();

                if (!$account) {
                    throw new \RuntimeException("Akun dengan kode [{$line['account_code']}] tidak ditemukan.");
                }

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id'       => $account->id,
                    'description'      => $line['description'] ?? $data['description'],
                    'debit'            => $line['debit'] ?? 0,
                    'credit'           => $line['credit'] ?? 0,
                ]);
            }

            // Validate balance
            if (!$entry->isBalanced()) {
                throw new \RuntimeException('Jurnal tidak seimbang: Total Debit ≠ Total Kredit.');
            }

            return $entry;
        });
    }

    /**
     * Record journal for deposit (setor simpanan tunai)
     * D: Kas / Bank       K: Simpanan Sukarela Anggota
     */
    public function journalDeposit(float $amount, int $orgId, int $userId, string $memberName): JournalEntry
    {
        return $this->createJournal([
            'description' => "Setoran Simpanan Sukarela - {$memberName}",
            'source_type' => 'deposit',
            'lines'       => [
                ['account_code' => '1-101', 'debit' => $amount, 'credit' => 0, 'description' => 'Kas Masuk'],
                ['account_code' => '2-201', 'debit' => 0, 'credit' => $amount, 'description' => 'Simpanan Sukarela'],
            ],
        ], $orgId, $userId);
    }

    /**
     * Record journal for withdrawal (tarik simpanan)
     * D: Simpanan Sukarela Anggota    K: Kas / Bank
     */
    public function journalWithdrawal(float $amount, int $orgId, int $userId, string $memberName): JournalEntry
    {
        return $this->createJournal([
            'description' => "Penarikan Simpanan Sukarela - {$memberName}",
            'source_type' => 'deposit',
            'lines'       => [
                ['account_code' => '2-201', 'debit' => $amount, 'credit' => 0, 'description' => 'Simpanan Sukarela'],
                ['account_code' => '1-101', 'debit' => 0, 'credit' => $amount, 'description' => 'Kas Keluar'],
            ],
        ], $orgId, $userId);
    }

    /**
     * Record journal for loan disbursement
     * D: Piutang Anggota    K: Kas / Bank
     */
    public function journalLoanDisbursement(float $amount, int $orgId, int $userId, string $memberName): JournalEntry
    {
        return $this->createJournal([
            'description' => "Pencairan Pinjaman - {$memberName}",
            'source_type' => 'loan',
            'lines'       => [
                ['account_code' => '1-201', 'debit' => $amount, 'credit' => 0, 'description' => 'Piutang Pinjaman Anggota'],
                ['account_code' => '1-101', 'debit' => 0, 'credit' => $amount, 'description' => 'Kas Keluar'],
            ],
        ], $orgId, $userId);
    }

    /**
     * Record journal for loan repayment (angsuran)
     * D: Kas/Bank    K: Piutang Anggota (pokok) + Pendapatan Jasa (bunga)
     */
    public function journalLoanRepayment(float $principal, float $interest, int $orgId, int $userId, string $memberName): JournalEntry
    {
        $total = $principal + $interest;
        return $this->createJournal([
            'description' => "Angsuran Pinjaman - {$memberName}",
            'source_type' => 'loan_schedule',
            'lines'       => [
                ['account_code' => '1-101', 'debit' => $total, 'credit' => 0, 'description' => 'Kas Masuk Angsuran'],
                ['account_code' => '1-201', 'debit' => 0, 'credit' => $principal, 'description' => 'Pelunasan Pokok Pinjaman'],
                ['account_code' => '4-101', 'debit' => 0, 'credit' => $interest, 'description' => 'Pendapatan Jasa Pinjaman'],
            ],
        ], $orgId, $userId);
    }

    /**
     * Record journal for payroll deduction (simpanan wajib via gaji)
     * D: Kas/Bank    K: Simpanan Wajib
     */
    public function journalPayrollDeduction(float $amount, int $orgId, int $userId, string $memberName): JournalEntry
    {
        return $this->createJournal([
            'description' => "Potongan Gaji Simpanan Wajib - {$memberName}",
            'source_type' => 'payroll',
            'lines'       => [
                ['account_code' => '1-101', 'debit' => $amount, 'credit' => 0, 'description' => 'Kas Masuk Payroll'],
                ['account_code' => '2-202', 'debit' => 0, 'credit' => $amount, 'description' => 'Simpanan Wajib Anggota'],
            ],
        ], $orgId, $userId);
    }

    /**
     * Seed default Chart of Accounts (COA) for a new organization
     */
    public function seedDefaultCoa(int $orgId): void
    {
        $accounts = [
            // ASET (1)
            ['code' => '1-101', 'name' => 'Kas',                         'type' => 'asset',    'normal_balance' => 'debit'],
            ['code' => '1-102', 'name' => 'Bank',                        'type' => 'asset',    'normal_balance' => 'debit'],
            ['code' => '1-201', 'name' => 'Piutang Pinjaman Anggota',    'type' => 'asset',    'normal_balance' => 'debit'],
            ['code' => '1-202', 'name' => 'Piutang Lain-lain',           'type' => 'asset',    'normal_balance' => 'debit'],
            ['code' => '1-301', 'name' => 'Perlengkapan Kantor',         'type' => 'asset',    'normal_balance' => 'debit'],
            ['code' => '1-401', 'name' => 'Aset Tetap',                  'type' => 'asset',    'normal_balance' => 'debit'],

            // KEWAJIBAN (2)
            ['code' => '2-101', 'name' => 'Simpanan Pokok',              'type' => 'liability','normal_balance' => 'credit'],
            ['code' => '2-201', 'name' => 'Simpanan Sukarela',           'type' => 'liability','normal_balance' => 'credit'],
            ['code' => '2-202', 'name' => 'Simpanan Wajib',              'type' => 'liability','normal_balance' => 'credit'],
            ['code' => '2-301', 'name' => 'Dana SHU Belum Dibagi',       'type' => 'liability','normal_balance' => 'credit'],
            ['code' => '2-401', 'name' => 'Utang Lain-lain',             'type' => 'liability','normal_balance' => 'credit'],

            // MODAL (3)
            ['code' => '3-101', 'name' => 'Modal Koperasi',              'type' => 'equity',   'normal_balance' => 'credit'],
            ['code' => '3-201', 'name' => 'Dana Cadangan',               'type' => 'equity',   'normal_balance' => 'credit'],
            ['code' => '3-301', 'name' => 'SHU Tahun Berjalan',          'type' => 'equity',   'normal_balance' => 'credit'],

            // PENDAPATAN (4)
            ['code' => '4-101', 'name' => 'Pendapatan Jasa Pinjaman',    'type' => 'income',   'normal_balance' => 'credit'],
            ['code' => '4-201', 'name' => 'Pendapatan Lain-lain',        'type' => 'income',   'normal_balance' => 'credit'],

            // BEBAN (5)
            ['code' => '5-101', 'name' => 'Beban Operasional Kantor',    'type' => 'expense',  'normal_balance' => 'debit'],
            ['code' => '5-201', 'name' => 'Beban Pengurus',              'type' => 'expense',  'normal_balance' => 'debit'],
            ['code' => '5-301', 'name' => 'Beban Pendidikan Koperasi',   'type' => 'expense',  'normal_balance' => 'debit'],
            ['code' => '5-401', 'name' => 'Beban Lain-lain',             'type' => 'expense',  'normal_balance' => 'debit'],
        ];

        foreach ($accounts as $account) {
            Account::withoutGlobalScopes()->firstOrCreate(
                ['organization_id' => $orgId, 'code' => $account['code']],
                array_merge($account, ['organization_id' => $orgId, 'is_system' => true])
            );
        }
    }

    private function generateReference(int $orgId): string
    {
        $year  = now()->year;
        $month = now()->format('m');
        $count = JournalEntry::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->whereYear('date', $year)
            ->count() + 1;
        return "JU-{$year}{$month}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
