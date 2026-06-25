<?php

namespace App\Models;

use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $fillable = [
        'organization_id', 'reference', 'description',
        'date', 'source_type', 'source_id', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope());
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getTotalDebit(): float
    {
        return $this->lines()->sum('debit');
    }

    public function getTotalCredit(): float
    {
        return $this->lines()->sum('credit');
    }

    public function isBalanced(): bool
    {
        return abs($this->getTotalDebit() - $this->getTotalCredit()) < 0.01;
    }
}

// ───────────────────────────────────────────────────────────────────────────
// JournalEntryLine (kept in same file for simplicity)
// ───────────────────────────────────────────────────────────────────────────
