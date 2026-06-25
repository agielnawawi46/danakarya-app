<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'name', 'legal_name', 'address', 'phone', 'email',
        'legal_number', 'logo', 'is_configured', 'is_active',
        'simpanan_pokok', 'simpanan_wajib', 'loan_interest_rate',
        'loan_max_tenor', 'loan_max_plafon', 'loan_interest_method',
        'shu_dana_cadangan_pct', 'shu_anggota_pct', 'shu_pengurus_pct',
        'shu_karyawan_pct', 'shu_pendidikan_pct',
    ];

    protected $casts = [
        'is_configured'      => 'boolean',
        'is_active'          => 'boolean',
        'simpanan_pokok'     => 'decimal:2',
        'simpanan_wajib'     => 'decimal:2',
        'loan_interest_rate' => 'decimal:2',
        'loan_max_plafon'    => 'decimal:2',
        'shu_dana_cadangan_pct' => 'decimal:2',
        'shu_anggota_pct'    => 'decimal:2',
        'shu_pengurus_pct'   => 'decimal:2',
        'shu_karyawan_pct'   => 'decimal:2',
        'shu_pendidikan_pct' => 'decimal:2',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function shuDistributions(): HasMany
    {
        return $this->hasMany(ShuDistribution::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isConfigured(): bool
    {
        return $this->is_configured && $this->name !== 'Koperasi Baru (Belum Dikonfigurasi)';
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('images/default-logo.png');
    }

    /**
     * Calculate maximum loan installment per month for a given salary
     */
    public function getMaxMonthlyInstallment(float $salary): float
    {
        return $salary * 0.30;
    }
}
