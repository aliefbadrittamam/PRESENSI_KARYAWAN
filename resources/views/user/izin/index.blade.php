@extends('layouts.user')

@section('title', 'Riwayat Izin')

@section('content')
<div class="container-desktop">
    @include('user.components.header', ['karyawan' => $karyawan])

    <div class="izin-container">
        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Header -->
        <div class="page-header mb-4">
            <h4 class="mb-3">
                <i class="fas fa-history me-2"></i>
                Riwayat Pengajuan Izin
            </h4>
            <a href="{{ route('user.izin.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>
                Ajukan Izin Baru
            </a>
        </div>

        <!-- List Izin -->
        @forelse($izinList as $izin)
            <div class="izin-item-card mb-3">
                <div class="izin-header">
                    <div>
                        {!! $izin->tipe_izin_badge !!}
                        {!! $izin->status_badge !!}
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        {{ $izin->tanggal_pengajuan->diffForHumans() }}
                    </small>
                </div>

                <div class="izin-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-2">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                {{ $izin->tanggal_mulai_formatted }} - {{ $izin->tanggal_selesai_formatted }}
                            </h6>
                            <p class="mb-2 text-muted">
                                <i class="fas fa-comment me-2"></i>
                                {{ Str::limit($izin->keterangan, 100) }}
                            </p>
                            <div class="info-tags">
                                <span class="tag">
                                    <i class="fas fa-calendar-day me-1"></i>
                                    {{ $izin->jumlah_hari }} hari
                                </span>
                                @if($izin->file_pendukung)
                                    <span class="tag">
                                        <i class="fas fa-paperclip me-1"></i>
                                        Ada lampiran
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('user.izin.show', $izin->id_izin) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h5>Belum Ada Pengajuan Izin</h5>
                <p>Anda belum pernah mengajukan izin</p>
                <a href="{{ route('user.izin.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>
                    Ajukan Izin Sekarang
                </a>
            </div>
        @endforelse

        <!-- Pagination -->
        @if($izinList->hasPages())
            <div class="mt-4">
                {{ $izinList->links() }}
            </div>
        @endif
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

    .page-header {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .izin-item-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        transition: all 0.3s;
    }

    .izin-item-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    .izin-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .izin-body {
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
    }
</style>
@endpush