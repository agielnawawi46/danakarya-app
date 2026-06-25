<?php

namespace App\Models;

use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deposit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id', 'user_id', 'type', 'amount',
        'status', 'notes', 'processed_by', 'period_month',
        'period_year', 'transaction_type',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
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

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function shuMemberDetail(): HasMany
    {
        return $this->hasMany(ShuMemberDetail::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCredit($query)
    {
        return $query->where('transaction_type', 'credit');
    }

    public function scopeDebit($query)
    {
        return $query->where('transaction_type', 'debit');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopePeriod($query, int $month, int $year)
    {
        return $query->where('period_month', $month)->where('period_year', $year);
    }

    // Helpers
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'pokok'    => 'Simpanan Pokok',
            'wajib'    => 'Simpanan Wajib',
            'sukarela' => 'Simpanan Sukarela',
            default    => ucfirst($this->type),
        };
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            'completed' => '<span class="badge badge-success">Selesai</span>',
            'pending'   => '<span class="badge badge-warning">Menunggu</span>',
            'rejected'  => '<span class="badge badge-danger">Ditolak</span>',
            default     => $this->status,
        };
    }
}
