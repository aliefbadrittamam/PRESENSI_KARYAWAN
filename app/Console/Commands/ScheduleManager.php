<?php

namespace App\Console;

use App\Models\ShiftKerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;

class ScheduleManager
{
    public static function registerShiftReminders()
    {
        $shift = ShiftKerja::where('status_aktif', 1)->first();
        
        if (!$shift) {
            return;
        }

        // Parse jam dari database (bisa datetime atau time)
        $jamMulai = Carbon::parse($shift->jam_mulai);
        $jamSelesai = Carbon::parse($shift->jam_selesai);
        
        // Hitung waktu reminder (30 menit sebelum)
        $waktuReminderMasuk = $jamMulai->copy()->subMinutes(30)->format('H:i');
        $waktuReminderKeluar = $jamSelesai->copy()->subMinutes(30)->format('H:i');

        // Schedule reminder masuk
        Schedule::command('presensi:remind --force')
            ->dailyAt($waktuReminderMasuk)
            ->timezone('Asia/Jakarta')
            ->description("Reminder absen masuk - {$shift->nama_shift}");

        // Schedule reminder keluar
        Schedule::command('presensi:remind-keluar --force')
            ->dailyAt($waktuReminderKeluar)
            ->timezone('Asia/Jakarta')
            ->description("Reminder absen keluar - {$shift->nama_shift}");
    }
}