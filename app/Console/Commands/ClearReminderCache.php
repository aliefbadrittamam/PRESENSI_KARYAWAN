<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ClearReminderCache extends Command
{
    protected $signature = 'cache:clear-reminder';
    protected $description = 'Clear reminder cache untuk testing ulang';

    public function handle()
    {
        $today = Carbon::today();
        
        $cacheMasuk = 'reminder_masuk_sent_' . $today->format('Y-m-d');
        $cacheKeluar = 'reminder_keluar_sent_' . $today->format('Y-m-d');
        
        Cache::forget($cacheMasuk);
        Cache::forget($cacheKeluar);
        
        $this->info("✅ Cache reminder berhasil dihapus!");
        $this->info("Sekarang bisa test kirim reminder lagi.");
        
        return Command::SUCCESS;
    }
}