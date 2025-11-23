{{-- File: resources/views/user/cuti/index.blade.php --}}
@extends('layouts.user')

@section('title', 'Riwayat Cuti')

@section('content')
<div class="container-desktop">
    @include('user.components.header', ['karyawan' => $karyawan])

    <div class="cuti-container">
        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Sisa Cuti Card -->
        <div class="kuota-card mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-2">
                        <i class="fas fa-calendar-check me-2 text-primary"></i>
                        Kuota Cuti Tahunan {{ date('Y') }}
                    </h5>
                    <p class="text-muted mb-0">
                        Sisa kuota cuti tahunan yang dapat Anda gunakan
                    </p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="d-flex justify-content-md-end align-items-center gap-3">
                        <div class="stat-box">
                            <small class="text-muted d-block">Terpakai</small>
                            <h4 class="mb-0 text-danger">{{ $cutiTerpakai }}</h4>
                        </div>
                        <div class="divider"></div>
                        <div class="stat-box">
                            <small class="text-muted d-block">Sisa</small>
                            <h4 class="mb-0 text-success">{{ $sisaCuti }}</h4>
                        </div>
                        <div class="divider"></div>
                        <div class="stat-box">
                            <small class="text-muted d-block">Total</small>
                            <h4 class="mb-0 text-primary">12</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div class="page-header mb-4">
            <h4 class="mb-3">
                <i class="fas fa-history me-2"></i>
                Riwayat Pengajuan Cuti
            </h4>
            <a href="{{ route('user.cuti.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>
                Ajukan Cuti Baru
            </a>
        </div>

        <!-- List Cuti -->
        @forelse($cutiList as $cuti)
            <div class="cuti-item-card mb-3">
                <div class="cuti-header">
                    <div>
                        {!! $cuti->jenis_cuti_badge !!}
                        {!! $cuti->status_badge !!}
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        {{ $cuti->tanggal_pengajuan->diffForHumans() }}
                    </small>
                </div>

                <div class="cuti-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-2">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                {{ $cuti->tanggal_mulai_formatted }} - {{ $cuti->tanggal_selesai_formatted }}
                            </h6>
                            <p class="mb-2 text-muted">
                                <i class="fas fa-comment me-2"></i>
                                {{ Str::limit($cuti->keterangan, 100) }}
                            </p>
                            <div class="info-tags">
                                <span class="tag">
                                    <i class="fas fa-calendar-day me-1"></i>
                                    {{ $cuti->jumlah_hari }} hari
                                </span>
                                @if($cuti->file_pendukung)
                                    <span class="tag">
                                        <i class="fas fa-paperclip me-1"></i>
                                        Ada lampiran
                                    </span>
                                @endif
                                @if($cuti->jenis_cuti === 'tahunan' && $cuti->status_approval === 'approved')
                                    <span class="tag tag-warning">
                                        <i class="fas fa-minus-circle me-1"></i>
                                        Kuota terpakai
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('user.cuti.show', $cuti->id_cuti) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-umbrella-beach"></i>
                <h5>Belum Ada Pengajuan Cuti</h5>
                <p>Anda belum pernah mengajukan cuti</p>
                <a href="{{ route('user.cuti.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>
                    Ajukan Cuti Sekarang
                </a>
            </div>
        @endforelse

        <!-- Pagination -->
        @if($cutiList->hasPages())
            <div class="mt-4">
                {{ $cutiList->links() }}
            </div>
        @endif
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

    .kuota-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    }

    .stat-box {
        text-align: center;
    }

    .stat-box h4 {
        font-weight: 700;
        font-size: 2rem;
    }

    .divider {
        width: 2px;
        height: 50px;
        background: rgba(255, 255, 255, 0.3);
    }

    .page-header {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cuti-item-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        transition: all 0.3s;
    }

    .cuti-item-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .cuti-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .cuti-body {
        padding: 1.5rem;
    }

    .info-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .tag {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: #e3f2fd;
        color: #1976d2;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .tag-warning {
        background: #fff3cd;
        color: #856404;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
    }

    .empty-state i {
        font-size: 4rem;
        color: #ccc;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .kuota-card {
            padding: 1.5rem;
        }

        .stat-box h4 {
            font-size: 1.5rem;
        }

        .divider {
            height: 40px;
        }
    }
</style>
@endpush