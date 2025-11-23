@extends('layouts.user')

@section('title', 'Rekap Presensi Saya')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Filter Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line"></i> Rekap Presensi Saya
                    </h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.presensi.rekap') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pilih Bulan</label>
                                    <select name="bulan" class="form-control" onchange="this.form.submit()">
                                        @foreach($months as $month)
                                            <option value="{{ $month['value'] }}" {{ $month['selected'] ? 'selected' : '' }}>
                                                {{ $month['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Hadir</h6>
                                    <h2 class="mb-0">{{ $rekap['jumlah_hadir'] }}</h2>
                                </div>
                                <i class="fas fa-check-circle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Terlambat</h6>
                                    <h2 class="mb-0">{{ $rekap['jumlah_terlambat'] }}</h2>
                                    <small>Avg: {{ $rekap['rata_rata_terlambat'] }} mnt</small>
                                </div>
                                <i class="fas fa-clock fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Izin/Sakit/Cuti</h6>
                                    <h2 class="mb-0">{{ $rekap['jumlah_izin'] + $rekap['jumlah_sakit'] + $rekap['jumlah_cuti'] }}</h2>
                                    <small>{{ $rekap['jumlah_izin'] }}I, {{ $rekap['jumlah_sakit'] }}S, {{ $rekap['jumlah_cuti'] }}C</small>
                                </div>
                                <i class="fas fa-file-medical fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Alpha</h6>
                                    <h2 class="mb-0">{{ $rekap['jumlah_alpha'] }}</h2>
                                </div>
                                <i class="fas fa-times-circle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Ringkasan Kehadiran</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <strong>Total Hari Kerja:</strong>
                                        <span class="float-right">{{ $rekap['total_hari_kerja'] }} hari</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Persentase Kehadiran:</strong>
                                        <span class="float-right">
                                            @if($rekap['persentase_kehadiran'] >= 90)
                                                <span class="badge badge-success">{{ $rekap['persentase_kehadiran'] }}%</span>
                                            @elseif($rekap['persentase_kehadiran'] >= 75)
                                                <span class="badge badge-warning">{{ $rekap['persentase_kehadiran'] }}%</span>
                                            @else
                                                <span class="badge badge-danger">{{ $rekap['persentase_kehadiran'] }}%</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <strong>Total Menit Terlambat:</strong>
                                        <span class="float-right">{{ $rekap['total_menit_terlambat'] }} menit</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Rata-rata Terlambat:</strong>
                                        <span class="float-right">{{ $rekap['rata_rata_terlambat'] }} menit</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <strong>Total Jam Kerja:</strong>
                                        <span class="float-right">{{ $rekap['total_jam_kerja'] }} jam</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Status:</strong>
                                        <span class="float-right">
                                            @if($rekap['persentase_kehadiran'] >= 90)
                                                <span class="badge badge-success">Excellent</span>
                                            @elseif($rekap['persentase_kehadiran'] >= 75)
                                                <span class="badge badge-warning">Good</span>
                                            @else
                                                <span class="badge badge-danger">Need Improvement</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <label>Kehadiran</label>
                                <div class="progress" style="height: 25px;">
                                    @php
                                        $hadir_persen = $rekap['total_hari_kerja'] > 0 ? ($rekap['jumlah_hadir'] / $rekap['total_hari_kerja']) * 100 : 0;
                                        $terlambat_persen = $rekap['total_hari_kerja'] > 0 ? ($rekap['jumlah_terlambat'] / $rekap['total_hari_kerja']) * 100 : 0;
                                        $izin_persen = $rekap['total_hari_kerja'] > 0 ? (($rekap['jumlah_izin'] + $rekap['jumlah_sakit'] + $rekap['jumlah_cuti']) / $rekap['total_hari_kerja']) * 100 : 0;
                                        $alpha_persen = $rekap['total_hari_kerja'] > 0 ? ($rekap['jumlah_alpha'] / $rekap['total_hari_kerja']) * 100 : 0;
                                    @endphp
                                    
                                    @if($hadir_persen > 0)
                                    <div class="progress-bar bg-success" style="width: {{ $hadir_persen }}%">
                                        {{ round($hadir_persen) }}%
                                    </div>
                                    @endif
                                    
                                    @if($terlambat_persen > 0)
                                    <div class="progress-bar bg-warning" style="width: {{ $terlambat_persen }}%">
                                        {{ round($terlambat_persen) }}%
                                    </div>
                                    @endif
                                    
                                    @if($izin_persen > 0)
                                    <div class="progress-bar bg-info" style="width: {{ $izin_persen }}%">
                                        {{ round($izin_persen) }}%
                                    </div>
                                    @endif
                                    
                                    @if($alpha_persen > 0)
                                    <div class="progress-bar bg-danger" style="width: {{ $alpha_persen }}%">
                                        {{ round($alpha_persen) }}%
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <small>
                                        <span class="badge badge-success">Hadir</span>
                                        <span class="badge badge-warning">Terlambat</span>
                                        <span class="badge badge-info">Izin/Sakit/Cuti</span>
                                        <span class="badge badge-danger">Alpha</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Table -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detail Presensi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Status</th>
                                    <th>Terlambat</th>
                                    <th>Total Jam</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presensiList as $presensi)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($presensi->tanggal_presensi)->format('d/m/Y') }}</td>
                                    <td>{{ $presensi->jam_masuk ?? '-' }}</td>
                                    <td>{{ $presensi->jam_keluar ?? '-' }}</td>
                                    <td>
                                        @if($presensi->status_kehadiran === 'hadir')
                                            <span class="badge badge-success">Hadir</span>
                                        @elseif($presensi->status_kehadiran === 'terlambat')
                                            <span class="badge badge-warning">Terlambat</span>
                                        @elseif($presensi->status_kehadiran === 'izin')
                                            <span class="badge badge-info">Izin</span>
                                        @elseif($presensi->status_kehadiran === 'sakit')
                                            <span class="badge badge-secondary">Sakit</span>
                                        @elseif($presensi->status_kehadiran === 'cuti')
                                            <span class="badge badge-primary">Cuti</span>
                                        @else
                                            <span class="badge badge-danger">Alpha</span>
                                        @endif
                                    </td>
                                    <td>{{ $presensi->keterlambatan_menit > 0 ? $presensi->keterlambatan_menit . ' menit' : '-' }}</td>
                                    <td>{{ $presensi->total_jam_kerja ? number_format($presensi->total_jam_kerja, 1) . ' jam' : '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.presensi.show', $presensi->id_presensi) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data presensi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .opacity-50 {
        opacity: 0.5;
    }
</style>
@endpush