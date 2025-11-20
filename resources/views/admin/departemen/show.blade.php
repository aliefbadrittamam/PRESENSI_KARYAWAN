@extends('layouts.app')

@section('title', 'Detail Departemen')
@section('icon', 'fa-building')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Departemen Info Card -->
        <div class="card-modern text-center p-4">
            <div class="rounded-circle bg-info d-flex align-items-center justify-content-center mx-auto mb-3" 
                 style="width: 100px; height: 100px;">
                <i class="fas fa-building fa-3x text-white"></i>
            </div>
            <h4 class="text-white">{{ $departemen->nama_departemen }}</h4>
            <p class="text-muted">{{ $departemen->kode_departemen }}</p>
            <span class="badge bg-{{ $departemen->status_aktif ? 'success' : 'danger' }} badge-modern">
                {{ $departemen->status_aktif ? 'Aktif' : 'Non-Aktif' }}
            </span>
        </div>
        
        <!-- Quick Stats -->
        <div class="card-modern mt-4 p-4">
            <h6 class="text-primary mb-3"><i class="fas fa-chart-pie me-2"></i>Statistik</h6>
            <div class="text-white-50">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-users me-2"></i>Total Karyawan</span>
                    <span class="badge bg-primary">{{ $departemen->karyawan->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-user-check me-2"></i>Karyawan Aktif</span>
                    <span class="badge bg-success">{{ $departemen->karyawan->where('status_aktif', true)->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-calendar me-2"></i>Dibuat Pada</span>
                    <small>{{ $departemen->created_at->format('d/m/Y') }}</small>
                </div>
            </div>
        </div>

        <!-- Fakultas Info -->
        <div class="card-modern mt-4 p-4">
            <h6 class="text-primary mb-3"><i class="fas fa-university me-2"></i>Fakultas</h6>
            <div class="text-center">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-2" 
                     style="width: 60px; height: 60px;">
                    <i class="fas fa-university fa-2x text-white"></i>
                </div>
                <h6 class="text-white mb-1">{{ $departemen->fakultas->nama_fakultas }}</h6>
                <small class="text-muted">{{ $departemen->fakultas->kode_fakultas }}</small>
                <div class="mt-2">
                    <span class="badge bg-{{ $departemen->fakultas->status_aktif ? 'success' : 'danger' }}">
                        {{ $departemen->fakultas->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Main Details -->
        <div class="card-modern p-4">
            <h5 class="text-white mb-4"><i class="fas fa-info-circle me-2"></i>Informasi Departemen</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label text-muted">Kode Departemen</label>
                        <p class="text-white">
                            <span class="badge bg-primary badge-modern">{{ $departemen->kode_departemen }}</span>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Status</label>
                        <p class="text-white">
                            @if($departemen->status_aktif)
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
                        <label class="form-label text-muted">Fakultas</label>
                        <p class="text-white">
                            <i class="fas fa-university me-2 text-primary"></i>
                            {{ $departemen->fakultas->nama_fakultas }}
                            <span class="badge bg-dark ms-1">{{ $departemen->fakultas->kode_fakultas }}</span>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Terakhir Diupdate</label>
                        <p class="text-white">
                            <i class="fas fa-clock me-2 text-muted"></i>
                            {{ $departemen->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            @if($departemen->deskripsi)
            <div class="mt-4">
                <label class="form-label text-muted">Deskripsi</label>
                <div class="card bg-dark border-secondary">
                    <div class="card-body">
                        <p class="text-white mb-0">{{ $departemen->deskripsi }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Karyawan List -->
            <div class="mt-4">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-users me-2"></i>Karyawan di Departemen Ini
                    <span class="badge bg-primary ms-2">{{ $departemen->karyawan->count() }}</span>
                </h6>
                
                @if($departemen->karyawan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-dark table-sm">
                            <thead>
                                <tr>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departemen->karyawan as $karyawan)
                                <tr>
                                    <td>{{ $karyawan->nip }}</td>
                                    <td>
                                        <a href="{{ route('admin.karyawan.show', $karyawan->id_karyawan) }}" class="text-white text-decoration-none">
                                            {{ $karyawan->nama_lengkap }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $karyawan->jabatan->nama_jabatan }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $karyawan->status_aktif ? 'success' : 'danger' }}">
                                            {{ $karyawan->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada karyawan di departemen ini.</p>
                    </div>
                @endif
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('admin.departemen.index') }}" class="btn btn-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                </a>
                <div>
                    <a href="{{ route('admin.departemen.edit', $departemen->id_departemen) }}" class="btn btn-warning btn-modern me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    @if($departemen->karyawan->count() == 0)
                    <form action="{{ route('admin.departemen.destroy', $departemen->id_departemen) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-modern" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus departemen ini?')">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </button>
                    </form>
                    @else
                    <button class="btn btn-danger btn-modern" disabled data-bs-toggle="tooltip" 
                            title="Tidak dapat menghapus departemen yang masih memiliki karyawan">
                        <i class="fas fa-trash me-2"></i>Hapus
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection