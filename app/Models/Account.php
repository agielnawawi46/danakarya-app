<?php

namespace App\Models;

use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'organization_id', 'code', 'name', 'type',
        'normal_balance', 'parent_id', 'is_system', 'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope());
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getBalance(): float
    {
        $debit  = $this->journalLines()->sum('debit');
        $credit = $this->journalLines()->sum('credit');

        if ($this->normal_balance === 'debit') {
            return $debit - $credit;
        }
        return $credit - $debit;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'asset'    => 'Aset',
            'liability'=> 'Kewajiban',
            'equity'   => 'Modal',
            'income'   => 'Pendapatan',
            'expense'  => 'Beban',
            default    => ucfirst($this->type),
        };
    }
}
