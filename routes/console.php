<?php

use App\Console\Commands\GenerateMonthlyBilling;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Generate monthly savings billing on the 1st of every month at 00:01
Schedule::command(GenerateMonthlyBilling::class)->monthlyOn(1, '00:01');

