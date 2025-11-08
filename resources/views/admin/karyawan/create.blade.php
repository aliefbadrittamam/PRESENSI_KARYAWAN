@extends('layouts.app')

@section('title', 'Tambah Karyawan')
@section('icon', 'fa-user-plus')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-user-plus me-2"></i>Tambah Karyawan Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.karyawan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-id-card me-2"></i>Data Pribadi</h6>
                            
                            <div class="mb-3">
                                <label for="nip" class="form-label text-white">NIP *</label>
                                <input type="text" class="form-control form-control-modern @error('nip') is-invalid @enderror" 
                                       id="nip" name="nip" value="{{ old('nip') }}" required>
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label text-white">Nama Lengkap *</label>
                                <input type="text" class="form-control form-control-modern @error('nama_lengkap') is-invalid @enderror" 
                                       id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jenis_kelamin" class="form-label text-white">Jenis Kelamin *</label>
                                        <select class="form-select form-control-modern @error('jenis_kelamin') is-invalid @enderror" 
                                                id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_lahir" class="form-label text-white">Tanggal Lahir *</label>
                                        <input type="date" class="form-control form-control-modern @error('tanggal_lahir') is-invalid @enderror" 
                                               id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label text-white">Email *</label>
                                <input type="email" class="form-control form-control-modern @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nomor_telepon" class="form-label text-white">Nomor Telepon *</label>
                                <input type="text" class="form-control form-control-modern @error('nomor_telepon') is-invalid @enderror" 
                                       id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}" required>
                                @error('nomor_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-briefcase me-2"></i>Data Pekerjaan</h6>

                            <div class="mb-3">
                                <label for="id_jabatan" class="form-label text-white">Jabatan *</label>
                                <select class="form-select form-control-modern @error('id_jabatan') is-invalid @enderror" 
                                        id="id_jabatan" name="id_jabatan" required>
                                    <option value="">Pilih Jabatan</option>
                                    @foreach($jabatan as $jab)
                                        <option value="{{ $jab->id_jabatan }}" {{ old('id_jabatan') == $jab->id_jabatan ? 'selected' : '' }}>
                                            {{ $jab->nama_jabatan }} ({{ $jab->jenis_jabatan }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="id_departemen" class="form-label text-white">Departemen *</label>
                                <select class="form-select form-control-modern @error('id_departemen') is-invalid @enderror" 
                                        id="id_departemen" name="id_departemen" required>
                                    <option value="">Pilih Departemen</option>
                                    @foreach($departemen as $dept)
                                        <option value="{{ $dept->id_departemen }}" {{ old('id_departemen') == $dept->id_departemen ? 'selected' : '' }}>
                                            {{ $dept->nama_departemen }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_departemen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="id_fakultas" class="form-label text-white">Fakultas *</label>
                                <select class="form-select form-control-modern @error('id_fakultas') is-invalid @enderror" 
                                        id="id_fakultas" name="id_fakultas" required>
                                    <option value="">Pilih Fakultas</option>
                                    @foreach($fakultas as $fak)
                                        <option value="{{ $fak->id_fakultas }}" {{ old('id_fakultas') == $fak->id_fakultas ? 'selected' : '' }}>
                                            {{ $fak->nama_fakultas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_fakultas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_mulai_kerja" class="form-label text-white">Tanggal Mulai Kerja *</label>
                                <input type="date" class="form-control form-control-modern @error('tanggal_mulai_kerja') is-invalid @enderror" 
                                       id="tanggal_mulai_kerja" name="tanggal_mulai_kerja" value="{{ old('tanggal_mulai_kerja') }}" required>
                                @error('tanggal_mulai_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="foto" class="form-label text-white">Foto Profil</label>
                                <input type="file" class="form-control form-control-modern @error('foto') is-invalid @enderror" 
                                       id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                                <div class="form-text text-muted">Format: JPG, PNG, JPEG (Max: 2MB)</div>
                                @error('foto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- New Photo Preview (sama seperti di halaman edit) -->
                                <div class="mt-2 text-center" id="newPhotoPreview" style="display: none;">
                                    <img id="preview" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                                    <div class="mt-1">
                                        <small class="text-muted">Pratinjau foto baru</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="status_aktif" name="status_aktif" value="1" checked>
                                <label class="form-check-label text-white" for="status_aktif">Status Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-save me-2"></i>Simpan Data
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
    // Preview new photo before upload (dipanggil oleh onchange pada input#foto)
    function previewImage(input) {
        const preview = document.getElementById('preview');
        const newPhotoPreview = document.getElementById('newPhotoPreview');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];

            // opsional: validasi ukuran file (contoh 2MB)
            const maxSizeBytes = 2 * 1024 * 1024;
            if (file.size > maxSizeBytes) {
                alert('Ukuran file terlalu besar. Maksimum 2MB.');
                input.value = ''; // reset input
                newPhotoPreview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                newPhotoPreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            newPhotoPreview.style.display = 'none';
        }
    }
</script>
@endpush
  