@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('icon', 'fa-tachometer-alt')

@section('content')
    <!-- Stats Cards Row 1 - Compact -->
    <div class="row g-2 mb-2">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-gradient-info text-white border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-university fa-3x opacity-50"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0 fw-bold">{{ $totalFakultas }}</h2>
                            <p class="mb-0 small">Total Fakultas</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 p-2">
                    <a href="{{ route('admin.fakultas.index') }}" class="text-white text-decoration-none small">
                        <i class="fas fa-arrow-circle-right me-1"></i>Lihat Detail
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-gradient-success text-white border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-building fa-3x opacity-50"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0 fw-bold">{{ $totalDepartemen }}</h2>
                            <p class="mb-0 small">Total Departemen</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 p-2">
                    <a href="{{ route('admin.departemen.index') }}" class="text-white text-decoration-none small">
                        <i class="fas fa-arrow-circle-right me-1"></i>Lihat Detail
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-gradient-warning text-white border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-briefcase fa-3x opacity-50"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0 fw-bold">{{ $totalJabatan }}</h2>
                            <p class="mb-0 small">Total Jabatan</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 p-2">
                    <a href="{{ route('admin.jabatan.index') }}" class="text-white text-decoration-none small">
                        <i class="fas fa-arrow-circle-right me-1"></i>Lihat Detail
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-gradient-danger text-white border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0 fw-bold">{{ $totalKaryawan }}</h2>
                            <p class="mb-0 small">Karyawan Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 p-2">
                    <a href="{{ route('admin.karyawan.index') }}" class="text-white text-decoration-none small">
                        <i class="fas fa-arrow-circle-right me-1"></i>Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Presensi Hari Ini & Pengajuan - Compact -->
    <div class="row g-2 mb-2">
        <!-- Presensi Hari Ini -->
        <div class="col-lg-8">
            <div class="card border-0 h-100">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>Presensi Hari Ini
                        </h6>
                        <span class="badge bg-primary">{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="row g-2 mb-2">
                        <div class="col-6 col-lg-3">
                            <div class="card bg-success text-white border-0 mb-0">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-check fa-2x opacity-50"></i>
                                        </div>
                                        <div class="text-end">
                                            <h3 class="mb-0 fw-bold">{{ $hadirHariIni }}</h3>
                                            <small>Hadir</small>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 3px;">
                                        <div class="progress-bar bg-white"
                                            style="width: {{ $totalKaryawan > 0 ? ($hadirHariIni / $totalKaryawan) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                    <small
                                        class="d-block mt-1">{{ $totalKaryawan > 0 ? round(($hadirHariIni / $totalKaryawan) * 100, 1) : 0 }}%</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="card bg-warning text-white border-0 mb-0">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-clock fa-2x opacity-50"></i>
                                        </div>
                                        <div class="text-end">
                                            <h3 class="mb-0 fw-bold">{{ $terlambatHariIni }}</h3>
                                            <small>Terlambat</small>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 3px;">
                                        <div class="progress-bar bg-white"
                                            style="width: {{ $totalKaryawan > 0 ? ($terlambatHariIni / $totalKaryawan) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                    <small
                                        class="d-block mt-1">{{ $totalKaryawan > 0 ? round(($terlambatHariIni / $totalKaryawan) * 100, 1) : 0 }}%</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="card bg-info text-white border-0 mb-0">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-file-medical fa-2x opacity-50"></i>
                                        </div>
                                        <div class="text-end">
                                            <h3 class="mb-0 fw-bold">{{ $izinHariIni }}</h3>
                                            <small>Izin</small>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 3px;">
                                        <div class="progress-bar bg-white"
                                            style="width: {{ $totalKaryawan > 0 ? ($izinHariIni / $totalKaryawan) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                    <small
                                        class="d-block mt-1">{{ $totalKaryawan > 0 ? round(($izinHariIni / $totalKaryawan) * 100, 1) : 0 }}%</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="card bg-danger text-white border-0 mb-0">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-times fa-2x opacity-50"></i>
                                        </div>
                                        <div class="text-end">
                                            <h3 class="mb-0 fw-bold">{{ $alphaHariIni }}</h3>
                                            <small>Alpha</small>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 3px;">
                                        <div class="progress-bar bg-white"
                                            style="width: {{ $totalKaryawan > 0 ? ($alphaHariIni / $totalKaryawan) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                    <small
                                        class="d-block mt-1">{{ $totalKaryawan > 0 ? round(($alphaHariIni / $totalKaryawan) * 100, 1) : 0 }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-dark text-white border-0 mb-0">
                        <div class="card-body text-center py-2">
                            <h2 class="mb-0 fw-bold text-warning">{{ $persentaseKehadiranHariIni }}%</h2>
                            <small>Tingkat Kehadiran Hari Ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Right - Pengajuan & Stats -->
        <div class="col-lg-4">
            <!-- Pengajuan Pending -->
            <div class="card bg-gradient-danger text-white border-0 mb-2">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $totalPengajuanPending }}</h4>
                            <small>Pengajuan Pending</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                    </div>
                    <hr class="bg-white my-2 opacity-25">
                    <div class="d-flex justify-content-between mb-1">
                        <small><i class="fas fa-envelope me-1"></i>Izin</small>
                        <span class="badge bg-light text-dark">{{ $izinPending }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small><i class="fas fa-calendar-alt me-1"></i>Cuti</small>
                        <span class="badge bg-light text-dark">{{ $cutiPending }}</span>
                    </div>
                    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-light btn-sm w-100">
                        <i class="fas fa-eye me-1"></i>Lihat Semua
                    </a>
                </div>
            </div>

            <!-- Stats Bulan Ini -->
            <div class="card bg-gradient-primary text-white border-0 mb-0">
                <div class="card-body p-2">
                    <h6 class="mb-2"><i class="fas fa-chart-line me-2"></i>Statistik Bulan Ini</h6>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light border-opacity-25">
                        <small>Total Presensi</small>
                        <span class="badge bg-light text-dark">{{ $presensiTotalBulanIni }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light border-opacity-25">
                        <small>Total Terlambat</small>
                        <span class="badge bg-warning text-white">{{ $terlambatBulanIni }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small>Rata-rata Keterlambatan</small>
                        <span class="badge bg-light text-dark">{{ $avgKeterlambatan }} menit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row - Compact -->
    <div class="row g-2 mb-2">
        <!-- Grafik Presensi 7 Hari -->
        <div class="col-lg-8">
            <div class="card border-0 h-100">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Tren Presensi 7 Hari Terakhir</h6>
                </div>
                <div class="card-body p-2">
                    <div style="position: relative; height: 280px; width: 100%;">
                        <canvas id="presensiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart Status -->
        <div class="col-lg-4">
            <div class="card border-0 h-100">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Status Bulan Ini</h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 - Compact -->
    <div class="row g-2 mb-2">
        <!-- Departemen Chart -->
        <div class="col-lg-6">
            <div class="card border-0 h-100">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>Presensi per Departemen</h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="departemenChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Tren Pengajuan -->
        <div class="col-lg-6">
            <div class="card border-0 h-100">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Tren Pengajuan 6 Bulan</h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="trendPengajuanChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row - Compact -->
    <div class="row g-2 mb-2">
        <!-- Top Terlambat -->
        <div class="col-lg-6">
            <div class="card border-0 h-100">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Top 5 Sering Terlambat
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-dark table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-white">Nama</th>
                                    <th class="text-white">Dept.</th>
                                    <th class="text-center text-white">Frek.</th>
                                    <th class="text-center text-white">Menit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topTerlambat as $karyawan)
                                    <tr>
                                        <td class="text-white">
                                            <strong>{{ $karyawan->nama_lengkap }}</strong>
                                            <br><small class="text-muted">{{ $karyawan->nip }}</small>
                                        </td>
                                        <td class="text-white-50">{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-warning text-dark">{{ $karyawan->total_terlambat }}x</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $karyawan->total_menit_terlambat }}m</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-white-50 py-3">
                                            <i class="fas fa-check-circle me-2"></i>Tidak ada data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kehadiran Sempurna -->
        <div class="col-lg-6">
            <div class="card border-0 h-100">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-trophy me-2 text-warning"></i>Kehadiran Sempurna
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-dark table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-white">Nama</th>
                                    <th class="text-white">Dept.</th>
                                    <th class="text-center text-white">Hadir</th>
                                    <th class="text-center text-white">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($karyawanPerfect as $karyawan)
                                    <tr>
                                        <td class="text-white">
                                            <strong>{{ $karyawan->nama_lengkap }}</strong>
                                            <br><small class="text-muted">{{ $karyawan->nip }}</small>
                                        </td>
                                        <td class="text-white-50">{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $karyawan->total_hadir }}</span>
                                        </td>
                                        <td class="text-center">
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-white-50 py-3">
                                            <i class="fas fa-info-circle me-2"></i>Belum ada data
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

    <!-- Latest Presensi - Compact -->
    <div class="row g-2">
        <div class="col-12">
            <div class="card border-0">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-history me-2"></i>Presensi Terbaru Hari Ini
                        </h6>
                        <a href="{{ route('admin.presensi.monitoring') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-dark table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-white">Karyawan</th>
                                    <th class="text-white">Departemen</th>
                                    <th class="text-white">Jabatan</th>
                                    <th class="text-center text-white">Masuk</th>
                                    <th class="text-center text-white">Keluar</th>
                                    <th class="text-center text-white">Status</th>
                                    <th class="text-center text-white">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestPresensi as $presensi)
                                    <tr>
                                        <td class="text-white">
                                            <strong>{{ $presensi->karyawan->nama_lengkap ?? '-' }}</strong>
                                            <br><small class="text-muted">{{ $presensi->karyawan->nip ?? '-' }}</small>
                                        </td>
                                        <td class="text-white-50">
                                            {{ $presensi->karyawan->departemen->nama_departemen ?? '-' }}</td>
                                        <td class="text-white-50">{{ $presensi->karyawan->jabatan->nama_jabatan ?? '-' }}
                                        </td>
                                        <td class="text-center text-white-50">
                                            @if ($presensi->jam_masuk)
                                                <i class="fas fa-clock text-success me-1"></i>
                                                {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-white-50">
                                            @if ($presensi->jam_keluar)
                                                <i class="fas fa-clock text-danger me-1"></i>
                                                {{ \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($presensi->status_kehadiran == 'hadir')
                                                <span class="badge bg-success">Hadir</span>
                                            @elseif($presensi->status_kehadiran == 'terlambat')
                                                <span class="badge bg-warning text-dark">Terlambat</span>
                                                @if ($presensi->keterlambatan_menit > 0)
                                                    <br><small
                                                        class="text-muted">({{ $presensi->keterlambatan_menit }}m)</small>
                                                @endif
                                            @elseif($presensi->status_kehadiran == 'izin')
                                                <span class="badge bg-info">Izin</span>
                                            @elseif($presensi->status_kehadiran == 'sakit')
                                                <span class="badge bg-primary">Sakit</span>
                                            @elseif($presensi->status_kehadiran == 'cuti')
                                                <span class="badge bg-secondary">Cuti</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($presensi->status_kehadiran) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.presensi.monitoring.show', $presensi->id_presensi) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-white-50 py-4">
                                            <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                            Belum ada data presensi hari ini
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
            // Smooth animations
            $('.card').hide().fadeIn(400);

            // Chart Default Config for Dark Mode
            Chart.defaults.color = '#fff';
            Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

            // Chart 1: Presensi 7 Hari
            new Chart(document.getElementById('presensiChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($last7Days) !!},
                    datasets: [{
                            label: 'Hadir',
                            data: {!! json_encode($last7DaysData['hadir']) !!},
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2
                        },
                        {
                            label: 'Terlambat',
                            data: {!! json_encode($last7DaysData['terlambat']) !!},
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.2)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2
                        },
                        {
                            label: 'Izin',
                            data: {!! json_encode($last7DaysData['izin']) !!},
                            borderColor: '#17a2b8',
                            backgroundColor: 'rgba(23, 162, 184, 0.2)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2
                        },
                        {
                            label: 'Alpha',
                            data: {!! json_encode($last7DaysData['alpha']) !!},
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.2)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 2.5,
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 10,
                            left: 10,
                            right: 10
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#fff',
                                boxWidth: 12,
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 10,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#fff',
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                color: '#fff',
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                                drawBorder: false
                            }
                        }
                    }
                }
            });
            // Chart 2: Status Bulan Ini (Pie Chart)
            new Chart(document.getElementById('statusChart'), {
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
                            '#28a745', // Hadir - Green
                            '#ffc107', // Terlambat - Yellow
                            '#17a2b8', // Izin - Cyan
                            '#007bff', // Sakit - Blue
                            '#6c757d' // Cuti - Gray
                        ],
                        borderColor: '#2d3236',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#fff',
                                boxWidth: 12,
                                padding: 10,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // Chart 3: Departemen Bar
            new Chart(document.getElementById('departemenChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($departemenLabels) !!},
                    datasets: [{
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
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#fff',
                                boxWidth: 12,
                                padding: 10
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#fff'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#fff'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    }
                }
            });

            // Chart 4: Tren Pengajuan
            new Chart(document.getElementById('trendPengajuanChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($trendLabels) !!},
                    datasets: [{
                            label: 'Izin',
                            data: {!! json_encode($trendIzin) !!},
                            borderColor: '#17a2b8',
                            backgroundColor: 'rgba(23, 162, 184, 0.2)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Cuti',
                            data: {!! json_encode($trendCuti) !!},
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.2)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#fff',
                                boxWidth: 12,
                                padding: 10
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#fff'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#fff'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
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
        /* Compact Layout - Bootstrap 5 */
        .content-wrapper {
            background-color: #1a1d20 !important;
        }

        .card {
            background-color: #2d3236 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .4) !important;
            border-radius: 0.375rem !important;
        }

        .card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        /* Gradient Cards */
        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #208637 100%) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        }

        /* Table Dark Mode */
        .table-dark {
            --bs-table-bg: #2d3236 !important;
            --bs-table-striped-bg: #343a40 !important;
            --bs-table-hover-bg: #3a4046 !important;
            color: #fff !important;
        }

        .table-dark th {
            border-color: rgba(255, 255, 255, 0.1) !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
        }

        .table-dark td {
            border-color: rgba(255, 255, 255, 0.05) !important;
            font-size: 0.875rem !important;
        }

        /* Text Colors */
        .text-white-50 {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        /* Compact Spacing */
        .row.g-2 {
            --bs-gutter-x: 0.5rem;
            --bs-gutter-y: 0.5rem;
        }

        /* Badge Styling */
        .badge {
            font-weight: 500 !important;
            padding: 0.375rem 0.65rem !important;
            font-size: 0.75rem !important;
        }

        /* Progress Bar */
        .progress {
            background-color: rgba(255, 255, 255, 0.2) !important;
            height: 3px !important;
        }

        .progress-bar {
            background-color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Icon Opacity */
        .opacity-50 {
            opacity: 0.5 !important;
        }

        .opacity-25 {
            opacity: 0.25 !important;
        }

        /* Hover Effects */
        .card:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .6) !important;
        }

        /* Button Compact */
        .btn-sm {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.8rem !important;
        }

        /* Remove Extra Padding */
        .card-body.p-0 .table {
            margin-bottom: 0 !important;
        }

        /* Responsive Text */
        @media (max-width: 768px) {

            h2,
            .h2 {
                font-size: 1.5rem !important;
            }

            h3,
            .h3 {
                font-size: 1.25rem !important;
            }

            h4,
            .h4 {
                font-size: 1.1rem !important;
            }

            h6,
            .h6 {
                font-size: 0.9rem !important;
            }
        }

        /* Chart Container */
        canvas {
            max-height: 300px !important;
        }

        /* Scrollbar Dark */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1d20;
        }

        ::-webkit-scrollbar-thumb {
            background: #4a5056;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #5a6066;
        }

        /* No wrap for small text */
        small {
            white-space: nowrap;
        }

        /* Border Utilities */
        .border-opacity-25 {
            --bs-border-opacity: 0.25;
        }

        /* Flex utilities */
        .flex-shrink-0 {
            flex-shrink: 0 !important;
        }

        .flex-grow-1 {
            flex-grow: 1 !important;
        }

        /* Additional spacing utilities */
        .mb-2 {
            margin-bottom: 0.5rem !important;
        }

        .p-2 {
            padding: 0.5rem !important;
        }

        .py-2 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        /* Table responsive improvements */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Card improvements */
        .card.h-100 {
            height: 100% !important;
        }

        /* Text utilities */
        .text-end {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-decoration-none {
            text-decoration: none !important;
        }

        .fw-bold {
            font-weight: 700 !important;
        }

        /* Display utilities */
        .d-block {
            display: block !important;
        }

        .d-flex {
            display: flex !important;
        }

        /* Alignment utilities */
        .align-items-center {
            align-items: center !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        /* Margin utilities */
        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .mt-1 {
            margin-top: 0.25rem !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .me-1 {
            margin-right: 0.25rem !important;
        }

        .me-2 {
            margin-right: 0.5rem !important;
        }

        .ms-3 {
            margin-left: 1rem !important;
        }

        .my-2 {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        /* Width utilities */
        .w-100 {
            width: 100% !important;
        }
    </style>
@endpush
