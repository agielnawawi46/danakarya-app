<?php

namespace App\Models;

use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanSchedule extends Model
{
    protected $fillable = [
        'loan_id', 'organization_id', 'user_id', 'installment_number',
        'due_date', 'principal_amount', 'interest_amount', 'total_amount',
        'paid_amount', 'remaining_balance', 'status', 'paid_at', 'processed_by',
    ];

    protected $casts = [
        'principal_amount'  => 'decimal:2',
        'interest_amount'   => 'decimal:2',
        'total_amount'      => 'decimal:2',
        'paid_amount'       => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'due_date'          => 'date',
        'paid_at'           => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope());
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'pending')->where('due_date', '<', now()->toDateString());
            });
    }

    // Helpers
    public function isOverdue(): bool
    {
        return $this->status === 'overdue' ||
            ($this->status === 'pending' && $this->due_date->isPast());
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            'paid'    => '<span class="badge badge-success">Lunas</span>',
            'pending' => $this->isOverdue()
                ? '<span class="badge badge-danger">Telat</span>'
                : '<span class="badge badge-warning">Belum Bayar</span>',
            'overdue' => '<span class="badge badge-danger">Telat</span>',
            'partial' => '<span class="badge badge-info">Bayar Sebagian</span>',
            default   => $this->status,
        };
    }
}
