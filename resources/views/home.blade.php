@extends('layouts.app')

@section('title', 'Dashboard')
@section('icon', 'fa-tachometer-alt')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-number">{{ $stats['total_fakultas'] }}</div>
                <div class="stats-label">Total Fakultas</div>
                <i class="fas fa-university fa-2x mt-2 opacity-50"></i>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-number">{{ $stats['total_departemen'] }}</div>
                <div class="stats-label">Total Departemen</div>
                <i class="fas fa-building fa-2x mt-2 opacity-50"></i>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-number">{{ $stats['total_jabatan'] }}</div>
                <div class="stats-label">Total Jabatan</div>
                <i class="fas fa-briefcase fa-2x mt-2 opacity-50"></i>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-number">{{ $stats['total_karyawan'] }}</div>
                <div class="stats-label">Karyawan Aktif</div>
                <i class="fas fa-users fa-2x mt-2 opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions & Presensi -->
        <div class="col-lg-8">
            <!-- Quick Actions -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-rocket me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('karyawan.create') }}" class="btn btn-primary-modern btn-modern w-100 py-3">
                                <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                Tambah Karyawan
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('fakultas.create') }}" class="btn btn-success btn-modern w-100 py-3">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                Tambah Fakultas
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('departemen.create') }}" class="btn btn-info btn-modern w-100 py-3">
                                <i class="fas fa-building fa-2x mb-2"></i><br>
                                Tambah Departemen
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('jabatan.create') }}" class="btn btn-warning btn-modern w-100 py-3">
                                <i class="fas fa-briefcase fa-2x mb-2"></i><br>
                                Tambah Jabatan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Presensi Cepat -->
            <div class="card card-modern">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-fingerprint me-2"></i>Presensi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('presensi.masuk') }}" class="btn btn-success btn-modern btn-block btn-lg py-3">
                                <i class="fas fa-sign-in-alt fa-2x me-2"></i>
                                Presensi Masuk
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('presensi.keluar') }}" class="btn btn-warning btn-modern btn-block btn-lg py-3">
                                <i class="fas fa-sign-out-alt fa-2x me-2"></i>
                                Presensi Keluar
                            </a>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Hari Ini: {{ now()->translatedFormat('l, d F Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Info -->
        <div class="col-lg-4">
            <div class="card card-modern">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>System Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-white">
                        <p><i class="fas fa-calendar me-2"></i> {{ now()->translatedFormat('l, d F Y') }}</p>
                        <p><i class="fas fa-clock me-2"></i> <span id="live-time">{{ now()->format('H:i:s') }}</span></p>
                        <p><i class="fas fa-database me-2"></i> Laravel {{ app()->version() }}</p>
                        <p><i class="fas fa-user me-2"></i> {{ Auth::user()->name }}</p>
                        <p><i class="fas fa-server me-2"></i> PHP {{ phpversion() }}</p>
                    </div>
                </div>
            </div>

            <!-- Today's Stats -->
            <div class="card card-modern mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistik Hari Ini
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-white">
                        <p><i class="fas fa-calendar-check me-2 text-success"></i> 
                            Presensi: <strong>{{ $stats['presensi_hari_ini'] }}</strong>
                        </p>
                        <p><i class="fas fa-user-clock me-2 text-warning"></i> 
                            Terlambat: <strong>{{ $stats['karyawan_terlambat'] }}</strong>
                        </p>
                        @php
                            $percentage = $stats['total_karyawan'] > 0 ? 
                                round(($stats['presensi_hari_ini'] / $stats['total_karyawan']) * 100, 1) : 0;
                        @endphp
                        <p><i class="fas fa-percentage me-2 text-info"></i> 
                            Kehadiran: <strong>{{ $percentage }}%</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Aktivitas</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ now()->subMinutes(5)->format('H:i') }}</td>
                                    <td>Login</td>
                                    <td>{{ Auth::user()->name }} masuk ke sistem</td>
                                    <td><span class="badge badge-success badge-modern">Success</span></td>
                                </tr>
                                @if($stats['presensi_hari_ini'] > 0)
                                <tr>
                                    <td>{{ now()->subMinutes(30)->format('H:i') }}</td>
                                    <td>Presensi</td>
                                    <td>{{ $stats['presensi_hari_ini'] }} karyawan telah presensi</td>
                                    <td><span class="badge badge-info badge-modern">Info</span></td>
                                </tr>
                                @endif
                                <tr>
                                    <td>{{ now()->subHours(1)->format('H:i') }}</td>
                                    <td>System</td>
                                    <td>Sistem berjalan normal</td>
                                    <td><span class="badge badge-success badge-modern">Active</span></td>
                                </tr>
                            </tbody>
                        </table>
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
        // Update live time
        function updateLiveTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            $('#live-time').text(timeString);
        }
        
        setInterval(updateLiveTime, 1000);
    });
</script>
@endpush