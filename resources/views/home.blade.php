@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('icon', 'fa-tachometer-alt')

@section('content')
    <!-- Stats Cards Row 1 -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalFakultas }}</h3>
                    <p>Total Fakultas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-university"></i>
                </div>
                <a href="{{ route('admin.fakultas.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalDepartemen }}</h3>
                    <p>Total Departemen</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <a href="{{ route('admin.departemen.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalJabatan }}</h3>
                    <p>Total Jabatan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <a href="{{ route('admin.jabatan.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalKaryawan }}</h3>
                    <p>Total Karyawan Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.karyawan.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Presensi Hari Ini & Pengajuan -->
    <div class="row">
        <!-- Presensi Hari Ini -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Presensi Hari Ini
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">{{ now()->format('d F Y') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Hadir Tepat Waktu</span>
                                    <span class="info-box-number">{{ $hadirHariIni }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $totalKaryawan > 0 ? ($hadirHariIni / $totalKaryawan * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ $totalKaryawan > 0 ? round($hadirHariIni / $totalKaryawan * 100, 1) : 0 }}% dari total karyawan
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-gradient-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Terlambat</span>
                                    <span class="info-box-number">{{ $terlambatHariIni }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $totalKaryawan > 0 ? ($terlambatHariIni / $totalKaryawan * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ $totalKaryawan > 0 ? round($terlambatHariIni / $totalKaryawan * 100, 1) : 0 }}% dari total karyawan
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-gradient-info">
                                <span class="info-box-icon"><i class="fas fa-file-medical"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Izin/Sakit/Cuti</span>
                                    <span class="info-box-number">{{ $izinHariIni }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $totalKaryawan > 0 ? ($izinHariIni / $totalKaryawan * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ $totalKaryawan > 0 ? round($izinHariIni / $totalKaryawan * 100, 1) : 0 }}% dari total karyawan
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-gradient-danger">
                                <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Alpha</span>
                                    <span class="info-box-number">{{ $alphaHariIni }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $totalKaryawan > 0 ? ($alphaHariIni / $totalKaryawan * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ $totalKaryawan > 0 ? round($alphaHariIni / $totalKaryawan * 100, 1) : 0 }}% dari total karyawan
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="text-center">
                                <h2 class="font-weight-bold text-primary">{{ $persentaseKehadiranHariIni }}%</h2>
                                <p class="text-muted">Tingkat Kehadiran Hari Ini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengajuan & Stats Bulan Ini -->
        <div class="col-lg-4">
            <!-- Pengajuan Pending -->
            <div class="card bg-gradient-danger">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-envelope mr-2"></i>
                        Pengajuan Pending
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="mb-0">{{ $totalPengajuanPending }}</h2>
                            <p class="mb-0">Total Menunggu Approval</p>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </div>
                    </div>
                    <hr class="bg-white">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-envelope mr-2"></i>Izin</span>
                        <span class="badge badge-light">{{ $izinPending }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-calendar-alt mr-2"></i>Cuti</span>
                        <span class="badge badge-light">{{ $cutiPending }}</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-light btn-block">
                        <i class="fas fa-eye mr-2"></i>Lihat Semua Pengajuan
                    </a>
                </div>
            </div>
            
            <!-- Statistik Bulan Ini -->
            <div class="card bg-gradient-primary">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>
                        Statistik Bulan Ini
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Presensi</span>
                        <span class="badge badge-light">{{ $presensiTotalBulanIni }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Terlambat</span>
                        <span class="badge badge-warning">{{ $terlambatBulanIni }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Rata-rata Keterlambatan</span>
                        <span class="badge badge-light">{{ $avgKeterlambatan }} menit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Grafik Presensi 7 Hari Terakhir -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Tren Presensi 7 Hari Terakhir
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="presensiChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart Status Kehadiran Bulan Ini -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-2"></i>
                        Status Kehadiran Bulan Ini
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Presensi per Departemen & Tren Pengajuan -->
    <div class="row">
        <!-- Presensi per Departemen -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-building mr-2"></i>
                        Presensi per Departemen Hari Ini
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="departemenChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Tren Pengajuan 6 Bulan Terakhir -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>
                        Tren Pengajuan 6 Bulan Terakhir
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="trendPengajuanChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <!-- Top 5 Karyawan Terlambat -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                        Top 5 Karyawan Sering Terlambat Bulan Ini
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Departemen</th>
                                <th class="text-center">Frekuensi</th>
                                <th class="text-center">Total Menit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topTerlambat as $karyawan)
                                <tr>
                                    <td>
                                        <strong>{{ $karyawan->nama_lengkap }}</strong><br>
                                        <small class="text-muted">{{ $karyawan->nip }}</small>
                                    </td>
                                    <td>{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">{{ $karyawan->total_terlambat }}x</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-danger">{{ $karyawan->total_menit_terlambat }} menit</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Tidak ada data keterlambatan bulan ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Karyawan dengan Kehadiran Sempurna -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-trophy mr-2 text-success"></i>
                        Karyawan dengan Kehadiran Sempurna
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Departemen</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($karyawanPerfect as $karyawan)
                                <tr>
                                    <td>
                                        <strong>{{ $karyawan->nama_lengkap }}</strong><br>
                                        <small class="text-muted">{{ $karyawan->nip }}</small>
                                    </td>
                                    <td>{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $karyawan->total_hadir }} hari</span>
                                    </td>
                                    <td class="text-center">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Belum ada karyawan dengan kehadiran sempurna bulan ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Presensi Terbaru Hari Ini -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>
                        Presensi Terbaru Hari Ini
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.presensi.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye mr-2"></i>Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Karyawan</th>
                                    <th>Departemen</th>
                                    <th>Jabatan</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestPresensi as $presensi)
                                    <tr>
                                        <td>
                                            <strong>{{ $presensi->karyawan->nama_lengkap ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $presensi->karyawan->nip ?? '-' }}</small>
                                        </td>
                                        <td>{{ $presensi->karyawan->departemen->nama_departemen ?? '-' }}</td>
                                        <td>{{ $presensi->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                                        <td>
                                            @if($presensi->jam_masuk)
                                                <i class="fas fa-clock text-success mr-1"></i>
                                                {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($presensi->jam_keluar)
                                                <i class="fas fa-clock text-danger mr-1"></i>
                                                {{ \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($presensi->status_kehadiran == 'hadir')
                                                <span class="badge badge-success">Hadir</span>
                                            @elseif($presensi->status_kehadiran == 'terlambat')
                                                <span class="badge badge-warning">Terlambat</span>
                                                @if($presensi->keterlambatan_menit > 0)
                                                    <small class="text-muted">({{ $presensi->keterlambatan_menit }} menit)</small>
                                                @endif
                                            @elseif($presensi->status_kehadiran == 'izin')
                                                <span class="badge badge-info">Izin</span>
                                            @elseif($presensi->status_kehadiran == 'sakit')
                                                <span class="badge badge-primary">Sakit</span>
                                            @elseif($presensi->status_kehadiran == 'cuti')
                                                <span class="badge badge-secondary">Cuti</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($presensi->status_kehadiran) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.presensi.show', $presensi->id_presensi) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                                            <p class="mb-0">Belum ada data presensi hari ini</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Animations
    $('.card, .small-box, .info-box').hide().fadeIn(600);
    
    // Chart 1: Presensi 7 Hari Terakhir
    const ctx1 = document.getElementById('presensiChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: {!! json_encode($last7Days) !!},
            datasets: [
                {
                    label: 'Hadir',
                    data: {!! json_encode($last7DaysData['hadir']) !!},
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Terlambat',
                    data: {!! json_encode($last7DaysData['terlambat']) !!},
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Izin/Sakit',
                    data: {!! json_encode($last7DaysData['izin']) !!},
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Alpha',
                    data: {!! json_encode($last7DaysData['alpha']) !!},
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Chart 2: Status Kehadiran Pie Chart
    const ctx2 = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Cuti'],
            datasets: [{
                data: [
                    {{ $statusBulanIni['hadir'] }},
                    {{ $statusBulanIni['terlambat'] }},
                    {{ $statusBulanIni['izin'] }},
                    {{ $statusBulanIni['sakit'] }},
                    {{ $statusBulanIni['cuti'] }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#17a2b8',
                    '#007bff',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Chart 3: Presensi per Departemen
    const ctx3 = document.getElementById('departemenChart').getContext('2d');
    new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: {!! json_encode($departemenLabels) !!},
            datasets: [
                {
                    label: 'Hadir',
                    data: {!! json_encode($departemenHadir) !!},
                    backgroundColor: '#28a745'
                },
                {
                    label: 'Total Karyawan',
                    data: {!! json_encode($departemenTotal) !!},
                    backgroundColor: '#6c757d'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Chart 4: Tren Pengajuan
    const ctx4 = document.getElementById('trendPengajuanChart').getContext('2d');
    new Chart(ctx4, {
        type: 'line',
        data: {
            labels: {!! json_encode($trendLabels) !!},
            datasets: [
                {
                    label: 'Izin',
                    data: {!! json_encode($trendIzin) !!},
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Cuti',
                    data: {!! json_encode($trendCuti) !!},
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush

@push('css')
<style>
    .info-box {
        min-height: 90px;
    }
    
    .small-box {
        border-radius: 0.25rem;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }
    
    .small-box .icon {
        transition: all 0.3s ease;
    }
    
    .small-box:hover .icon {
        transform: scale(1.1);
    }
    
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }
    
    .progress {
        height: 3px;
        margin-top: 5px;
    }
    
    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endpush