<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollImport extends Model
{
    protected $fillable = [
        'organization_id', 'period_month', 'period_year', 'file_path',
        'status', 'processed_count', 'success_count', 'failed_count',
        'total_amount', 'notes', 'processed_by', 'processed_at',
    ];

    protected $casts = [
        'total_amount'  => 'decimal:2',
        'processed_at'  => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getPeriodLabel(): string
    {
        $months = [
            1  => 'Januari', 2  => 'Februari', 3  => 'Maret',
            4  => 'April',   5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',    8  => 'Agustus',   9  => 'September',
            10 => 'Oktober', 11 => 'November',  12 => 'Desember',
        ];
        return ($months[$this->period_month] ?? $this->period_month) . ' ' . $this->period_year;
    }
}
