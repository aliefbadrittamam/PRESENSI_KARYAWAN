@extends('layouts.app')

@section('title', 'Edit Departemen')
@section('icon', 'fa-edit')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Data Departemen
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.departemen.update', $departemen->id_departemen) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode_departemen" class="form-label text-white">Kode Departemen *</label>
                                <input type="text" class="form-control form-control-modern @error('kode_departemen') is-invalid @enderror" 
                                       id="kode_departemen" name="kode_departemen" 
                                       value="{{ old('kode_departemen', $departemen->kode_departemen) }}" required>
                                <div class="form-text text-muted">Kode unik untuk departemen</div>
                                @error('kode_departemen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nama_departemen" class="form-label text-white">Nama Departemen *</label>
                                <input type="text" class="form-control form-control-modern @error('nama_departemen') is-invalid @enderror" 
                                       id="nama_departemen" name="nama_departemen" 
                                       value="{{ old('nama_departemen', $departemen->nama_departemen) }}" required>
                                @error('nama_departemen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="id_fakultas" class="form-label text-white">Fakultas *</label>
                                <select class="form-select form-control-modern @error('id_fakultas') is-invalid @enderror" 
                                        id="id_fakultas" name="id_fakultas" required>
                                    <option value="">Pilih Fakultas</option>
                                    @foreach($fakultas as $fak)
                                        <option value="{{ $fak->id_fakultas }}" {{ old('id_fakultas', $departemen->id_fakultas) == $fak->id_fakultas ? 'selected' : '' }}>
                                            {{ $fak->nama_fakultas }} ({{ $fak->kode_fakultas }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_fakultas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label text-white">Deskripsi</label>
                                <textarea class="form-control form-control-modern @error('deskripsi') is-invalid @enderror" 
                                          id="deskripsi" name="deskripsi" rows="5">{{ old('deskripsi', $departemen->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Status</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="status_aktif" name="status_aktif" value="1" 
                                        {{ old('status_aktif', $departemen->status_aktif) ? 'checked' : '' }}>
                                    <label class="form-check-label text-white" for="status_aktif">Departemen Aktif</label>
                                </div>
                                <div class="form-text text-muted">
                                    @if(!$departemen->status_aktif)
                                        <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                        Departemen saat ini non-aktif
                                    @endif
                                </div>
                            </div>

                            <!-- Statistics -->
                            <div class="card-modern p-3 mt-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-chart-bar me-2"></i>Statistik Saat Ini</h6>
                                <div class="row text-center">
                                    <div class="col-12">
                                        <div class="text-primary">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <p class="mb-0 small">Total Karyawan</p>
                                            <h5 class="mb-0">{{ $departemen->karyawan->count() }}</h5>
                                        </div>
                                    </div>
                                </div>
                                @if($departemen->karyawan->count() > 0)
                                <div class="alert alert-info mt-2 py-2">
                                    <small>
                                        <i class="fas fa-info-circle me-1"></i>
                                        Departemen ini memiliki {{ $departemen->karyawan->count() }} karyawan
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Warning if has relations and deactivating -->
                    @if($departemen->karyawan->count() > 0 && !$departemen->status_aktif)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Departemen ini memiliki {{ $departemen->karyawan->count() }} karyawan. 
                        Menonaktifkan departemen akan mempengaruhi data karyawan terkait.
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.departemen.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <div>
                            <a href="{{ route('admin.departemen.show', $departemen->id_departemen) }}" class="btn btn-info btn-modern me-2">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </a>
                            <button type="submit" class="btn btn-primary-modern btn-modern">
                                <i class="fas fa-save me-2"></i>Update Departemen
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-format kode departemen to uppercase
    document.getElementById('kode_departemen').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Show warning when deactivating department with employees
    document.getElementById('status_aktif').addEventListener('change', function() {
        if (!this.checked) {
            const karyawanCount = {{ $departemen->karyawan->count() }};
            
            if (karyawanCount > 0) {
                if (!confirm('Departemen ini memiliki ' + karyawanCount + ' karyawan. Yakin ingin menonaktifkan?')) {
                    this.checked = true;
                }
            }
        }
    });
</script>
@endpush