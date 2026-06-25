<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'organization_id',
        'employee_id', 'department', 'salary', 'phone',
        'join_date', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'join_date'         => 'date',
        'salary'            => 'encrypted', // column-level encryption
    ];

    protected string $guard_name = 'web';

    // ─── Relationships ────────────────────────────────────────────────────

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function loanSchedules(): HasMany
    {
        return $this->hasMany(LoanSchedule::class);
    }

    public function shuDetails(): HasMany
    {
        return $this->hasMany(ShuMemberDetail::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ─── Role Helpers ─────────────────────────────────────────────────────

    public function isSuperadmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPengurus(): bool
    {
        return $this->hasRole('pengurus');
    }

    public function isPengawas(): bool
    {
        return $this->hasRole('pengawas');
    }

    public function isAnggota(): bool
    {
        return $this->hasRole('anggota');
    }

    public function getPrimaryRole(): string
    {
        if ($this->isSuperadmin()) return 'superadmin';
        if ($this->isAdmin())      return 'admin';
        if ($this->isPengurus())   return 'pengurus';
        if ($this->isPengawas())   return 'pengawas';
        if ($this->isAnggota())    return 'anggota';
        return 'guest';
    }

    public function getDashboardRoute(): string
    {
        return match ($this->getPrimaryRole()) {
            'superadmin' => route('superadmin.dashboard'),
            'admin'      => route('admin.dashboard'),
            'pengurus'   => route('pengurus.dashboard'),
            'pengawas'   => route('pengawas.dashboard'),
            'anggota'    => route('member.dashboard'),
            default      => '/',
        };
    }

    // ─── Financial Helpers ────────────────────────────────────────────────

    public function getSalaryDecrypted(): float
    {
        // salary is auto-decrypted via encrypted cast
        return (float) ($this->salary ?? 0);
    }

    public function getTotalSimpanan(): float
    {
        return (float) $this->deposits()
            ->where('status', 'completed')
            ->where('transaction_type', 'credit')
            ->sum('amount')
            - (float) $this->deposits()
            ->where('status', 'completed')
            ->where('transaction_type', 'debit')
            ->sum('amount');
    }

    public function getTotalSimpananByType(string $type): float
    {
        $credit = (float) $this->deposits()
            ->where('type', $type)
            ->where('status', 'completed')
            ->where('transaction_type', 'credit')
            ->sum('amount');
        $debit = (float) $this->deposits()
            ->where('type', $type)
            ->where('status', 'completed')
            ->where('transaction_type', 'debit')
            ->sum('amount');
        return $credit - $debit;
    }

    public function getActiveLoans()
    {
        return $this->loans()->where('status', 'active')->get();
    }

    public function hasActiveLoan(): bool
    {
        return $this->loans()->where('status', 'active')->exists();
    }

    public function getRoleLabel(): string
    {
        return match ($this->getPrimaryRole()) {
            'superadmin' => 'Superadmin',
            'admin'      => 'Admin Koperasi',
            'pengurus'   => 'Pengurus',
            'pengawas'   => 'Pengawas',
            'anggota'    => 'Anggota',
            default      => 'Pengguna',
        };
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            'active'    => '<span class="badge badge-success">Aktif</span>',
            'inactive'  => '<span class="badge badge-secondary">Tidak Aktif</span>',
            'suspended' => '<span class="badge badge-danger">Ditangguhkan</span>',
            default     => $this->status,
        };
    }
}
