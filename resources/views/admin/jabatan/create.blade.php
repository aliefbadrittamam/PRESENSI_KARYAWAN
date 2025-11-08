@extends('layouts.app')

@section('title', 'Tambah Jabatan')
@section('icon', 'fa-plus-circle')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Jabatan Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.jabatan.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            {{-- 🔹 Kode Jabatan --}}
                            <div class="mb-3">
                                <label for="kode_jabatan" class="form-label text-white">Kode Jabatan *</label>
                                <input type="text"
                                    class="form-control form-control-modern @error('kode_jabatan') is-invalid @enderror"
                                    id="kode_jabatan" name="kode_jabatan" value="{{ old('kode_jabatan') }}"
                                    placeholder="Contoh: JAB001" required>
                                <div class="form-text text-muted">Gunakan format unik, max 10 karakter (huruf besar & angka).</div>
                                @error('kode_jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 🔹 Nama Jabatan --}}
                            <div class="mb-3">
                                <label for="nama_jabatan" class="form-label text-white">Nama Jabatan *</label>
                                <input type="text"
                                    class="form-control form-control-modern @error('nama_jabatan') is-invalid @enderror"
                                    id="nama_jabatan" name="nama_jabatan" value="{{ old('nama_jabatan') }}"
                                    placeholder="Contoh: Kepala Program Studi" required>
                                @error('nama_jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- 🔹 Jenis Jabatan --}}
                            <div class="mb-3">
                                <label for="jenis_jabatan" class="form-label text-white">Jenis Jabatan *</label>
                                <select
                                    class="form-select form-control-modern @error('jenis_jabatan') is-invalid @enderror"
                                    id="jenis_jabatan" name="jenis_jabatan" required>
                                    <option value="">Pilih Jenis Jabatan</option>
                                    <option value="struktural" {{ old('jenis_jabatan') == 'struktural' ? 'selected' : '' }}>Struktural</option>
                                    <option value="fungsional" {{ old('jenis_jabatan') == 'fungsional' ? 'selected' : '' }}>Fungsional</option>
                                </select>
                                @error('jenis_jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 🔹 Keterangan --}}
                            <div class="mb-3">
                                <label for="keterangan" class="form-label text-white">Keterangan</label>
                                <textarea class="form-control form-control-modern @error('keterangan') is-invalid @enderror"
                                    id="keterangan" name="keterangan" rows="4"
                                    placeholder="Tambahkan keterangan singkat jika perlu...">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.jabatan.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-save me-2"></i>Simpan Jabatan
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
    // Otomatis ubah Kode Jabatan ke huruf besar
    document.getElementById('kode_jabatan').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush
