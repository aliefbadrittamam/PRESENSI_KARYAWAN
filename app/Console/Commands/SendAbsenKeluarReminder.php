<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use App\Jobs\SendWhatsAppNotification;
use Carbon\Carbon;

class SendAbsenKeluarReminder extends Command
{
    protected $signature = 'presensi:remind-keluar {--force : Force send reminder regardless of time}';
    protected $description = 'Kirim pengingat absen keluar 30 menit sebelum jam selesai';

    public function handle()
    {
        $today = Carbon::today();
        $now = Carbon::now();
        
        // Get active shift
        $shift = ShiftKerja::where('status_aktif', 1)->first();
        
        if (!$shift) {
            $this->error('Tidak ada shift aktif!');
            return Command::FAILURE;
        }

        // Parse jam selesai - bisa datetime atau time
        $jamSelesai = Carbon::parse($shift->jam_selesai);
        
        // Set ke hari ini jika parsing menghasilkan tanggal berbeda
        $jamSelesaiHariIni = Carbon::today()->setTimeFrom($jamSelesai);
        $waktuReminder = $jamSelesaiHariIni->copy()->subMinutes(30);
        
        // Log untuk debugging
        $this->info("Shift: {$shift->nama_shift}");
        $this->info("Jam Selesai: {$jamSelesaiHariIni->format('H:i')}");
        $this->info("Waktu Reminder: {$waktuReminder->format('H:i')}");
        $this->info("Waktu Sekarang: {$now->format('H:i')}");
        
        // Cek apakah sekarang adalah waktu reminder (toleransi 2 menit)
        if (!$this->option('force')) {
            $selisihMenit = abs($now->diffInMinutes($waktuReminder));
            
            if ($selisihMenit > 2) {
                $this->info("❌ Belum waktunya mengirim reminder.");
                $this->info("Reminder akan dikirim pada: {$waktuReminder->format('H:i')}");
                return Command::SUCCESS;
            }
        }

        // Get presensi yang sudah absen masuk tapi belum keluar
        $presensiList = Presensi::with('karyawan')
            ->where('tanggal_presensi', $today)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->get();

        if ($presensiList->count() === 0) {
            $this->info('✓ Semua karyawan sudah absen keluar.');
            return Command::SUCCESS;
        }

        $this->info("📋 Ditemukan {$presensiList->count()} karyawan yang belum absen keluar.");
        
        $successCount = 0;
        $failedCount = 0;

        foreach ($presensiList as $presensi) {
            $karyawan = $presensi->karyawan;
            
            // Skip jika nomor telepon tidak ada
            if (empty($karyawan->nomor_telepon)) {
                $this->warn("⚠️  {$karyawan->nama_lengkap} - Tidak ada nomor telepon");
                $failedCount++;
                continue;
            }

            try {
                // Kirim WA reminder
                SendWhatsAppNotification::dispatch(
                    $karyawan->nomor_telepon,
                    'presensi_belum_absen_keluar',
                    [
                        'nama' => $karyawan->nama_lengkap,
                        'tanggal' => $today->format('d F Y'),
                        'jam_masuk' => $presensi->jam_masuk,
                        'jam_selesai' => $jamSelesaiHariIni->format('H:i'),
                    ]
                );

                $this->info("✓ {$karyawan->nama_lengkap} ({$karyawan->nomor_telepon})");
                $successCount++;
            } catch (\Exception $e) {
                $this->error("✗ {$karyawan->nama_lengkap} - Error: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->newLine();
        $this->info("📊 Ringkasan:");
        $this->info("✓ Berhasil: {$successCount}");
        if ($failedCount > 0) {
            $this->warn("✗ Gagal: {$failedCount}");
        }
        
        return Command::SUCCESS;
    }
}