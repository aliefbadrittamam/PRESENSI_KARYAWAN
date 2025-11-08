@extends('layouts.app')

@section('title', 'Detail Presensi')
@section('icon', 'fa-calendar-check')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Presensi Info Card -->
        <div class="card-modern text-center p-4">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" 
                 style="width: 100px; height: 100px;">
                <i class="fas fa-calendar-check fa-3x text-white"></i>
            </div>
            <h4 class="text-white">{{ $presensi->karyawan->nama_lengkap }}</h4>
            <p class="text-muted">{{ $presensi->karyawan->nip }}</p>
            <div class="mb-2">
                <span class="badge bg-{{ $presensi->status_kehadiran_color }} badge-modern">
                    {{ strtoupper($presensi->status_kehadiran) }}
                </span>
                <span class="badge bg-{{ $presensi->status_verifikasi == 'verified' ? 'success' : ($presensi->status_verifikasi == 'rejected' ? 'danger' : 'warning') }} badge-modern ms-1">
                    {{ strtoupper($presensi->status_verifikasi) }}
                </span>
            </div>
            <small class="text-muted">{{ $presensi->tanggal_presensi->format('d F Y') }}</small>
        </div>
        
        <!-- Quick Stats -->
        <div class="card-modern mt-4 p-4">
            <h6 class="text-primary mb-3"><i class="fas fa-chart-pie me-2"></i>Statistik Presensi</h6>
            <div class="text-white-50">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-clock me-2"></i>Shift</span>
                    <span class="badge bg-dark">{{ $presensi->shift->kode_shift }}</span>
                </div>
                @if($presensi->keterlambatan_menit > 0)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-hourglass-half me-2"></i>Keterlambatan</span>
                    <span class="badge bg-warning">{{ $presensi->keterlambatan_menit }} menit</span>
                </div>
                @endif
                @if($presensi->total_jam_kerja)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-business-time me-2"></i>Total Jam Kerja</span>
                    <span class="badge bg-info">{{ number_format($presensi->total_jam_kerja, 1) }} jam</span>
                </div>
                @endif
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-calendar me-2"></i>Tanggal</span>
                    <small>{{ $presensi->created_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>

        <!-- Karyawan Info -->
        <div class="card-modern mt-4 p-4">
            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Informasi Karyawan</h6>
            <div class="text-center">
                @if($presensi->karyawan->foto)
                    <img src="{{ asset('storage/' . $presensi->karyawan->foto) }}" 
                         class="rounded-circle mb-2" width="60" height="60" 
                         alt="{{ $presensi->karyawan->nama_lengkap }}">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-2" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                @endif
                <h6 class="text-white mb-1">{{ $presensi->karyawan->nama_lengkap }}</h6>
                <small class="text-muted">{{ $presensi->karyawan->nip }}</small>
                <div class="mt-2">
                    <span class="badge bg-info">{{ $presensi->karyawan->jabatan->nama_jabatan }}</span>
                    <span class="badge bg-dark ms-1">{{ $presensi->karyawan->departemen->nama_departemen }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Main Details -->
        <div class="card-modern p-4">
            <h5 class="text-white mb-4"><i class="fas fa-info-circle me-2"></i>Detail Presensi</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label text-muted">Tanggal Presensi</label>
                        <p class="text-white">
                            <i class="fas fa-calendar me-2 text-primary"></i>
                            <strong>{{ $presensi->tanggal_presensi->format('d F Y') }}</strong>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Shift Kerja</label>
                        <p class="text-white">
                            <i class="fas fa-clock me-2 text-warning"></i>
                            {{ $presensi->shift->nama_shift }}
                            <span class="badge bg-dark ms-2">{{ $presensi->shift->kode_shift }}</span>
                        </p>
                        <small class="text-muted">
                            Jam: {{ \Carbon\Carbon::parse($presensi->shift->jam_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($presensi->shift->jam_selesai)->format('H:i') }}
                        </small>
                    </div>

                    <!-- Presensi Masuk -->
                    <div class="mb-4">
                        <label class="form-label text-muted">Presensi Masuk</label>
                        @if($presensi->jam_masuk)
                            <p class="text-white">
                                <i class="fas fa-sign-in-alt me-2 text-success"></i>
                                <strong>{{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s') }}</strong>
                            </p>
                            @if($presensi->confidence_score_masuk)
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Face ID Confidence:</small>
                                <span class="badge bg-{{ $presensi->confidence_score_masuk >= 0.75 ? 'success' : 'warning' }}">
                                    {{ number_format($presensi->confidence_score_masuk * 100, 1) }}%
                                </span>
                            </div>
                            @endif
                        @else
                            <p class="text-muted">-</p>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label text-muted">Status Kehadiran</label>
                        <p class="text-white">
                            <span class="badge bg-{{ $presensi->status_kehadiran_color }} badge-modern">
                                <i class="fas fa-{{ $presensi->status_kehadiran == 'hadir' ? 'check' : ($presensi->status_kehadiran == 'terlambat' ? 'clock' : 'envelope') }} me-1"></i>
                                {{ strtoupper($presensi->status_kehadiran) }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Status Verifikasi</label>
                        <p class="text-white">
                            <span class="badge bg-{{ $presensi->status_verifikasi == 'verified' ? 'success' : ($presensi->status_verifikasi == 'rejected' ? 'danger' : 'warning') }} badge-modern">
                                {{ strtoupper($presensi->status_verifikasi) }}
                            </span>
                        </p>
                        @if($presensi->alasan_reject)
                        <small class="text-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            {{ $presensi->alasan_reject }}
                        </small>
                        @endif
                    </div>

                    <!-- Presensi Keluar -->
                    <div class="mb-4">
                        <label class="form-label text-muted">Presensi Keluar</label>
                        @if($presensi->jam_keluar)
                            <p class="text-white">
                                <i class="fas fa-sign-out-alt me-2 text-danger"></i>
                                <strong>{{ \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i:s') }}</strong>
                            </p>
                            @if($presensi->confidence_score_keluar)
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Face ID Confidence:</small>
                                <span class="badge bg-{{ $presensi->confidence_score_keluar >= 0.75 ? 'success' : 'warning' }}">
                                    {{ number_format($presensi->confidence_score_keluar * 100, 1) }}%
                                </span>
                            </div>
                            @endif
                        @else
                            <p class="text-muted">-</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lokasi Presensi -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Masuk</h6>
                    @if($presensi->latitude_masuk && $presensi->longitude_masuk)
                        <div class="card bg-dark border-secondary">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Latitude</small>
                                        <strong class="text-white">{{ $presensi->latitude_masuk }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Longitude</small>
                                        <strong class="text-white">{{ $presensi->longitude_masuk }}</strong>
                                    </div>
                                </div>
                                @if($presensi->accuracy_masuk)
                                <div class="mt-2">
                                    <small class="text-muted d-block">Akurasi GPS</small>
                                    <span class="badge bg-{{ $presensi->accuracy_masuk <= 50 ? 'success' : 'warning' }}">
                                        {{ number_format($presensi->accuracy_masuk, 1) }} meter
                                    </span>
                                </div>
                                @endif
                                @if($presensi->alamat_masuk)
                                <div class="mt-2">
                                    <small class="text-muted d-block">Alamat</small>
                                    <small class="text-white">{{ $presensi->alamat_masuk }}</small>
                                </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Tidak ada data lokasi masuk</p>
                    @endif
                </div>

                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Keluar</h6>
                    @if($presensi->latitude_keluar && $presensi->longitude_keluar)
                        <div class="card bg-dark border-secondary">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Latitude</small>
                                        <strong class="text-white">{{ $presensi->latitude_keluar }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Longitude</small>
                                        <strong class="text-white">{{ $presensi->longitude_keluar }}</strong>
                                    </div>
                                </div>
                                @if($presensi->accuracy_keluar)
                                <div class="mt-2">
                                    <small class="text-muted d-block">Akurasi GPS</small>
                                    <span class="badge bg-{{ $presensi->accuracy_keluar <= 50 ? 'success' : 'warning' }}">
                                        {{ number_format($presensi->accuracy_keluar, 1) }} meter
                                    </span>
                                </div>
                                @endif
                                @if($presensi->alamat_keluar)
                                <div class="mt-2">
                                    <small class="text-muted d-block">Alamat</small>
                                    <small class="text-white">{{ $presensi->alamat_keluar }}</small>
                                </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Tidak ada data lokasi keluar</p>
                    @endif
                </div>
            </div>

            <!-- Foto Presensi -->
            <div class="row mt-4">
                @if($presensi->foto_masuk || $presensi->foto_keluar)
                <div class="col-12">
                    <h6 class="text-primary mb-3"><i class="fas fa-camera me-2"></i>Foto Presensi</h6>
                    <div class="row">
                        @if($presensi->foto_masuk)
                        <div class="col-md-6 text-center">
                            <p class="text-muted mb-2">Foto Masuk</p>
                            <img src="{{ asset('storage/' . $presensi->foto_masuk) }}" 
                                 class="img-fluid rounded border" 
                                 style="max-height: 200px;" 
                                 alt="Foto Presensi Masuk">
                            @if($presensi->confidence_score_masuk)
                            <div class="mt-1">
                                <small class="text-muted">Confidence: {{ number_format($presensi->confidence_score_masuk * 100, 1) }}%</small>
                            </div>
                            @endif
                        </div>
                        @endif
                        @if($presensi->foto_keluar)
                        <div class="col-md-6 text-center">
                            <p class="text-muted mb-2">Foto Keluar</p>
                            <img src="{{ asset('storage/' . $presensi->foto_keluar) }}" 
                                 class="img-fluid rounded border" 
                                 style="max-height: 200px;" 
                                 alt="Foto Presensi Keluar">
                            @if($presensi->confidence_score_keluar)
                            <div class="mt-1">
                                <small class="text-muted">Confidence: {{ number_format($presensi->confidence_score_keluar * 100, 1) }}%</small>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Catatan -->
            @if($presensi->catatan)
            <div class="mt-4">
                <label class="form-label text-muted">Catatan</label>
                <div class="card bg-dark border-secondary">
                    <div class="card-body">
                        <p class="text-white mb-0">{{ $presensi->catatan }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('admin.presensi.index') }}" class="btn btn-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                </a>
                <div>
                    <a href="{{ route('presensi.edit', $presensi->id_presensi) }}" class="btn btn-warning btn-modern me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <form action="{{ route('presensi.destroy', $presensi->id_presensi) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-modern" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus presensi ini?')">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection