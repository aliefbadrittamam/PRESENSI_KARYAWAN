@extends('layouts.app')

@section('title', 'Tambah Fakultas')
@section('icon', 'fa-plus-circle')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Fakultas Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('fakultas.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode_fakultas" class="form-label text-white">Kode Fakultas *</label>
                                <input type="text" class="form-control form-control-modern @error('kode_fakultas') is-invalid @enderror" 
                                       id="kode_fakultas" name="kode_fakultas" value="{{ old('kode_fakultas') }}" 
                                       placeholder="Contoh: FIT, FEB, FIK" required>
                                <div class="form-text text-muted">Kode unik untuk fakultas (max: 10 karakter)</div>
                                @error('kode_fakultas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nama_fakultas" class="form-label text-white">Nama Fakultas *</label>
                                <input type="text" class="form-control form-control-modern @error('nama_fakultas') is-invalid @enderror" 
                                       id="nama_fakultas" name="nama_fakultas" value="{{ old('nama_fakultas') }}" 
                                       placeholder="Contoh: Fakultas Ilmu Komputer" required>
                                @error('nama_fakultas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dekan" class="form-label text-white">Dekan</label>
                                <input type="text" class="form-control form-control-modern @error('dekan') is-invalid @enderror" 
                                       id="dekan" name="dekan" value="{{ old('dekan') }}" 
                                       placeholder="Nama Dekan saat ini">
                                @error('dekan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Status</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="status_aktif" name="status_aktif" value="1" checked>
                                    <label class="form-check-label text-white" for="status_aktif">Fakultas Aktif</label>
                                </div>
                                <div class="form-text text-muted">Non-aktifkan jika fakultas sudah tidak beroperasi</div>
                            </div>

                            <!-- Statistics Preview -->
                            <div class="card-modern p-3 mt-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-chart-bar me-2"></i>Informasi</h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="text-primary">
                                            <i class="fas fa-building fa-2x mb-2"></i>
                                            <p class="mb-0">Departemen</p>
                                            <h5 class="mb-0">0</h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-success">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <p class="mb-0">Karyawan</p>
                                            <h5 class="mb-0">0</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('fakultas.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-save me-2"></i>Simpan Fakultas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-format kode fakultas to uppercase
    document.getElementById('kode_fakultas').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Auto-capitalize each word in nama fakultas
    document.getElementById('nama_fakultas').addEventListener('input', function() {
        this.value = this.value.replace(/\w\S*/g, function(txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    });
</script>
@endpush