<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti';
    protected $primaryKey = 'id_cuti';

    protected $fillable = ['id_karyawan', 'jenis_cuti', 'tanggal_mulai', 'tanggal_selesai', 'jumlah_hari', 'keterangan', 'file_pendukung', 'status_approval', 'tanggal_pengajuan', 'tanggal_approval', 'approved_by', 'alasan_penolakan', 'sisa_cuti_tahunan'];

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
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
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
            'pending' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>',
            'approved' => '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Disetujui</span>',
            'rejected' => '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>',
        ];

        return $badges[$this->status_approval] ?? $badges['pending'];
    }

    /**
     * Accessor untuk jenis cuti badge HTML
     */
    public function getJenisCutiBadgeAttribute()
    {
        $badges = [
            'tahunan' => '<span class="badge bg-primary"><i class="fas fa-calendar me-1"></i>Cuti Tahunan</span>',
            'sakit' => '<span class="badge bg-danger"><i class="fas fa-hospital me-1"></i>Cuti Sakit</span>',
            'melahirkan' => '<span class="badge bg-info"><i class="fas fa-baby me-1"></i>Cuti Melahirkan</span>',
            'menikah' => '<span class="badge bg-success"><i class="fas fa-ring me-1"></i>Cuti Menikah</span>',
            'khusus' => '<span class="badge bg-secondary"><i class="fas fa-star me-1"></i>Cuti Khusus</span>',
        ];

        return $badges[$this->jenis_cuti] ?? $badges['tahunan'];
    }

    /**
     * Accessor untuk jenis cuti text
     */
    public function getJenisCutiTextAttribute()
    {
        $labels = [
            'tahunan' => 'Cuti Tahunan',
            'sakit' => 'Cuti Sakit',
            'melahirkan' => 'Cuti Melahirkan',
            'menikah' => 'Cuti Menikah',
            'khusus' => 'Cuti Khusus',
        ];

        return $labels[$this->jenis_cuti] ?? 'Cuti Tahunan';
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status_approval', $status);
    }

    /**
     * Scope untuk filter berdasarkan jenis
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis_cuti', $jenis);
    }

    /**
     * Scope untuk cuti pending
     */
    public function scopePending($query)
    {
        return $query->where('status_approval', 'pending');
    }

    /**
     * Scope untuk cuti approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_approval', 'approved');
    }

    /**
     * Scope untuk cuti tahunan berjalan
     */
    public function scopeCutiTahunanThisYear($query, $idKaryawan)
    {
        return $query->where('id_karyawan', $idKaryawan)->where('jenis_cuti', 'tahunan')->where('status_approval', 'approved')->whereYear('tanggal_mulai', date('Y'));
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
