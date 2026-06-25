<?php

namespace App\Models;

use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShuDistribution extends Model
{
    protected $fillable = [
        'organization_id', 'year', 'total_profit',
        'total_dana_cadangan', 'total_anggota', 'total_pengurus',
        'total_karyawan', 'total_pendidikan', 'total_jasa_modal',
        'total_jasa_pinjaman', 'status', 'approved_by', 'distributed_at',
    ];

    protected $casts = [
        'total_profit'       => 'decimal:2',
        'total_dana_cadangan'=> 'decimal:2',
        'total_anggota'      => 'decimal:2',
        'total_pengurus'     => 'decimal:2',
        'total_karyawan'     => 'decimal:2',
        'total_pendidikan'   => 'decimal:2',
        'total_jasa_modal'   => 'decimal:2',
        'total_jasa_pinjaman'=> 'decimal:2',
        'distributed_at'     => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope());
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function memberDetails(): HasMany
    {
        return $this->hasMany(ShuMemberDetail::class);
    }
}
