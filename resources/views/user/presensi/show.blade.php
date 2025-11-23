{{-- File: resources/views/user/presensi/show.blade.php --}}
@extends('layouts.user')

@section('title', 'Detail Presensi')

@section('content')
<div class="container-desktop">
    <!-- Header -->
    @include('user.components.header', ['karyawan' => $karyawan])

    <div class="detail-container">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('presensi.history') }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
            </a>
        </div>

        <!-- Page Title -->
        <div class="page-header">
            <h3 class="page-title">
                <i class="fas fa-file-alt me-2"></i>
                Detail Presensi
            </h3>
            <p class="text-muted mb-0">{{ $presensi->tanggal_presensi->format('l, d F Y') }}</p>
        </div>

        <!-- Status Card -->
        <div class="status-card-detail">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="info-icon bg-primary-subtle">
                            <i class="fas fa-calendar-day text-primary"></i>
                        </div>
                        <div class="info-content">
                            <small class="text-muted">Tanggal</small>
                            <div class="fw-semibold">{{ $presensi->tanggal_presensi->format('d F Y') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="info-icon bg-success-subtle">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <div class="info-content">
                            <small class="text-muted">Status</small>
                            <div>
                                <span class="badge bg-{{ $presensi->status_kehadiran == 'hadir' ? 'success' : ($presensi->status_kehadiran == 'terlambat' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($presensi->status_kehadiran) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absen Masuk Section -->
        <div class="detail-card">
            <h5 class="card-title">
                <i class="fas fa-sign-in-alt text-success me-2"></i>
                Absen Masuk
            </h5>

            @if($presensi->jam_masuk)
                <div class="row g-3">
                    <!-- Foto Masuk -->
                    <div class="col-md-4">
                        <div class="photo-wrapper">
                            @if($presensi->foto_masuk)
                                <img src="{{ asset('storage/' . $presensi->foto_masuk) }}" 
                                     alt="Foto Masuk" 
                                     class="presensi-photo"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#fotoMasukModal">
                                <div class="photo-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            @else
                                <div class="no-photo">
                                    <i class="fas fa-image"></i>
                                    <p>Tidak ada foto</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Info Masuk -->
                    <div class="col-md-8">
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="fas fa-clock text-primary"></i>
                                <div>
                                    <small class="text-muted">Waktu</small>
                                    <div class="fw-semibold">{{ date('H:i:s', strtotime($presensi->jam_masuk)) }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <div>
                                    <small class="text-muted">Koordinat</small>
                                    <div class="fw-semibold small">
                                        {{ $presensi->latitude_masuk }}, {{ $presensi->longitude_masuk }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-crosshairs text-success"></i>
                                <div>
                                    <small class="text-muted">Akurasi</small>
                                    <div class="fw-semibold">{{ number_format($presensi->accuracy_masuk, 0) }} meter</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-location-arrow text-info"></i>
                                <div>
                                    <small class="text-muted">Alamat</small>
                                    <div class="fw-semibold small">{{ $presensi->alamat_masuk ?: '-' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($presensi->keterlambatan_menit > 0)
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Terlambat {{ $presensi->keterlambatan_menit }} menit
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Belum melakukan absen masuk
                </div>
            @endif
        </div>

        <!-- Absen Keluar Section -->
        <div class="detail-card">
            <h5 class="card-title">
                <i class="fas fa-sign-out-alt text-danger me-2"></i>
                Absen Keluar
            </h5>

            @if($presensi->jam_keluar)
                <div class="row g-3">
                    <!-- Foto Keluar -->
                    <div class="col-md-4">
                        <div class="photo-wrapper">
                            @if($presensi->foto_keluar)
                                <img src="{{ asset('storage/' . $presensi->foto_keluar) }}" 
                                     alt="Foto Keluar" 
                                     class="presensi-photo"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#fotoKeluarModal">
                                <div class="photo-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            @else
                                <div class="no-photo">
                                    <i class="fas fa-image"></i>
                                    <p>Tidak ada foto</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Info Keluar -->
                    <div class="col-md-8">
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="fas fa-clock text-primary"></i>
                                <div>
                                    <small class="text-muted">Waktu</small>
                                    <div class="fw-semibold">{{ date('H:i:s', strtotime($presensi->jam_keluar)) }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <div>
                                    <small class="text-muted">Koordinat</small>
                                    <div class="fw-semibold small">
                                        {{ $presensi->latitude_keluar }}, {{ $presensi->longitude_keluar }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-crosshairs text-success"></i>
                                <div>
                                    <small class="text-muted">Akurasi</small>
                                    <div class="fw-semibold">{{ number_format($presensi->accuracy_keluar, 0) }} meter</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-location-arrow text-info"></i>
                                <div>
                                    <small class="text-muted">Alamat</small>
                                    <div class="fw-semibold small">{{ $presensi->alamat_keluar ?: '-' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($presensi->total_jam_kerja)
                            <div class="alert alert-success mt-3 mb-0">
                                <i class="fas fa-hourglass-half me-2"></i>
                                Total jam kerja: {{ number_format($presensi->total_jam_kerja, 1) }} jam
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Belum melakukan absen keluar
                </div>
            @endif
        </div>

        <!-- Additional Info -->
        @if($presensi->catatan)
            <div class="detail-card">
                <h5 class="card-title">
                    <i class="fas fa-sticky-note text-warning me-2"></i>
                    Catatan
                </h5>
                <p class="mb-0">{{ $presensi->catatan }}</p>
            </div>
        @endif

        @if($presensi->shift)
            <div class="detail-card">
                <h5 class="card-title">
                    <i class="fas fa-business-time text-info me-2"></i>
                    Shift Kerja
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Nama Shift</small>
                        <div class="fw-semibold">{{ $presensi->shift->nama_shift }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Jam Kerja</small>
                        <div class="fw-semibold">
                            {{ date('H:i', strtotime($presensi->shift->jam_mulai)) }} - 
                            {{ date('H:i', strtotime($presensi->shift->jam_selesai)) }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Foto Masuk -->
<div class="modal fade" id="fotoMasukModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Absen Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @if($presensi->foto_masuk)
                    <img src="{{ asset('storage/' . $presensi->foto_masuk) }}" class="img-fluid w-100">
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto Keluar -->
<div class="modal fade" id="fotoKeluarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Absen Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @if($presensi->foto_keluar)
                    <img src="{{ asset('storage/' . $presensi->foto_keluar) }}" class="img-fluid w-100">
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Bottom Navigation -->
@include('user.components.bottom-nav')

<!-- Sidebar Menu -->
@include('user.components.sidebar-menu')
@endsection

@push('styles')
<style>
    .detail-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 1rem;
    }

    .page-header {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .page-title {
        color: #1F2937;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .status-card-detail {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .info-box {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .info-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .detail-card {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .card-title {
        color: #1F2937;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f8f9fa;
    }

    .photo-wrapper {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
        aspect-ratio: 3/4;
        cursor: pointer;
    }

    .presensi-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .photo-wrapper:hover .presensi-photo {
        transform: scale(1.05);
    }

    .photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
        color: white;
        font-size: 2rem;
    }

    .photo-wrapper:hover .photo-overlay {
        opacity: 1;
    }

    .no-photo {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6c757d;
    }

    .no-photo i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .info-item i {
        font-size: 1.25rem;
        margin-top: 0.25rem;
        width: 24px;
        flex-shrink: 0;
    }

    @media (min-width: 768px) {
        .detail-container {
            padding: 2rem;
        }

        .info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush