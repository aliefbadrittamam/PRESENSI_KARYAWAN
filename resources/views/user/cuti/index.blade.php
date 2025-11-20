{{-- File: resources/views/user/cuti/index.blade.php --}}
@extends('layouts.user')

@section('title', 'Riwayat Cuti')

@section('content')
    <div class="container-desktop">
        @include('user.components.header', ['karyawan' => $karyawan])

        <div class="cuti-container">
            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Sisa Cuti Card -->
            <div class="kuota-card mb-4">
                <div class="kuota-content-wrapper">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-2 kuota-title">
                                <i class="fas fa-calendar-check me-2"></i>
                                Kuota Cuti Tahunan {{ date('Y') }}
                            </h5>
                            <p class="kuota-subtitle mb-0">
                                Sisa kuota cuti tahunan yang dapat Anda gunakan
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <div class="d-flex justify-content-md-end align-items-center gap-3">
                                <div class="stat-box">
                                    <small class="stat-label d-block">Terpakai</small>
                                    <h4 class="mb-0 stat-value stat-danger">{{ $cutiTerpakai }}</h4>
                                </div>
                                <div class="divider"></div>
                                <div class="stat-box">
                                    <small class="stat-label d-block">Sisa</small>
                                    <h4 class="mb-0 stat-value stat-success">{{ $sisaCuti }}</h4>
                                </div>
                                <div class="divider"></div>
                                <div class="stat-box">
                                    <small class="stat-label d-block">Total</small>
                                    <h4 class="mb-0 stat-value stat-primary">12</h4>
                                </div>
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
                <a href="{{ route('cuti.create') }}" class="btn btn-sage">
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
                                    <i class="fas fa-calendar-alt me-2 text-sage"></i>
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
                                    @if ($cuti->file_pendukung)
                                        <span class="tag">
                                            <i class="fas fa-paperclip me-1"></i>
                                            Ada lampiran
                                        </span>
                                    @endif
                                    @if ($cuti->jenis_cuti === 'tahunan' && $cuti->status_approval === 'approved')
                                        <span class="tag tag-warning">
                                            <i class="fas fa-minus-circle me-1"></i>
                                            Kuota terpakai
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="{{ route('cuti.show', $cuti->id_cuti) }}" class="btn btn-outline-sage btn-sm">
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
                    <a href="{{ route('cuti.create') }}" class="btn btn-sage">
                        <i class="fas fa-plus-circle me-2 text-white"></i>
                        Ajukan Cuti Sekarang
                    </a>

                </div>
            @endforelse

            <!-- Pagination -->
            @if ($cutiList->hasPages())
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
        :root {
            /* Warna Sage Green */
            --primary-sage: #9DC183;
            --secondary-sage: #6FA976;
            --accent-cream: #FDF6EC;
            --accent-gold: #E4C988;
            --light-gray: #F5F7FA;
            --medium-gray: #A0AEC0;
            --dark-gray: #4A5568;
            --soft-pink: #F2C6B4;
            --soft-blue: #BFD8D2;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .cuti-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 1rem;
        }

        /* Kuota Card dengan Inner Card */
        .kuota-card {
            background: var(--primary-sage);
            padding: 2rem;
            border-radius: 24px;
            box-shadow: 0 8px 16px rgba(157, 193, 131, 0.3);
        }

        .kuota-content-wrapper {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .kuota-title {
            color: var(--dark-gray);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .kuota-title i {
            color: var(--primary-sage);
            font-size: 1.25rem;
        }

        .kuota-subtitle {
            color: var(--medium-gray);
            font-size: 0.95rem;
        }

        .stat-box {
            text-align: center;
        }

        .stat-label {
            color: var(--medium-gray);
            font-size: 0.85rem;
        }

        .stat-value {
            font-weight: 700;
            font-size: 2rem;
        }

        .stat-danger {
            color: #E57373;
        }

        .stat-success {
            color: var(--secondary-sage);
        }

        .stat-primary {
            color: var(--primary-sage);
        }

        .divider {
            width: 2px;
            height: 50px;
            background: #e0e0e0;
        }

        .page-header {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-header h4 {
            color: var(--dark-gray);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .page-header i {
            color: var(--primary-sage);
        }

        /* Button Sage */
        .btn-sage {
            background: var(--primary-sage);
            border: none;
            color: white;
            transition: all 0.3s;
            padding: 0.625rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-sage i {
            color: white;
        }

        .btn-sage:hover {
            background: var(--secondary-sage);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(157, 193, 131, 0.4);
        }

        .btn-sage:hover i {
            color: white;
        }

        .btn-outline-sage {
            border: 2px solid var(--primary-sage);
            color: var(--primary-sage);
            background: transparent;
            transition: all 0.3s;
            padding: 0.5rem 1.25rem;
            border-radius: 10px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-outline-sage i {
            color: var(--primary-sage);
        }

        .btn-outline-sage:hover {
            background: var(--primary-sage);
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline-sage:hover i {
            color: white;
        }

        .text-sage {
            color: var(--primary-sage);
        }

        .cuti-item-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: all 0.3s;
        }

        .cuti-item-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .cuti-header {
            background: var(--light-gray);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .cuti-header>div:first-child {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .cuti-body {
            padding: 1.5rem;
        }

        .cuti-body h6 {
            color: var(--dark-gray);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cuti-body p {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            line-height: 1.6;
        }

        .info-tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.75rem;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.875rem;
            background: rgba(157, 193, 131, 0.15);
            color: var(--secondary-sage);
            border-radius: 20px;
            font-size: 0.85rem;
            border: 1px solid rgba(157, 193, 131, 0.3);
            font-weight: 500;
        }

        .tag i {
            font-size: 0.8rem;
        }

        .tag-warning {
            background: rgba(228, 201, 136, 0.15);
            color: #B8860B;
            border: 1px solid rgba(228, 201, 136, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--medium-gray);
            opacity: 0.5;
            margin-bottom: 1.5rem;
            display: block;
        }

        .empty-state h5 {
            color: var(--dark-gray);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .empty-state p {
            color: var(--medium-gray);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        /* Alert Styles */
        .alert-success {
            background: #E8F5E9;
            color: #2E7D32;
            border-left: 4px solid var(--primary-sage);
            border-radius: 12px;
            border: none;
        }

        @media (max-width: 768px) {
            .cuti-container {
                padding: 0.5rem;
            }

            .page-header {
                flex-direction: column;
                text-align: center;
                padding: 1.25rem;
            }

            .page-header h4 {
                margin-bottom: 0 !important;
                justify-content: center;
            }

            .page-header .btn-sage {
                width: 100%;
            }

            .kuota-card {
                padding: 1.5rem;
            }

            .kuota-content-wrapper {
                padding: 1.5rem;
            }

            .kuota-title {
                font-size: 1.1rem;
                justify-content: center;
            }

            .kuota-subtitle {
                font-size: 0.85rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .stat-label {
                font-size: 0.75rem;
            }

            .divider {
                height: 40px;
            }

            .d-flex.gap-3 {
                justify-content: center !important;
            }

            .cuti-body .btn-outline-sage {
                width: 100%;
                margin-top: 1rem;
            }

            .cuti-header {
                padding: 1rem;
            }

            .cuti-body {
                padding: 1.25rem;
            }
        }
    </style>
@endpush
