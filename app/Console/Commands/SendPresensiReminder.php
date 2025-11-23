<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use App\Jobs\SendWhatsAppNotification;
use Carbon\Carbon;

class SendPresensiReminder extends Command
{
    protected $signature = 'presensi:remind {--force : Force send reminder regardless of time}';
    protected $description = 'Kirim pengingat presensi 30 menit sebelum jam masuk';

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

        // Parse jam mulai - bisa datetime atau time
        $jamMulai = Carbon::parse($shift->jam_mulai);
        
        // Set ke hari ini jika parsing menghasilkan tanggal berbeda
        $jamMulaiHariIni = Carbon::today()->setTimeFrom($jamMulai);
        $waktuReminder = $jamMulaiHariIni->copy()->subMinutes(30);
        
        // Log untuk debugging
        $this->info("Shift: {$shift->nama_shift}");
        $this->info("Jam Mulai: {$jamMulaiHariIni->format('H:i')}");
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

        // Get karyawan yang belum absen hari ini
        $karyawanBelumAbsen = Karyawan::where('status_aktif', 1)
            ->whereDoesntHave('presensi', function ($query) use ($today) {
                $query->where('tanggal_presensi', $today)
                      ->whereNotNull('jam_masuk');
            })
            ->get();

        if ($karyawanBelumAbsen->count() === 0) {
            $this->info('✓ Semua karyawan sudah absen masuk.');
            return Command::SUCCESS;
        }

        $this->info("📋 Ditemukan {$karyawanBelumAbsen->count()} karyawan yang belum absen.");
        
        $successCount = 0;
        $failedCount = 0;

        foreach ($karyawanBelumAbsen as $karyawan) {
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
                    'presensi_belum_absen',
                    [
                        'nama' => $karyawan->nama_lengkap,
                        'tanggal' => $today->format('d F Y'),
                        'waktu' => $now->format('H:i'),
                        'jam_mulai' => $jamMulaiHariIni->format('H:i'),
                        'shift' => $shift->nama_shift . ' (' . $jamMulaiHariIni->format('H:i') . ' - ' . Carbon::parse($shift->jam_selesai)->format('H:i') . ')',
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