@extends('layouts.app')

@section('title', 'Edit Karyawan')
@section('icon', 'fa-user-edit')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-user-edit me-2"></i>Edit Data Karyawan
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.karyawan.update', $karyawan->id_karyawan) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Current Photo Preview -->
                        <div class="col-12 mb-4">
                            <div class="text-center">
                                @if($karyawan->foto)
                                    <img src="{{ asset('public/' . $karyawan->foto) }}" 
                                         class="rounded-circle mb-3" width="120" height="120" 
                                         alt="{{ $karyawan->nama_lengkap }}" id="currentPhoto">
                                    <div class="mt-2">
                                        <a href="#" class="btn btn-sm btn-outline-danger" onclick="removePhoto()">
                                            <i class="fas fa-trash me-1"></i>Hapus Foto
                                        </a>
                                    </div>
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" 
                                         style="width: 120px; height: 120px;">
                                        <i class="fas fa-user fa-3x text-white"></i>
                                    </div>
                                @endif
                                <h5 class="text-white">{{ $karyawan->nama_lengkap }}</h5>
                                <p class="text-muted">{{ $karyawan->nip }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-id-card me-2"></i>Data Pribadi</h6>
                            
                            <div class="mb-3">
                                <label for="nip" class="form-label text-white">NIP *</label>
                                <input type="text" class="form-control form-control-modern @error('nip') is-invalid @enderror" 
                                       id="nip" name="nip" value="{{ old('nip', $karyawan->nip) }}" required>
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label text-white">Nama Lengkap *</label>
                                <input type="text" class="form-control form-control-modern @error('nama_lengkap') is-invalid @enderror" 
                                       id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" required>
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
                                            <option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
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
                                               id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir->format('Y-m-d')) }}" required>
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label text-white">Email *</label>
                                <input type="email" class="form-control form-control-modern @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $karyawan->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nomor_telepon" class="form-label text-white">Nomor Telepon *</label>
                                <input type="text" class="form-control form-control-modern @error('nomor_telepon') is-invalid @enderror" 
                                       id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $karyawan->nomor_telepon) }}" required>
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
                                        <option value="{{ $jab->id_jabatan }}" {{ old('id_jabatan', $karyawan->id_jabatan) == $jab->id_jabatan ? 'selected' : '' }}>
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
                                        <option value="{{ $dept->id_departemen }}" {{ old('id_departemen', $karyawan->id_departemen) == $dept->id_departemen ? 'selected' : '' }}>
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
                                        <option value="{{ $fak->id_fakultas }}" {{ old('id_fakultas', $karyawan->id_fakultas) == $fak->id_fakultas ? 'selected' : '' }}>
                                            {{ $fak->nama_fakultas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_fakultas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_mulai_kerja" class="form-label text-white">Tanggal Mulai Kerja *</label>
                                        <input type="date" class="form-control form-control-modern @error('tanggal_mulai_kerja') is-invalid @enderror" 
                                               id="tanggal_mulai_kerja" name="tanggal_mulai_kerja" value="{{ old('tanggal_mulai_kerja', $karyawan->tanggal_mulai_kerja->format('Y-m-d')) }}" required>
                                        @error('tanggal_mulai_kerja')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_berhenti_kerja" class="form-label text-white">Tanggal Berhenti Kerja</label>
                                        <input type="date" class="form-control form-control-modern @error('tanggal_berhenti_kerja') is-invalid @enderror" 
                                               id="tanggal_berhenti_kerja" name="tanggal_berhenti_kerja" value="{{ old('tanggal_berhenti_kerja', $karyawan->tanggal_berhenti_kerja ? $karyawan->tanggal_berhenti_kerja->format('Y-m-d') : '') }}">
                                        @error('tanggal_berhenti_kerja')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="foto" class="form-label text-white">Ganti Foto Profil</label>
                                <input type="file" class="form-control form-control-modern @error('foto') is-invalid @enderror" 
                                       id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                                <div class="form-text text-muted">Kosongkan jika tidak ingin mengganti foto</div>
                                @error('foto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <!-- New Photo Preview -->
                                <div class="mt-2 text-center" id="newPhotoPreview" style="display: none;">
                                    <img id="preview" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                                    <div class="mt-1">
                                        <small class="text-muted">Pratinjau foto baru</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="status_aktif" name="status_aktif" value="1" 
                                    {{ old('status_aktif', $karyawan->status_aktif) ? 'checked' : '' }}>
                                <label class="form-check-label text-white" for="status_aktif">Status Aktif</label>
                            </div>

                            <!-- Face ID Status -->
                            <div class="mb-3">
                                <label class="form-label text-white">Status Face ID</label>
                                <div>
                                    @switch($karyawan->status_verifikasi_face_id)
                                        @case('verified')
                                            <span class="badge bg-success badge-modern">
                                                <i class="fas fa-check-circle me-1"></i>Terverifikasi
                                            </span>
                                            @if($karyawan->tanggal_verifikasi_face_id)
                                                <small class="text-muted d-block mt-1">
                                                    Terverifikasi pada: {{ $karyawan->tanggal_verifikasi_face_id->format('d/m/Y H:i') }}
                                                </small>
                                            @endif
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning badge-modern">
                                                <i class="fas fa-clock me-1"></i>Menunggu Verifikasi
                                            </span>
                                            @break
                                        @case('failed')
                                            <span class="badge bg-danger badge-modern">
                                                <i class="fas fa-times-circle me-1"></i>Gagal Verifikasi
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary badge-modern">
                                                <i class="fas fa-question-circle me-1"></i>Belum Daftar
                                            </span>
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <div>
                            <a href="{{ route('admin.karyawan.show', $karyawan->id_karyawan) }}" class="btn btn-info btn-modern me-2">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </a>
                            <button type="submit" class="btn btn-primary-modern btn-modern">
                                <i class="fas fa-save me-2"></i>Update Data
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
    // Preview new photo before upload
    function previewImage(input) {
        const preview = document.getElementById('preview');
        const newPhotoPreview = document.getElementById('newPhotoPreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                newPhotoPreview.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            newPhotoPreview.style.display = 'none';
        }
    }

    // Remove current photo
    function removePhoto() {
        if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
            // Create hidden input to indicate photo removal
            const form = document.querySelector('form');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'remove_photo';
            hiddenInput.value = '1';
            form.appendChild(hiddenInput);
            
            // Hide current photo and show placeholder
            const currentPhoto = document.getElementById('currentPhoto');
            if (currentPhoto) {
                currentPhoto.style.display = 'none';
            }
            
            // Show message
            alert('Foto akan dihapus setelah Anda mengupdate data.');
        }
    }

    // Auto-disable status if resignation date is filled
    document.getElementById('tanggal_berhenti_kerja').addEventListener('change', function() {
        const statusCheckbox = document.getElementById('status_aktif');
        if (this.value) {
            statusCheckbox.checked = false;
        }
    });

    // Auto-fill resignation date if status is unchecked
    document.getElementById('status_aktif').addEventListener('change', function() {
        const resignationDate = document.getElementById('tanggal_berhenti_kerja');
        if (!this.checked && !resignationDate.value) {
            resignationDate.value = new Date().toISOString().split('T')[0];
        } else if (this.checked) {
            resignationDate.value = '';
        }
    });
</script>
@endpush