@extends('layouts.user')

@section('title', 'Detail Izin')

@section('content')
<div class="container-desktop">
    @include('user.components.header', ['karyawan' => $karyawan])

    <div class="izin-container">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('user.izin.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali ke Daftar Izin
            </a>
        </div>

        <!-- Detail Card -->
        <div class="detail-card">
            <!-- Header -->
            <div class="detail-header">
                <div>
                    <h4 class="mb-2">Detail Pengajuan Izin</h4>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Diajukan pada {{ $izin->tanggal_pengajuan->isoFormat('DD MMMM YYYY, HH:mm') }} WIB
                    </small>
                </div>
                <div class="status-badges">
                    {!! $izin->tipe_izin_badge !!}
                    {!! $izin->status_badge !!}
                </div>
            </div>

            <!-- Body -->
            <div class="detail-body">
                <!-- Informasi Dasar -->
                <div class="info-section">
                    <h6 class="section-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Izin
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Jenis Izin</label>
                                <div class="info-value">
                                    {{ $izin->tipe_izin_text }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Durasi</label>
                                <div class="info-value">
                                    {{ $izin->jumlah_hari }} hari
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Tanggal Mulai</label>
                                <div class="info-value">
                                    <i class="fas fa-calendar-day text-success me-2"></i>
                                    {{ $izin->tanggal_mulai_formatted }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Tanggal Selesai</label>
                                <div class="info-value">
                                    <i class="fas fa-calendar-check text-danger me-2"></i>
                                    {{ $izin->tanggal_selesai_formatted }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="info-section">
                    <h6 class="section-title">
                        <i class="fas fa-comment-dots me-2"></i>
                        Alasan/Keterangan
                    </h6>
                    <div class="keterangan-box">
                        {{ $izin->keterangan }}
                    </div>
                </div>

                <!-- File Pendukung -->
                @if($izin->file_pendukung)
                    <div class="info-section">
                        <h6 class="section-title">
                            <i class="fas fa-paperclip me-2"></i>
                            File Pendukung
                        </h6>
                        <div class="file-box">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            <span>{{ basename($izin->file_pendukung) }}</span>
                            <a href="{{ asset('storage/' . $izin->file_pendukung) }}" target="_blank" class="btn btn-sm btn-primary ms-auto">
                                <i class="fas fa-download me-1"></i>
                                Download
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Status Approval -->
                <div class="info-section">
                    <h6 class="section-title">
                        <i class="fas fa-tasks me-2"></i>
                        Status Persetujuan
                    </h6>
                    
                    @if($izin->status_approval == 'pending')
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Menunggu Persetujuan</strong>
                            <p class="mb-0 mt-2">Pengajuan izin Anda sedang menunggu persetujuan dari admin/supervisor.</p>
                        </div>
                    @elseif($izin->status_approval == 'approved')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Disetujui</strong>
                            <p class="mb-0 mt-2">
                                Pengajuan izin Anda telah disetujui pada {{ $izin->tanggal_approval->isoFormat('DD MMMM YYYY, HH:mm') }} WIB
                                @if($izin->approver)
                                    oleh <strong>{{ $izin->approver->name }}</strong>
                                @endif
                            </p>
                        </div>
                    @elseif($izin->status_approval == 'rejected')
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Ditolak</strong>
                            <p class="mb-0 mt-2">
                                Pengajuan izin Anda ditolak pada {{ $izin->tanggal_approval->isoFormat('DD MMMM YYYY, HH:mm') }} WIB
                                @if($izin->approver)
                                    oleh <strong>{{ $izin->approver->name }}</strong>
                                @endif
                            </p>
                            @if($izin->alasan_penolakan)
                                <hr>
                                <strong>Alasan Penolakan:</strong>
                                <p class="mb-0 mt-1">{{ $izin->alasan_penolakan }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Timeline -->
                <div class="info-section">
                    <h6 class="section-title">
                        <i class="fas fa-history me-2"></i>
                        Riwayat
                    </h6>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <strong>Pengajuan Dibuat</strong>
                                    <small class="text-muted">{{ $izin->tanggal_pengajuan->isoFormat('DD MMM YYYY, HH:mm') }}</small>
                                </div>
                                <p class="mb-0 text-muted">Pengajuan izin telah dikirim</p>
                            </div>
                        </div>

                        @if($izin->status_approval == 'approved')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <strong>Disetujui</strong>
                                        <small class="text-muted">{{ $izin->tanggal_approval->isoFormat('DD MMM YYYY, HH:mm') }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">
                                        Pengajuan disetujui
                                        @if($izin->approver)
                                            oleh {{ $izin->approver->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @elseif($izin->status_approval == 'rejected')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <strong>Ditolak</strong>
                                        <small class="text-muted">{{ $izin->tanggal_approval->isoFormat('DD MMM YYYY, HH:mm') }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">
                                        Pengajuan ditolak
                                        @if($izin->approver)
                                            oleh {{ $izin->approver->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.components.bottom-nav')
@include('user.components.sidebar-menu')
@endsection

@push('styles')
<style>
    .izin-container {
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        border-left: 4px solid #667eea;
        line-height: 1.6;
    }

    .file-box {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 12px;
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
        border: 2px solid white;
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
    }
</style>
@endpush