<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'organization_id', 'user_id', 'action', 'model', 'model_id',
        'old_values', 'new_values', 'description', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabel(): string
    {
        return match ($this->action) {
            'login'                  => 'Login',
            'logout'                 => 'Logout',
            'approved_loan'          => 'Setujui Pinjaman',
            'rejected_loan'          => 'Tolak Pinjaman',
            'processed_deposit'      => 'Proses Setoran',
            'processed_withdrawal'   => 'Proses Penarikan',
            'distributed_shu'        => 'Distribusi SHU',
            'imported_payroll'       => 'Import Payroll',
            'updated_organization'   => 'Update Profil Koperasi',
            'created_member'         => 'Tambah Anggota',
            default                  => ucwords(str_replace('_', ' ', $this->action)),
        };
    }
}
