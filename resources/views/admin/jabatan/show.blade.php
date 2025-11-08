@extends('layouts.app')

@section('title', 'Detail Jabatan')
@section('icon', 'fa-eye')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-briefcase me-2"></i>Detail Jabatan
                </h5>
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label text-muted">ID Jabatan</label>
                    <p class="fs-6 fw-bold text-white">{{ $jabatan->id_jabatan }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Nama Jabatan</label>
                    <p class="fs-6 fw-bold text-white">{{ $jabatan->nama_jabatan }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Jenis Jabatan</label>
                    <p>
                        @if ($jabatan->jenis_jabatan == 'Struktural')
                            <span class="badge bg-primary">Struktural</span>
                        @else
                            <span class="badge bg-success">Fungsional</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Keterangan</label>
                    <p class="text-white">{{ $jabatan->keterangan ?? '-' }}</p>
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <a href="{{ route('admin.jabatan.index') }}" class="btn btn-secondary btn-modern">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <a href="{{ route('admin.jabatan.edit', $jabatan->id_jabatan) }}" class="btn btn-warning btn-modern">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
