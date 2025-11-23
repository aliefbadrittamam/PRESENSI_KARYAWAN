{{-- File: resources/views/user/presensi/history.blade.php --}}
@extends('layouts.user')

@section('title', 'Riwayat Presensi')

@section('content')
<div class="container-desktop">
    <!-- Header -->
    @include('user.components.header', ['karyawan' => $karyawan])

    <div class="history-container">
        <!-- Page Title -->
        <div class="page-header">
            <h3 class="page-title">
                <i class="fas fa-history me-2"></i>
                Riwayat Presensi
            </h3>
            <p class="text-muted mb-0">Lihat riwayat kehadiran Anda</p>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Filter Bulan</label>
                    <select class="form-select" id="monthFilter">
                        @foreach($months as $monthData)
                            <option value="{{ $monthData['value'] }}" {{ $monthData['selected'] ? 'selected' : '' }}>
                                {{ $monthData['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <label class="form-label fw-semibold">Total Presensi</label>
                    <div class="stats-box">
                        <i class="fas fa-calendar-check text-success me-2"></i>
                        <span class="fw-bold">{{ $presensi->total() }} Hari</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- History List -->
        @if($presensi->count() > 0)
            <div class="history-list">
                @foreach($presensi as $item)
                    <div class="history-item">
                        <div class="history-date">
                            <div class="date-badge">
                                <div class="date-number">{{ $item->tanggal_presensi->format('d') }}</div>
                                <div class="date-month">{{ $item->tanggal_presensi->format('M') }}</div>
                            </div>
                        </div>
                        
                        <div class="history-content">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="history-title mb-1">
                                        {{ $item->tanggal_presensi->format('l, d F Y') }}
                                    </h6>
                                    <span class="badge bg-{{ $item->status_kehadiran == 'hadir' ? 'success' : ($item->status_kehadiran == 'terlambat' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($item->status_kehadiran) }}
                                    </span>
                                </div>
                                <a href="{{ route('presensi.show', $item->id_presensi) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>

                            <div class="time-info">
                                <div class="time-item">
                                    <i class="fas fa-sign-in-alt text-success"></i>
                                    <span class="time-label">Masuk:</span>
                                    <span class="time-value">
                                        {{ $item->jam_masuk ? date('H:i', strtotime($item->jam_masuk)) : '-' }}
                                    </span>
                                </div>
                                <div class="time-item">
                                    <i class="fas fa-sign-out-alt text-danger"></i>
                                    <span class="time-label">Keluar:</span>
                                    <span class="time-value">
                                        {{ $item->jam_keluar ? date('H:i', strtotime($item->jam_keluar)) : '-' }}
                                    </span>
                                </div>
                            </div>

                            @if($item->keterlambatan_menit > 0)
                                <div class="alert alert-warning alert-sm mt-2 mb-0">
                                    <i class="fas fa-clock me-1"></i>
                                    Terlambat {{ $item->keterlambatan_menit }} menit
                                </div>
                            @endif

                            @if($item->total_jam_kerja)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-hourglass-half me-1"></i>
                                        Total jam kerja: {{ number_format($item->total_jam_kerja, 1) }} jam
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $presensi->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h5>Belum Ada Data</h5>
                <p class="text-muted">Belum ada riwayat presensi untuk bulan ini</p>
                <a href="{{ route('presensi.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-camera me-2"></i>
                    Absen Sekarang
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Bottom Navigation -->
@include('user.components.bottom-nav')

<!-- Sidebar Menu -->
@include('user.components.sidebar-menu')
@endsection

@push('styles')
<style>
    .history-container {
        max-width: 800px;
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

    .filter-card {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .stats-box {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        font-size: 1.1rem;
    }

    .history-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .history-item {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        gap: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .history-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .history-date {
        flex-shrink: 0;
    }

    .date-badge {
        width: 60px;
        height: 70px;
        background: linear-gradient(135deg, #FF4F7E 0%, #FF6B8F 100%);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
    }

    .date-number {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1;
    }

    .date-month {
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-top: 0.25rem;
    }

    .history-content {
        flex: 1;
    }

    .history-title {
        color: #1F2937;
        font-weight: 600;
        margin: 0;
    }

    .time-info {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 0.75rem;
    }

    .time-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .time-item i {
        width: 20px;
    }

    .time-label {
        color: #6c757d;
    }

    .time-value {
        font-weight: 600;
        color: #1F2937;
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }

    .empty-state {
        background: white;
        padding: 3rem 2rem;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .pagination-wrapper {
        margin-top: 1.5rem;
    }

    @media (max-width: 576px) {
        .history-item {
            flex-direction: column;
            text-align: center;
        }

        .date-badge {
            margin: 0 auto;
        }

        .time-info {
            justify-content: center;
        }
    }

    @media (min-width: 768px) {
        .history-container {
            padding: 2rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('monthFilter').addEventListener('change', function() {
        window.location.href = '{{ route("presensi.history") }}?bulan=' + this.value;
    });
</script>
@endpush