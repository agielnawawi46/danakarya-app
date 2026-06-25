<?php

namespace App\Models;

use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShuMemberDetail extends Model
{
    protected $fillable = [
        'shu_distribution_id', 'organization_id', 'user_id',
        'total_simpanan', 'total_bunga_paid', 'jasa_modal',
        'jasa_pinjaman', 'total_shu', 'deposit_id', 'deposited_at',
    ];

    protected $casts = [
        'total_simpanan'   => 'decimal:2',
        'total_bunga_paid' => 'decimal:2',
        'jasa_modal'       => 'decimal:2',
        'jasa_pinjaman'    => 'decimal:2',
        'total_shu'        => 'decimal:2',
        'deposited_at'     => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope());
    }

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(ShuDistribution::class, 'shu_distribution_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }
}
