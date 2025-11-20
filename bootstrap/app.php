<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php', commands: __DIR__ . '/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'cekrole' => CheckRole::class,
            'redirect.role' => \App\Http\Middleware\RedirectIfAuthenticatedByRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {})
    ->withSchedule(function (Schedule $schedule) {
        // Kirim reminder presensi masuk setiap hari jam 08:30
        $schedule->command('presensi:remind')->dailyAt('08:30')->timezone('Asia/Jakarta');

        // Kirim reminder absen keluar setiap hari jam 17:15
        $schedule->command('presensi:remind-keluar')->dailyAt('17:15')->timezone('Asia/Jakarta');

        // Kirim reminder kedua jika masih belum absen (30 menit setelah reminder pertama)
        $schedule->command('presensi:remind')->dailyAt('09:00')->timezone('Asia/Jakarta');
    })
    ->create();
