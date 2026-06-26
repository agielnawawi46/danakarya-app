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
    public function calculateCreditScore(float $amount, float $interestRate, int $tenor, float $salary): array
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

        $monthlyPrincipal = $amount / $tenor;
        $monthlyInterest  = $amount * ($interestRate / 100);
        $monthlyTotal     = $monthlyPrincipal + $monthlyInterest;
        $score            = ($monthlyTotal / $salary) * 100;
        $maxAllowed       = $salary * 0.30;

        return [
            'score'       => round($score, 2),
            'eligible'    => $score <= 30,
            'monthly'     => $monthlyTotal,
            'max_allowed' => $maxAllowed,
            'reason'      => $score > 30
                ? "Angsuran bulanan (Rp " . number_format($monthlyTotal, 0, ',', '.') . ") melebihi 30% gaji."
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
        $monthlyPrincipal = round($loan->amount / $loan->tenor_months);
        $monthlyInterest  = round($loan->amount * ($loan->interest_rate / 100));
        
        $remainingPrincipal = $loan->amount;

        for ($i = 1; $i <= $loan->tenor_months; $i++) {
            $dueDate = $startDate->copy()->addMonths($i - 1);
            
            $principal = $monthlyPrincipal;
            $interest  = $monthlyInterest;
            
            // Adjust the last installment to ensure the total principal matches exactly
            if ($i === $loan->tenor_months) {
                $principal = $remainingPrincipal;
            }
            
            $total = $principal + $interest;
            $remainingPrincipal -= $principal;

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
            $interest  = round($balance * $r);
            
            if ($i === $n) {
                // Last installment: principal is exactly the remaining balance
                $principal = round($balance);
                $total     = $principal + $interest;
            } else {
                $total     = round($annuity);
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
