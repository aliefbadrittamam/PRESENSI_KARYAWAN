<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShiftKerja;
use Carbon\Carbon;

class ResetShiftNormal extends Command
{
    protected $signature = 'shift:reset';
    protected $description = 'Reset shift ke jadwal normal (08:00 - 17:00)';

    public function handle()
    {
        $shift = ShiftKerja::where('status_aktif', 1)->first();
        
        if (!$shift) {
            $this->error('❌ Tidak ada shift aktif!');
            return Command::FAILURE;
        }
        
        // Set ke jam default
        $shift->update([
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '17:00:00',
        ]);
        
        $this->info("✅ Shift berhasil direset!");
        $this->info("🏢 Jam Kerja: 08:00 - 17:00");
        $this->info("📱 Reminder Masuk: 07:30");
        $this->info("📱 Reminder Keluar: 16:30");
        
        return Command::SUCCESS;
    }
}