<?php

use App\Console\Commands\GenerateMonthlyBilling;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Generate monthly savings billing every day at 00:01 (logic filters by deposit_date inside command)
Schedule::command(GenerateMonthlyBilling::class)->dailyAt('00:01');

// ─── Backup Scheduling ───────────────────────────────────────────────────────
// Run database backup every day at 02:00
Schedule::command('backup:run --only-db')->dailyAt('02:00')->onFailure(function () {
    \Illuminate\Support\Facades\Log::error('Daily database backup FAILED.');
});

// Clean up old backups every Sunday at 01:00
Schedule::command('backup:clean')->weeklyOn(0, '01:00');
