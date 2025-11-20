@extends('layouts.app')

@section('title', 'Dashboard')
@section('icon', 'fa-tachometer-alt')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Statistik Cards -->
        <div class="col-lg-3 col-6">
            <div class="stats-card">
                <div class="stats-number">150</div>
                <div class="stats-label">Total Karyawan</div>
                <i class="fas fa-users fa-2x mt-2"></i>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="stats-card" style="background: linear-gradient(145deg, #27ae60, #219653);">
                <div class="stats-number">142</div>
                <div class="stats-label">Hadir Hari Ini</div>
                <i class="fas fa-calendar-check fa-2x mt-2"></i>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="stats-card" style="background: linear-gradient(145deg, #e74c3c, #c0392b);">
                <div class="stats-number">8</div>
                <div class="stats-label">Tidak Hadir</div>
                <i class="fas fa-user-times fa-2x mt-2"></i>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="stats-card" style="background: linear-gradient(145deg, #f39c12, #e67e22);">
                <div class="stats-number">95%</div>
                <div class="stats-label">Presentase Kehadiran</div>
                <i class="fas fa-chart-line fa-2x mt-2"></i>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Presensi Cepat -->
        <div class="col-md-6">
            <div class="card card-modern">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-fingerprint me-2"></i>
                        Presensi Cepat
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('presensi.masuk') }}" class="btn btn-success btn-modern btn-block btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Masuk
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('presensi.keluar') }}" class="btn btn-warning btn-modern btn-block btn-lg">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Keluar
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Jam Server: <span id="server-time">{{ now()->format('H:i:s') }}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Presensi Terbaru -->
        <div class="col-md-6">
            <div class="card card-modern">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history me-2"></i>
                        Presensi Terbaru
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Doe</td>
                                    <td>08:00:15</td>
                                    <td><span class="badge badge-success badge-modern">Tepat Waktu</span></td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>08:05:30</td>
                                    <td><span class="badge badge-warning badge-modern">Terlambat</span></td>
                                </tr>
                                <tr>
                                    <td>Bob Johnson</td>
                                    <td>07:55:10</td>
                                    <td><span class="badge badge-success badge-modern">Tepat Waktu</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Presensi -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        Grafik Presensi 7 Hari Terakhir
                    </h3>
                </div>
                <div class="card-body">
                    <div style="height: 300px; background: #2c3e50; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #e9ecef;">
                        <div class="text-center">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <p>Grafik Presensi akan ditampilkan di sini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Update waktu server setiap detik
        function updateServerTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            $('#server-time').text(timeString);
        }
        
        setInterval(updateServerTime, 1000);
        
        // Inisialisasi tooltip
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush