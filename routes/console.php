<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\DailyMaintenanceJob;
use Illuminate\Support\Facades\Schedule;



Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new DailyMaintenanceJob())->dailyAt('00:00')->name('daily-maintenance');

// Schedule::job(new DailyMaintenanceJob)
//     ->daily()
//     ->name('daily-maintenance')
//     ->onOneServer()
//     ->withoutOverlapping();