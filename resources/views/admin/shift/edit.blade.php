@extends('layouts.app')

@section('title', 'Edit Shift Kerja')
@section('icon', 'fa-edit')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Data Shift Kerja
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.shift.update', $shift->id_shift) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode_shift" class="form-label text-white">Kode Shift *</label>
                                <input type="text" class="form-control form-control-modern @error('kode_shift') is-invalid @enderror" 
                                       id="kode_shift" name="kode_shift" 
                                       value="{{ old('kode_shift', $shift->kode_shift) }}" required>
                                <div class="form-text text-muted">Kode unik untuk shift kerja</div>
                                @error('kode_shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nama_shift" class="form-label text-white">Nama Shift *</label>
                                <input type="text" class="form-control form-control-modern @error('nama_shift') is-invalid @enderror" 
                                       id="nama_shift" name="nama_shift" 
                                       value="{{ old('nama_shift', $shift->nama_shift) }}" required>
                                @error('nama_shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jam_mulai" class="form-label text-white">Jam Mulai *</label>
                                        <input type="time" class="form-control form-control-modern @error('jam_mulai') is-invalid @enderror" 
                                               id="jam_mulai" name="jam_mulai" 
                                               value="{{ old('jam_mulai', \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i')) }}" required>
                                        @error('jam_mulai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jam_selesai" class="form-label text-white">Jam Selesai *</label>
                                        <input type="time" class="form-control form-control-modern @error('jam_selesai') is-invalid @enderror" 
                                               id="jam_selesai" name="jam_selesai" 
                                               value="{{ old('jam_selesai', \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i')) }}" required>
                                        @error('jam_selesai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="toleransi_keterlambatan" class="form-label text-white">Toleransi Keterlambatan (menit)</label>
                                <input type="number" class="form-control form-control-modern @error('toleransi_keterlambatan') is-invalid @enderror" 
                                       id="toleransi_keterlambatan" name="toleransi_keterlambatan" 
                                       value="{{ old('toleransi_keterlambatan', $shift->toleransi_keterlambatan) }}" min="0" max="120">
                                @error('toleransi_keterlambatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label text-white">Keterangan</label>
                                <textarea class="form-control form-control-modern @error('keterangan') is-invalid @enderror" 
                                          id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $shift->keterangan) }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="status_aktif" name="status_aktif" value="1" 
                                    {{ old('status_aktif', $shift->status_aktif) ? 'checked' : '' }}>
                                <label class="form-check-label text-white" for="status_aktif">Shift Aktif</label>
                            </div>

                            <!-- Statistics -->
                            <div class="card-modern p-3 mt-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-chart-bar me-2"></i>Statistik Saat Ini</h6>
                                <div class="row text-center">
                                    <div class="col-12">
                                        <div class="text-primary">
                                            <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                            <p class="mb-0 small">Total Presensi</p>
                                            <h5 class="mb-0">{{ $shift->presensi->count() }}</h5>
                                        </div>
                                    </div>
                                </div>
                                @if($shift->presensi->count() > 0)
                                <div class="alert alert-info mt-2 py-2">
                                    <small>
                                        <i class="fas fa-info-circle me-1"></i>
                                        Shift ini memiliki {{ $shift->presensi->count() }} data presensi
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.shift.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <div>
                            <a href="{{ route('admin.shift.show', $shift->id_shift) }}" class="btn btn-info btn-modern me-2">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </a>
                            <button type="submit" class="btn btn-primary-modern btn-modern">
                                <i class="fas fa-save me-2"></i>Update Shift
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection