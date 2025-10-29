@extends('layouts.app')

@section('title', 'Rekap Presensi Bulanan')
@section('icon', 'fa-chart-bar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-white mb-0"><i class="fas fa-chart-bar me-2"></i>Rekap Presensi Bulanan</h4>
    <div>
        <a href="{{ route('presensi.index') }}" class="btn btn-secondary btn-modern me-2">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        <button type="button" class="btn btn-warning btn-modern" data-bs-toggle="modal" data-bs-target="#generateModal">
            <i class="fas fa-sync me-2"></i>Generate Rekap
        </button>
    </div>
</div>

<!-- Filter Form -->
<div class="card-modern mb-4">
    <div class="card-body">
        <form action="{{ route('presensi.rekap') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="bulan" class="form-label text-white">Bulan</label>
                <select class="form-select form-control-modern" id="bulan" name="bulan">
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label for="tahun" class="form-label text-white">Tahun</label>
                <select class="form-select form-control-modern" id="tahun" name="tahun">
                    @for($i = date('Y') - 2; $i <= date('Y'); $i++)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">{{ $statistik['total_karyawan'] }}</div>
            <div class="stats-label">Total Karyawan</div>
            <i class="fas fa-users fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">{{ number_format($statistik['rata_rata_kehadiran'], 1) }}%</div>
            <div class="stats-label">Rata-rata Kehadiran</div>
            <i class="fas fa-check-circle fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">{{ number_format($statistik['rata_rata_terlambat'], 1) }}%</div>
            <div class="stats-label">Rata-rata Terlambat</div>
            <i class="fas fa-clock fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">{{ $statistik['total_alpha'] }}</div>
            <div class="stats-label">Total Alpha</div>
            <i class="fas fa-times-circle fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-chart-line me-2"></i>Persentase Kehadiran Karyawan
                </h5>
            </div>
            <div class="card-body">
                <canvas id="kehadiranChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Distribusi Kehadiran
                </h5>
            </div>
            <div class="card-body">
                <canvas id="distribusiChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Rekap Table -->
<div class="card-modern">
    <div class="card-header bg-dark-blue">
        <h5 class="text-white mb-0">
            <i class="fas fa-table me-2"></i>Rekap Presensi {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern table-hover">
                <thead>
                    <tr>
                        <th>Nama Karyawan</th>
                        <th>NIP</th>
                        <th>Departemen</th>
                        <th>Hadir</th>
                        <th>Terlambat</th>
                        <th>Izin</th>
                        <th>Sakit</th>
                        <th>Cuti</th>
                        <th>Alpha</th>
                        <th>Kehadiran</th>
                        <th>Terlambat</th>
                        <th>Tidak Hadir</th>
                        <th>Total Jam</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($item->karyawan->foto)
                                    <img src="{{ asset('storage/' . $item->karyawan->foto) }}" 
                                         class="rounded-circle me-2" width="32" height="32" 
                                         alt="{{ $item->karyawan->nama_lengkap }}">
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" 
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                                <strong>{{ $item->karyawan->nama_lengkap }}</strong>
                            </div>
                        </td>
                        <td>{{ $item->karyawan->nip }}</td>
                        <td>{{ $item->karyawan->departemen->nama_departemen }}</td>
                        <td><span class="badge bg-success">{{ $item->jumlah_hadir }}</span></td>
                        <td><span class="badge bg-warning">{{ $item->jumlah_terlambat }}</span></td>
                        <td><span class="badge bg-info">{{ $item->jumlah_izin }}</span></td>
                        <td><span class="badge bg-primary">{{ $item->jumlah_sakit }}</span></td>
                        <td><span class="badge bg-secondary">{{ $item->jumlah_cuti }}</span></td>
                        <td><span class="badge bg-danger">{{ $item->jumlah_alpha }}</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $item->persentase_kehadiran }}%"></div>
                                </div>
                                <small>{{ number_format($item->persentase_kehadiran, 1) }}%</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $item->persentase_terlambat }}%"></div>
                                </div>
                                <small>{{ number_format($item->persentase_terlambat, 1) }}%</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $item->persentase_tidak_hadir }}%"></div>
                                </div>
                                <small>{{ number_format($item->persentase_tidak_hadir, 1) }}%</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ number_format($item->total_jam_kerja, 1) }} jam</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data rekap untuk periode ini.</p>
                            <button type="button" class="btn btn-primary-modern btn-modern" data-bs-toggle="modal" data-bs-target="#generateModal">
                                <i class="fas fa-sync me-2"></i>Generate Rekap
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($rekap->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Menampilkan {{ $rekap->firstItem() }} - {{ $rekap->lastItem() }} dari {{ $rekap->total() }} data
            </div>
            {{ $rekap->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Generate Modal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title text-white">Generate Rekap Presensi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-white">Generate rekap presensi untuk periode tertentu?</p>
                <form action="{{ route('presensi.generate-rekap') }}" method="POST" id="generateForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_bulan" class="form-label text-white">Bulan</label>
                                <select class="form-select form-control-modern" id="modal_bulan" name="bulan" required>
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_tahun" class="form-label text-white">Tahun</label>
                                <select class="form-select form-control-modern" id="modal_tahun" name="tahun" required>
                                    @for($i = date('Y') - 2; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="generateForm" class="btn btn-primary-modern">
                    <i class="fas fa-sync me-2"></i>Generate
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data dari controller
    const chartData = @json($chartData);
    
    // Chart Persentase Kehadiran
    const kehadiranCtx = document.getElementById('kehadiranChart').getContext('2d');
    new Chart(kehadiranCtx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Kehadiran (%)',
                    data: chartData.kehadiran,
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Terlambat (%)',
                    data: chartData.terlambat,
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Tidak Hadir (%)',
                    data: chartData.tidak_hadir,
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Persentase (%)'
                    }
                }
            }
        }
    });

    // Chart Distribusi Kehadiran
    const distribusiCtx = document.getElementById('distribusiChart').getContext('2d');
    
    // Hitung total untuk pie chart
    const totalHadir = {{ $statistik['total_hadir'] }};
    const totalTerlambat = {{ $statistik['total_terlambat'] }};
    const totalAlpha = {{ $statistik['total_alpha'] }};
    const totalLainnya = {{ $statistik['total_karyawan'] * $rekap->first()->total_hari_kerja - $statistik['total_hadir'] - $statistik['total_terlambat'] - $statistik['total_alpha'] }};

    new Chart(distribusiCtx, {
        type: 'pie',
        data: {
            labels: ['Hadir', 'Terlambat', 'Alpha', 'Lainnya'],
            datasets: [{
                data: [totalHadir, totalTerlambat, totalAlpha, totalLainnya],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(108, 117, 125, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
@endpush