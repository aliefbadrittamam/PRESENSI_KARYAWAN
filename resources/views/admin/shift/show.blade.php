@extends('layouts.app')

@section('title', 'Detail Shift Kerja')
@section('icon', 'fa-clock')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Shift Info Card -->
        <div class="card-modern text-center p-4">
            <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center mx-auto mb-3" 
                 style="width: 100px; height: 100px;">
                <i class="fas fa-clock fa-3x text-white"></i>
            </div>
            <h4 class="text-white">{{ $shift->nama_shift }}</h4>
            <p class="text-muted">{{ $shift->kode_shift }}</p>
            <span class="badge bg-{{ $shift->status_aktif ? 'success' : 'danger' }} badge-modern">
                {{ $shift->status_aktif ? 'Aktif' : 'Non-Aktif' }}
            </span>
        </div>
        
        <!-- Quick Stats -->
        <div class="card-modern mt-4 p-4">
            <h6 class="text-primary mb-3"><i class="fas fa-chart-pie me-2"></i>Statistik</h6>
            <div class="text-white-50">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-calendar-check me-2"></i>Total Presensi</span>
                    <span class="badge bg-primary">{{ $shift->presensi->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-users me-2"></i>Karyawan Aktif</span>
                    <span class="badge bg-success">{{ $shift->presensi->unique('id_karyawan')->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-calendar me-2"></i>Dibuat Pada</span>
                    <small>{{ $shift->created_at->format('d/m/Y') }}</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Main Details -->
        <div class="card-modern p-4">
            <h5 class="text-white mb-4"><i class="fas fa-info-circle me-2"></i>Informasi Shift</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label text-muted">Kode Shift</label>
                        <p class="text-white">
                            <span class="badge bg-primary badge-modern">{{ $shift->kode_shift }}</span>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Jam Mulai</label>
                        <p class="text-white">
                            <i class="fas fa-play-circle me-2 text-success"></i>
                            <strong>{{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }}</strong>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Status</label>
                        <p class="text-white">
                            @if($shift->status_aktif)
                                <span class="badge bg-success badge-modern">
                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge bg-danger badge-modern">
                                    <i class="fas fa-times-circle me-1"></i>Non-Aktif
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label text-muted">Durasi Shift</label>
                        <p class="text-white">
                            <span class="badge bg-info badge-modern">{{ $shift->durasi_shift }} jam</span>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Jam Selesai</label>
                        <p class="text-white">
                            <i class="fas fa-stop-circle me-2 text-danger"></i>
                            <strong>{{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }}</strong>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Toleransi Keterlambatan</label>
                        <p class="text-white">
                            <span class="badge bg-secondary badge-modern">{{ $shift->toleransi_keterlambatan }} menit</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            @if($shift->keterangan)
            <div class="mt-4">
                <label class="form-label text-muted">Keterangan</label>
                <div class="card bg-dark border-secondary">
                    <div class="card-body">
                        <p class="text-white mb-0">{{ $shift->keterangan }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Presensi -->
            <div class="mt-4">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-history me-2"></i>Presensi Terbaru
                    <span class="badge bg-primary ms-2">{{ $shift->presensi->count() }}</span>
                </h6>
                
                @if($shift->presensi->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-dark table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Karyawan</th>
                                    <th>Jam Masuk</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shift->presensi->take(5) as $presensi)
                                <tr>
                                    <td>{{ $presensi->tanggal_presensi->format('d/m') }}</td>
                                    <td>
                                        <a href="{{ route('admin.karyawan.show', $presensi->id_karyawan) }}" class="text-white text-decoration-none">
                                            {{ $presensi->karyawan->nama_lengkap }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($presensi->jam_masuk)
                                            {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}
                                            @if($presensi->keterlambatan_menit > 0)
                                            <small class="text-warning d-block">+{{ $presensi->keterlambatan_menit }}m</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $presensi->status_kehadiran_color }}">
                                            {{ strtoupper($presensi->status_kehadiran) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($shift->presensi->count() > 5)
                    <div class="text-center mt-2">
                        <a href="{{ route('admin.presensi.index') }}?shift={{ $shift->id_shift }}" class="btn btn-sm btn-primary-modern">
                            Lihat Semua Presensi
                        </a>
                    </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada presensi untuk shift ini.</p>
                    </div>
                @endif
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('admin.shift.index') }}" class="btn btn-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                </a>
                <div>
                    <a href="{{ route('admin.shift.edit', $shift->id_shift) }}" class="btn btn-warning btn-modern me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    @if($shift->presensi->count() == 0)
                    <form action="{{ route('admin.shift.destroy', $shift->id_shift) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-modern" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus shift ini?')">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </button>
                    </form>
                    @else
                    <button class="btn btn-danger btn-modern" disabled data-bs-toggle="tooltip" 
                            title="Tidak dapat menghapus shift yang masih memiliki presensi">
                        <i class="fas fa-trash me-2"></i>Hapus
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection