@extends('layouts.app')

@section('title', 'Detail Karyawan')
@section('icon', 'fa-user')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card-modern text-center p-4">
            @if($karyawan->foto)
                <img src="{{ asset('public/' . $karyawan->foto) }}" 
                     class="rounded-circle mb-3" width="150" height="150" 
                     alt="{{ $karyawan->nama_lengkap }}">
            @else
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" 
                     style="width: 150px; height: 150px;">
                    <i class="fas fa-user fa-4x text-white"></i>
                </div>
            @endif
            <h4 class="text-white">{{ $karyawan->nama_lengkap }}</h4>
            <p class="text-muted">{{ $karyawan->nip }}</p>
            <span class="badge bg-{{ $karyawan->status_aktif ? 'success' : 'danger' }} badge-modern">
                {{ $karyawan->status_aktif ? 'Aktif' : 'Non-Aktif' }}
            </span>
        </div>
        
        <div class="card-modern mt-4 p-4">
            <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Kontak</h6>
            <div class="text-white-50">
                <p><i class="fas fa-envelope me-2"></i> {{ $karyawan->email }}</p>
                <p><i class="fas fa-phone me-2"></i> {{ $karyawan->nomor_telepon }}</p>
                <p><i class="fas fa-venus-mars me-2"></i> {{ $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                <p><i class="fas fa-birthday-cake me-2"></i> {{ $karyawan->tanggal_lahir->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card-modern p-4">
            <h5 class="text-white mb-4">Detail Karyawan</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label text-muted">Jabatan</label>
                        <p class="text-white">{{ $karyawan->jabatan->nama_jabatan }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Departemen</label>
                        <p class="text-white">{{ $karyawan->departemen->nama_departemen }}</p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label text-muted">Fakultas</label>
                        <p class="text-white">{{ $karyawan->fakultas->nama_fakultas }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Tanggal Mulai Kerja</label>
                        <p class="text-white">{{ $karyawan->tanggal_mulai_kerja->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            
            @if($karyawan->tanggal_berhenti_kerja)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Karyawan berhenti bekerja pada: {{ $karyawan->tanggal_berhenti_kerja->format('d/m/Y') }}
            </div>
            @endif
            
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
                <div>
                    <a href="{{ route('admin.karyawan.edit', $karyawan->id_karyawan) }}" class="btn btn-warning btn-modern me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <form action="{{ route('admin.karyawan.destroy', $karyawan->id_karyawan) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-modern" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection