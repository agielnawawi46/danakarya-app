<?php

namespace App\Models;

use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id', 'user_id', 'amount', 'interest_rate',
        'tenor_months', 'interest_method', 'status', 'purpose',
        'approved_by', 'approved_at', 'disbursed_at', 'credit_score',
        'rejection_reason',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'credit_score'  => 'decimal:2',
        'approved_at'   => 'datetime',
        'disbursed_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(LoanSchedule::class)->orderBy('installment_number');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Helpers
    public function getTotalInterest(): float
    {
        if ($this->interest_method === 'flat') {
            return $this->amount * ($this->interest_rate / 100) * $this->tenor_months;
        }
        // Anuitas: calculated per schedule
        return $this->schedules()->sum('interest_amount');
    }

    public function getTotalRepayment(): float
    {
        return $this->amount + $this->getTotalInterest();
    }

    public function getMonthlyInstallmentFlat(): float
    {
        $monthlyPrincipal = $this->amount / $this->tenor_months;
        $monthlyInterest  = $this->amount * ($this->interest_rate / 100);
        return $monthlyPrincipal + $monthlyInterest;
    }

    public function getRemainingPrincipal(): float
    {
        $paid = $this->schedules()->where('status', 'paid')->sum('principal_amount');
        return $this->amount - $paid;
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            'pending'   => '<span class="badge badge-warning">Menunggu Review</span>',
            'approved'  => '<span class="badge badge-info">Disetujui</span>',
            'active'    => '<span class="badge badge-success">Aktif</span>',
            'completed' => '<span class="badge badge-secondary">Lunas</span>',
            'rejected'  => '<span class="badge badge-danger">Ditolak</span>',
            default     => $this->status,
        };
    }

    public function isEligible(float $salary): bool
    {
        if ($salary <= 0) return false;
        $monthlyInstallment = $this->getMonthlyInstallmentFlat();
        return ($monthlyInstallment / $salary) <= 0.30;
    }
}
