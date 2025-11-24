<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('presensi:remind')
    ->everyMinute()
    ->withoutOverlapping()
    ->timezone('Asia/Jakarta')
    ->appendOutputTo(storage_path('logs/reminder-masuk.log'));  // ← Output ke file

Schedule::command('presensi:remind-keluar')
    ->everyMinute()
    ->withoutOverlapping()
    ->timezone('Asia/Jakarta')
    ->appendOutputTo(storage_path('logs/reminder-keluar.log'));  // ← Output ke file