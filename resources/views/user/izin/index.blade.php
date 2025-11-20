@extends('layouts.user')

@section('title', 'Riwayat Izin')

@section('content')
    <div class="container-desktop">
        @include('user.components.header', ['karyawan' => $karyawan])

        <div class="izin-container">
            <!-- Success Message -->
            @if (session('success'))
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
                <a href="{{ route('izin.create') }}" class="btn btn-sage">
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
                                    <i class="fas fa-calendar-alt me-2 text-sage"></i>
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
                                    @if ($izin->file_pendukung)
                                        <span class="tag">
                                            <i class="fas fa-paperclip me-1"></i>
                                            Ada lampiran
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="{{ route('izin.show', $izin->id_izin) }}" class="btn btn-outline-sage btn-sm">
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
                    <a href="{{ route('izin.create') }}" class="btn btn-sage">
                        <i class="fas fa-plus-circle me-2 text-whitegut"></i>
                        Ajukan Izin Sekarang
                    </a>
                </div>
            @endforelse

            <!-- Pagination -->
            @if ($izinList->hasPages())
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

        .izin-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 1rem;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 10px;
            font-weight: 500;
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

        .izin-item-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: all 0.3s;
        }

        .izin-item-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .izin-header {
            background: var(--light-gray);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .izin-header > div:first-child {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .izin-body {
            padding: 1.5rem;
        }

        .izin-body h6 {
            color: var(--dark-gray);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .izin-body p {
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
            .izin-container {
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

            .izin-body .btn-outline-sage {
                width: 100%;
                margin-top: 1rem;
            }

            .izin-header {
                padding: 1rem;
            }

            .izin-body {
                padding: 1.25rem;
            }
        }
    </style>
@endpush