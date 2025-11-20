<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';
    protected $primaryKey = 'id_presensi';
    public $timestamps = true;

    protected $fillable = [
        'id_karyawan',
        'id_shift',
        'tanggal_presensi',
        'jam_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'alamat_masuk',
        'accuracy_masuk',
        'face_id_data_masuk',
        'confidence_score_masuk',
        'foto_masuk',
        'foto_thumbnail_masuk',
        'jam_keluar',
        'latitude_keluar',
        'longitude_keluar',
        'alamat_keluar',
        'accuracy_keluar',
        'face_id_data_keluar',
        'confidence_score_keluar',
        'foto_keluar',
        'foto_thumbnail_keluar',
        'status_kehadiran',
        'status_verifikasi',
        'alasan_reject',
        'keterlambatan_menit',
        'total_jam_kerja',
        'catatan'
    ];

    protected $casts = [
        'tanggal_presensi' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_keluar' => 'decimal:8',
        'longitude_keluar' => 'decimal:8',
        'accuracy_masuk' => 'decimal:2',
        'accuracy_keluar' => 'decimal:2',
        'confidence_score_masuk' => 'decimal:2',
        'confidence_score_keluar' => 'decimal:2',
        'total_jam_kerja' => 'float',
        'keterlambatan_menit' => 'integer',
        'status_kehadiran' => 'string'
    ];

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    // Relasi ke Shift Kerja
    public function shift()
    {
        return $this->belongsTo(ShiftKerja::class, 'id_shift');
    }

    // Scope untuk presensi hari ini
    public function scopeHariIni($query, $karyawanId = null)
    {
        $query->where('tanggal_presensi', today());
        
        if ($karyawanId) {
            $query->where('id_karyawan', $karyawanId);
        }
        
        return $query;
    }

    // Scope untuk presensi bulan ini
    public function scopeBulanIni($query, $karyawanId = null)
    {
        $query->whereMonth('tanggal_presensi', now()->month)
              ->whereYear('tanggal_presensi', now()->year);
        
        if ($karyawanId) {
            $query->where('id_karyawan', $karyawanId);
        }
        
        return $query;
    }

    /**
     * ✅ ENHANCEMENT: Hitung total jam kerja dengan Optimistic Locking
     * Menggunakan updated_at sebagai version control
     */
    public function hitungTotalJamKerja(): void
    {
        if ($this->jam_masuk && $this->jam_keluar) {
            try {
                // ✅ ENHANCEMENT: Reload fresh data dengan lock
                $fresh = self::where('id_presensi', $this->id_presensi)
                    ->lockForUpdate() // 🔒 Pessimistic lock untuk prevent concurrent update
                    ->first();

                if (!$fresh) {
                    Log::warning("Presensi {$this->id_presensi} not found for hitungTotalJamKerja");
                    return;
                }

                $masuk = \Carbon\Carbon::parse($fresh->jam_masuk);
                $keluar = \Carbon\Carbon::parse($fresh->jam_keluar);

                // Handle shift malam (melewati tengah malam)
                if ($keluar < $masuk) {
                    $keluar->addDay();
                }

                // Hitung total jam kerja (float, 2 desimal)
                $totalJamKerja = round($masuk->diffInMinutes($keluar) / 60, 2);

                // ✅ Update dengan optimistic locking check
                $affected = self::where('id_presensi', $this->id_presensi)
                    ->where('updated_at', $fresh->updated_at) // 🔒 Optimistic lock
                    ->update([
                        'total_jam_kerja' => $totalJamKerja,
                        'updated_at' => now() // Force update timestamp
                    ]);

                if ($affected === 0) {
                    // Data berubah oleh proses lain, retry sekali
                    Log::info("Optimistic lock failed for presensi {$this->id_presensi}, retrying...");
                    
                    // Retry once
                    $fresh = self::where('id_presensi', $this->id_presensi)
                        ->lockForUpdate()
                        ->first();

                    if ($fresh) {
                        $masuk = \Carbon\Carbon::parse($fresh->jam_masuk);
                        $keluar = \Carbon\Carbon::parse($fresh->jam_keluar);

                        if ($keluar < $masuk) {
                            $keluar->addDay();
                        }

                        $totalJamKerja = round($masuk->diffInMinutes($keluar) / 60, 2);

                        $fresh->update([
                            'total_jam_kerja' => $totalJamKerja
                        ]);
                    }
                }

                // Refresh current instance
                $this->refresh();
                
            } catch (\Exception $e) {
                Log::error("Error hitungTotalJamKerja for presensi {$this->id_presensi}: " . $e->getMessage());
            }
        }
    }

    /**
     * ✅ ENHANCEMENT: Safe update method dengan retry mechanism
     */
    public function safeUpdate(array $attributes, int $maxRetries = 3): bool
    {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                // Reload fresh data dengan lock
                $fresh = self::where('id_presensi', $this->id_presensi)
                    ->lockForUpdate()
                    ->first();

                if (!$fresh) {
                    return false;
                }

                // Update dengan optimistic lock check
                $affected = self::where('id_presensi', $this->id_presensi)
                    ->where('updated_at', $fresh->updated_at)
                    ->update(array_merge($attributes, [
                        'updated_at' => now()
                    ]));

                if ($affected > 0) {
                    $this->refresh();
                    return true;
                }

                // Retry jika optimistic lock failed
                if ($attempt < $maxRetries) {
                    usleep(100000); // Sleep 100ms before retry
                    continue;
                }

                return false;
            } catch (\Exception $e) {
                Log::error("Error in safeUpdate attempt {$attempt}: " . $e->getMessage());
                
                if ($attempt === $maxRetries) {
                    throw $e;
                }
                
                usleep(100000);
            }
        }

        return false;
    }

    // Method untuk cek apakah presensi masuk valid
    public function isPresensiMasukValid()
    {
        return $this->confidence_score_masuk >= 0.75 && $this->accuracy_masuk <= 50;
    }

    // Method untuk cek apakah presensi keluar valid
    public function isPresensiKeluarValid()
    {
        return $this->confidence_score_keluar >= 0.75 && $this->accuracy_keluar <= 50;
    }

    // Accessor untuk status kehadiran dengan warna
    public function getStatusKehadiranColorAttribute()
    {
        return match($this->status_kehadiran) {
            'hadir' => 'success',
            'terlambat' => 'warning',
            'izin' => 'info',
            'sakit' => 'primary',
            'cuti' => 'secondary',
            'alpha' => 'danger',
            default => 'dark'
        };
    }
}