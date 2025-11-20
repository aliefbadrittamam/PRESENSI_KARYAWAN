{{-- File: resources/views/user/cuti/show.blade.php --}}
@extends('layouts.user')

@section('title', 'Detail Cuti')

@section('content')
<div class="container-desktop">
    @include('user.components.header', ['karyawan' => $karyawan])

    <div class="cuti-container">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali ke Daftar Cuti
            </a>
        </div>

        <!-- Detail Card -->
        <div class="detail-card">
            <!-- Header -->
            <div class="detail-header">
                <div>
                    <h4 class="mb-2">Detail Pengajuan Cuti</h4>
                    <small class="text-white-50">
                        <i class="fas fa-calendar me-1"></i>
                        Diajukan pada {{ $cuti->tanggal_pengajuan->isoFormat('DD MMMM YYYY, HH:mm') }} WIB
                    </small>
                </div>
                <div class="status-badges">
                    {!! $cuti->jenis_cuti_badge !!}
                    {!! $cuti->status_badge !!}
                </div>
            </div>

            <!-- Body -->
            <div class="detail-body">
                <!-- Informasi Dasar -->
                <div class="info-section">
                    <h6 class="section-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Cuti
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Jenis Cuti</label>
                                <div class="info-value">
                                    <i class="fas fa-tag me-2 text-primary"></i>
                                    {{ $cuti->jenis_cuti_text }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Durasi</label>
                                <div class="info-value">
                                    <i class="fas fa-clock me-2 text-success"></i>
                                    {{ $cuti->jumlah_hari }} hari
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Tanggal Mulai</label>
                                <div class="info-value">
                                    <i class="fas fa-calendar-day text-success me-2"></i>
                                    {{ $cuti->tanggal_mulai_formatted }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Tanggal Selesai</label>
                                <div class="info-value">
                                    <i class="fas fa-calendar-check text-danger me-2"></i>
                                    {{ $cuti->tanggal_selesai_formatted }}
                                </div>
                            </div>
                        </div>
                        @if($cuti->jenis_cuti === 'tahunan' && $cuti->sisa_cuti_tahunan !== null)
                            <div class="col-md-12">
                                <div class="info-item">
                                    <label>Sisa Kuota Cuti Tahunan (Setelah Disetujui)</label>
                                    <div class="info-value">
                                        <i class="fas fa-chart-line me-2 text-warning"></i>
                                        {{ $cuti->sisa_cuti_tahunan }} hari
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="info-section">
                    <h6 class="section-title">
                        <i class="fas fa-comment-dots me-2"></i>
                        Alasan/Keterangan
                    </h6>
                    <div class="keterangan-box">
                        {{ $cuti->keterangan }}
                    </div>
                </div>

                <!-- File Pendukung -->
                @if($cuti->file_pendukung)
                    <div class="info-section">
                        <h6 class="section-title">
                            <i class="fas fa-paperclip me-2"></i>
                            File Pendukung
                        </h6>
                        <div class="file-box">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <i class="fas fa-file-alt text-primary fs-3"></i>
                                <div>
                                    <div class="fw-bold">{{ basename($cuti->file_pendukung) }}</div>
                                    <small class="text-muted">
                                        {{ strtoupper(pathinfo($cuti->file_pendukung, PATHINFO_EXTENSION)) }} File
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ asset('storage/' . $cuti->file_pendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>
                                    Lihat
                                </a>
                                <a href="{{ asset('storage/' . $cuti->file_pendukung) }}" download class="btn btn-sm btn-primary">
                                    <i class="fas fa-download me-1"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Status Approval -->
                <div class="info-section">
                    <h6 class="section-title">
                        <i class="fas fa-tasks me-2"></i>
                        Status Persetujuan
                    </h6>
                    
                    @if($cuti->status_approval == 'pending')
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-clock fs-3 me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Menunggu Persetujuan</h6>
                                    <p class="mb-0">Pengajuan cuti Anda sedang menunggu persetujuan dari admin/supervisor. Mohon bersabar menunggu konfirmasi.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($cuti->status_approval == 'approved')
                        <div class="alert alert-success">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-circle fs-3 me-3 mt-1"></i>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-2">Pengajuan Disetujui</h6>
                                    <p class="mb-2">
                                        Pengajuan cuti Anda telah disetujui pada <strong>{{ $cuti->tanggal_approval->isoFormat('DD MMMM YYYY, HH:mm') }} WIB</strong>
                                        @if($cuti->approver)
                                            oleh <strong>{{ $cuti->approver->name }}</strong>
                                        @endif
                                    </p>
                                    @if($cuti->jenis_cuti === 'tahunan')
                                        <div class="alert alert-info mb-0 mt-2">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Kuota cuti tahunan Anda telah dikurangi <strong>{{ $cuti->jumlah_hari }} hari</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($cuti->status_approval == 'rejected')
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-times-circle fs-3 me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Pengajuan Ditolak</h6>
                                    <p class="mb-2">
                                        Pengajuan cuti Anda ditolak pada <strong>{{ $cuti->tanggal_approval->isoFormat('DD MMMM YYYY, HH:mm') }} WIB</strong>
                                        @if($cuti->approver)
                                            oleh <strong>{{ $cuti->approver->name }}</strong>
                                        @endif
                                    </p>
                                    @if($cuti->alasan_penolakan)
                                        <hr>
                                        <strong>Alasan Penolakan:</strong>
                                        <p class="mb-0 mt-2">{{ $cuti->alasan_penolakan }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Timeline -->
                <div class="info-section">
                    <h6 class="section-title">
                        <i class="fas fa-history me-2"></i>
                        Riwayat Aktivitas
                    </h6>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <strong>Pengajuan Dibuat</strong>
                                    <small class="text-muted">{{ $cuti->tanggal_pengajuan->isoFormat('DD MMM YYYY, HH:mm') }}</small>
                                </div>
                                <p class="mb-0 text-muted">
                                    Pengajuan cuti {{ $cuti->jenis_cuti_text }} telah dikirim dan menunggu persetujuan
                                </p>
                            </div>
                        </div>

                        @if($cuti->status_approval == 'approved')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <strong>Disetujui</strong>
                                        <small class="text-muted">{{ $cuti->tanggal_approval->isoFormat('DD MMM YYYY, HH:mm') }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">
                                        Pengajuan disetujui
                                        @if($cuti->approver)
                                            oleh {{ $cuti->approver->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @elseif($cuti->status_approval == 'rejected')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <strong>Ditolak</strong>
                                        <small class="text-muted">{{ $cuti->tanggal_approval->isoFormat('DD MMM YYYY, HH:mm') }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">
                                        Pengajuan ditolak
                                        @if($cuti->approver)
                                            oleh {{ $cuti->approver->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                @if($cuti->status_approval == 'pending')
                    <div class="text-center mt-4">
                        <p class="text-muted mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Anda dapat membatalkan pengajuan ini jika diperlukan
                        </p>
                        <button class="btn btn-outline-danger" onclick="alert('Fitur pembatalan akan segera hadir')">
                            <i class="fas fa-times-circle me-2"></i>
                            Batalkan Pengajuan
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('user.components.bottom-nav')
@include('user.components.sidebar-menu')
@endsection

@push('styles')
<style>
    .cuti-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 1rem;
    }

    .detail-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .detail-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .status-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .detail-body {
        padding: 2rem;
    }

    .info-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .info-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .section-title {
        color: #1F2937;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .info-item {
        margin-bottom: 1rem;
    }

    .info-item label {
        display: block;
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .info-value {
        font-size: 1rem;
        color: #1F2937;
        font-weight: 500;
    }

    .keterangan-box {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 12px;
        border-left: 4px solid #f093fb;
        line-height: 1.6;
    }

    .file-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px dashed #dee2e6;
    }

    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -1.5rem;
        top: 0.25rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 2px currentColor;
    }

    .timeline-content {
        padding-left: 1rem;
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    @media (max-width: 768px) {
        .detail-header {
            flex-direction: column;
        }

        .detail-body {
            padding: 1.5rem;
        }

        .status-badges {
            width: 100%;
        }

        .file-box {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
@endpush