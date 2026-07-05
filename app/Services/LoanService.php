<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoanService
{
    public function __construct(
        private readonly AccountingService $accountingService,
        private readonly AuditService $auditService,
    ) {}

    /**
     * Calculate credit score (installment % of salary)
     * Returns the percentage of salary this loan would consume monthly.
     */
    public function calculateCreditScore(float $amount, float $interestRate, int $tenor, float $salary, \App\Models\Organization $org): array
    {
        if ($salary <= 0) {
            return [
                'score'      => 100,
                'eligible'   => false,
                'monthly'    => 0,
                'max_allowed'=> 0,
                'reason'     => 'Gaji tidak terdaftar di sistem.',
            ];
        }

        $maxPct = $org->max_loan_salary_pct ?? 30;

        $monthlyPrincipal = $amount / $tenor;
        $monthlyInterest  = $amount * ($interestRate / 100);
        $monthlyTotal     = $monthlyPrincipal + $monthlyInterest;
        $score            = ($monthlyTotal / $salary) * 100;
        $maxAllowed       = $salary * ($maxPct / 100);

        return [
            'score'       => round($score, 2),
            'eligible'    => $score <= $maxPct,
            'monthly'     => $monthlyTotal,
            'max_allowed' => $maxAllowed,
            'reason'      => $score > $maxPct
                ? "Angsuran bulanan (Rp " . number_format($monthlyTotal, 0, ',', '.') . ") melebihi {$maxPct}% gaji."
                : "Layak. Angsuran " . round($score, 1) . "% dari gaji.",
        ];
    }

    /**
     * Generate installment schedule (Flat or Annuity method)
     */
    public function generateSchedule(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            // Delete existing schedules if regenerating
            LoanSchedule::withoutGlobalScopes()->where('loan_id', $loan->id)->delete();

            $startDate = now()->addMonth()->startOfMonth();

            if ($loan->interest_method === 'flat') {
                $this->generateFlatSchedule($loan, $startDate);
            } else {
                $this->generateAnnuitySchedule($loan, $startDate);
            }
        });
    }

    private function generateFlatSchedule(Loan $loan, \Carbon\Carbon $startDate): void
    {
        // Round down to nearest 1000 to be cash-friendly (dibulatkan ke bawah)
        $monthlyPrincipal = floor(($loan->amount / $loan->tenor_months) / 1000) * 1000;
        $monthlyInterest  = floor(($loan->amount * ($loan->interest_rate / 100)) / 1000) * 1000;
        
        $remainingPrincipal = $loan->amount;
        $remainingInterest  = round($loan->amount * ($loan->interest_rate / 100) * $loan->tenor_months);

        for ($i = 1; $i <= $loan->tenor_months; $i++) {
            $dueDate = $startDate->copy()->addMonths($i - 1);
            
            if ($i === $loan->tenor_months) {
                // Adjust the last installment to ensure the total matches exactly
                $principal = $remainingPrincipal;
                $interest  = $remainingInterest;
            } else {
                $principal = $monthlyPrincipal;
                $interest  = $monthlyInterest;
            }
            
            $total = $principal + $interest;
            $remainingPrincipal -= $principal;
            $remainingInterest  -= $interest;

            LoanSchedule::create([
                'loan_id'           => $loan->id,
                'organization_id'   => $loan->organization_id,
                'user_id'           => $loan->user_id,
                'installment_number'=> $i,
                'due_date'          => $dueDate->toDateString(),
                'principal_amount'  => $principal,
                'interest_amount'   => $interest,
                'total_amount'      => $total,
                'remaining_balance' => max(0, $remainingPrincipal),
                'status'            => 'pending',
            ]);
        }
    }

    private function generateAnnuitySchedule(Loan $loan, \Carbon\Carbon $startDate): void
    {
        $r     = $loan->interest_rate / 100; // monthly rate
        $n     = $loan->tenor_months;
        $p     = $loan->amount;

        // Annuity formula: A = P * r(1+r)^n / ((1+r)^n - 1)
        $annuity = $p * ($r * pow(1 + $r, $n)) / (pow(1 + $r, $n) - 1);
        $balance = $p;

        for ($i = 1; $i <= $n; $i++) {
            $exactInterest = $balance * $r;
            
            if ($i === $n) {
                // Last installment: exact remainder
                $principal = round($balance);
                $interest  = round($exactInterest);
                $total     = $principal + $interest;
            } else {
                // Round down to nearest 1000 for cash-friendly
                $total     = floor($annuity / 1000) * 1000;
                $interest  = floor($exactInterest / 1000) * 1000;
                $principal = $total - $interest;
            }
            
            $balance -= $principal;
            $dueDate  = $startDate->copy()->addMonths($i - 1);

            LoanSchedule::create([
                'loan_id'           => $loan->id,
                'organization_id'   => $loan->organization_id,
                'user_id'           => $loan->user_id,
                'installment_number'=> $i,
                'due_date'          => $dueDate->toDateString(),
                'principal_amount'  => $principal,
                'interest_amount'   => $interest,
                'total_amount'      => $total,
                'remaining_balance' => max(0, $balance),
                'status'            => 'pending',
            ]);
        }
    }

    /**
     * Approve a loan application and generate schedule + journal
     */
    public function approveLoan(Loan $loan, int $approvedBy): Loan
    {
        return DB::transaction(function () use ($loan, $approvedBy) {
            $approver = User::find($approvedBy);

            $loan->update([
                'status'      => 'active',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
                'disbursed_at'=> now(),
            ]);

            // Generate installment schedule
            $this->generateSchedule($loan);

            // Create disbursement journal entry
            $this->accountingService->journalLoanDisbursement(
                $loan->amount,
                $loan->organization_id,
                $approvedBy,
                $loan->user->name
            );

            // Audit trail
            $this->auditService->log(
                action: 'approved_loan',
                model: 'Loan',
                modelId: $loan->id,
                description: "Pinjaman disetujui oleh {$approver->name}. Jumlah: Rp " . number_format($loan->amount, 0, ',', '.'),
                newValues: ['status' => 'active', 'approved_by' => $approvedBy],
                organizationId: $loan->organization_id,
                userId: $approvedBy,
            );

            return $loan->fresh();
        });
    }

    /**
     * Reject a loan application
     */
    public function rejectLoan(Loan $loan, int $rejectedBy, string $reason): Loan
    {
        return DB::transaction(function () use ($loan, $rejectedBy, $reason) {
            $loan->update([
                'status'           => 'rejected',
                'approved_by'      => $rejectedBy,
                'approved_at'      => now(),
                'rejection_reason' => $reason,
            ]);

            $this->auditService->log(
                action: 'rejected_loan',
                model: 'Loan',
                modelId: $loan->id,
                description: "Pinjaman ditolak. Alasan: {$reason}",
                newValues: ['status' => 'rejected', 'reason' => $reason],
                organizationId: $loan->organization_id,
                userId: $rejectedBy,
            );

            return $loan->fresh();
        });
    }

    /**
     * Process loan installment payment
     */
    public function payInstallment(LoanSchedule $schedule, int $processedBy): LoanSchedule
    {
        return DB::transaction(function () use ($schedule, $processedBy) {
            $schedule->update([
                'status'       => 'paid',
                'paid_amount'  => $schedule->total_amount,
                'paid_at'      => now(),
                'processed_by' => $processedBy,
            ]);

            // Journal: angsuran masuk
            $this->accountingService->journalLoanRepayment(
                $schedule->principal_amount,
                $schedule->interest_amount,
                $schedule->organization_id,
                $processedBy,
                $schedule->user->name
            );

            // Check if all installments paid → mark loan as completed
            $pendingCount = LoanSchedule::withoutGlobalScopes()
                ->where('loan_id', $schedule->loan_id)
                ->where('status', '!=', 'paid')
                ->count();

            if ($pendingCount === 0) {
                Loan::withoutGlobalScopes()->where('id', $schedule->loan_id)->update(['status' => 'completed']);
            }

            $this->auditService->log(
                action: 'paid_installment',
                model: 'LoanSchedule',
                modelId: $schedule->id,
                description: "Angsuran ke-{$schedule->installment_number} dibayar.",
                organizationId: $schedule->organization_id,
                userId: $processedBy,
            );

            return $schedule->fresh();
        });
    }
}
