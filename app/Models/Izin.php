<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Izin extends Model
{
    use HasFactory;

    protected $table = 'izin';
    protected $primaryKey = 'id_izin';

    protected $fillable = [
        'id_karyawan',
        'tipe_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'keterangan',
        'file_pendukung',
        'status_approval',
        'tanggal_pengajuan',
        'tanggal_approval',
        'approved_by',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_pengajuan' => 'datetime',
        'tanggal_approval' => 'datetime',
    ];

    /**
     * Relationship dengan Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan')->withTrashed();
    }

    /**
     * Relationship dengan User yang approve
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * Accessor untuk format tanggal Indonesia
     */
    public function getTanggalMulaiFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_mulai)->isoFormat('DD MMMM YYYY');
    }

    public function getTanggalSelesaiFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_selesai)->isoFormat('DD MMMM YYYY');
    }

    /**
     * Accessor untuk status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning text-white"><i class="fas fa-clock me-1"></i>  Menunggu</span>',
            'approved' => '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Disetujui</span>',
            'rejected' => '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>',
        ];

        return $badges[$this->status_approval] ?? $badges['pending'];
    }

    /**
     * Accessor untuk tipe izin badge HTML
     */
    public function getTipeIzinBadgeAttribute()
    {
        $badges = [
            'izin' => '<span class="badge bg-info"><i class="fas fa-calendar-alt me-1"></i>  Izin</span>',
            'sakit' => '<span class="badge bg-danger"><i class="fas fa-hospital me-1"></i>Sakit</span>',
        ];

        return $badges[$this->tipe_izin] ?? $badges['izin'];
    }

    /**
     * Accessor untuk tipe izin text
     */
    public function getTipeIzinTextAttribute()
    {
        return $this->tipe_izin === 'sakit' ? 'Sakit' : 'Izin';
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status_approval', $status);
    }

    /**
     * Scope untuk filter berdasarkan tipe
     */
    public function scopeTipe($query, $tipe)
    {
        return $query->where('tipe_izin', $tipe);
    }

    /**
     * Scope untuk izin pending
     */
    public function scopePending($query)
    {
        return $query->where('status_approval', 'pending');
    }
  public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
    /**
     * Scope untuk izin approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_approval', 'approved');
    }
}