@extends('layouts.app')

@section('title', 'Tambah Karyawan')
@section('icon', 'fa-user-plus')

@section('content')
    <div class="row g-2">
        <div class="col-12">
            <div class="card border-0">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Tambah Karyawan Baru
                    </h6>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('admin.karyawan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <!-- Left Column - Data Pribadi -->
                            <div class="col-lg-6">
                                <div class="card bg-gradient-info text-white border-0 mb-3">
                                    <div class="card-body p-2">
                                        <h6 class="mb-0"><i class="fas fa-id-card me-2"></i>Data Pribadi</h6>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label for="nip" class="form-label text-white small mb-1">NIP <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control form-control-sm bg-dark text-white border-secondary @error('nip') is-invalid @enderror"
                                        id="nip" name="nip" value="{{ old('nip') }}" placeholder="Masukkan NIP"
                                        required>
                                    @error('nip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-2">
                                    <label for="nama_lengkap" class="form-label text-white small mb-1">Nama Lengkap <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control form-control-sm bg-dark text-white border-secondary @error('nama_lengkap') is-invalid @enderror"
                                        id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                        placeholder="Masukkan nama lengkap" required>
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label for="jenis_kelamin" class="form-label text-white small mb-1">Jenis
                                                Kelamin <span class="text-danger">*</span></label>
                                            <select
                                                class="form-select form-select-sm bg-dark text-white border-secondary @error('jenis_kelamin') is-invalid @enderror"
                                                id="jenis_kelamin" name="jenis_kelamin" required>
                                                <option value="">Pilih</option>
                                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>
                                                    Laki-laki</option>
                                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                            @error('jenis_kelamin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label for="tanggal_lahir" class="form-label text-white small mb-1">Tanggal
                                                Lahir <span class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control form-control-sm bg-dark text-white border-secondary @error('tanggal_lahir') is-invalid @enderror"
                                                id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                                required>
                                            @error('tanggal_lahir')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label for="email" class="form-label text-white small mb-1">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email"
                                        class="form-control form-control-sm bg-dark text-white border-secondary @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}"
                                        placeholder="contoh@email.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Email akan digunakan untuk login</small>
                                </div>

                                <div class="mb-2">
                                    <label for="nomor_telepon" class="form-label text-white small mb-1">
                                        Nomor Telepon <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control form-control-sm bg-dark text-white border-secondary @error('nomor_telepon') is-invalid @enderror"
                                        id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}"
                                        placeholder="08xxxxxxxxxx" required inputmode="numeric" pattern="[0-9]*"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    @error('nomor_telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <!-- Password Info -->
                                <div class="alert alert-info py-2 px-3 mb-0" role="alert">
                                    <small>
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Password default:</strong> Sistem akan membuat password random 6 digit angka
                                        secara otomatis
                                    </small>
                                </div>
                            </div>

                            <!-- Right Column - Data Pekerjaan & Foto -->
                            <div class="col-lg-6">
                                <div class="card bg-gradient-success text-white border-0 mb-3">
                                    <div class="card-body p-2">
                                        <h6 class="mb-0"><i class="fas fa-briefcase me-2"></i>Data Pekerjaan</h6>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label for="id_jabatan" class="form-label text-white small mb-1">Jabatan <span
                                            class="text-danger">*</span></label>
                                    <select
                                        class="form-select form-select-sm bg-dark text-white border-secondary @error('id_jabatan') is-invalid @enderror"
                                        id="id_jabatan" name="id_jabatan" required>
                                        <option value="">Pilih Jabatan</option>
                                        @foreach ($jabatan as $jab)
                                            <option value="{{ $jab->id_jabatan }}"
                                                {{ old('id_jabatan') == $jab->id_jabatan ? 'selected' : '' }}>
                                                {{ $jab->nama_jabatan }} ({{ ucfirst($jab->jenis_jabatan) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-2">
                                    <label for="id_departemen" class="form-label text-white small mb-1">Departemen <span
                                            class="text-danger">*</span></label>
                                    <select
                                        class="form-select form-select-sm bg-dark text-white border-secondary @error('id_departemen') is-invalid @enderror"
                                        id="id_departemen" name="id_departemen" required>
                                        <option value="">Pilih Departemen</option>
                                        @foreach ($departemen as $dept)
                                            <option value="{{ $dept->id_departemen }}"
                                                {{ old('id_departemen') == $dept->id_departemen ? 'selected' : '' }}>
                                                {{ $dept->nama_departemen }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_departemen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-2">
                                    <label for="id_fakultas" class="form-label text-white small mb-1">Fakultas <span
                                            class="text-danger">*</span></label>
                                    <select
                                        class="form-select form-select-sm bg-dark text-white border-secondary @error('id_fakultas') is-invalid @enderror"
                                        id="id_fakultas" name="id_fakultas" required>
                                        <option value="">Pilih Fakultas</option>
                                        @foreach ($fakultas as $fak)
                                            <option value="{{ $fak->id_fakultas }}"
                                                {{ old('id_fakultas') == $fak->id_fakultas ? 'selected' : '' }}>
                                                {{ $fak->nama_fakultas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_fakultas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-2">
                                    <label for="tanggal_mulai_kerja" class="form-label text-white small mb-1">Tanggal
                                        Mulai Kerja <span class="text-danger">*</span></label>
                                    <input type="date"
                                        class="form-control form-control-sm bg-dark text-white border-secondary @error('tanggal_mulai_kerja') is-invalid @enderror"
                                        id="tanggal_mulai_kerja" name="tanggal_mulai_kerja"
                                        value="{{ old('tanggal_mulai_kerja') }}" required>
                                    @error('tanggal_mulai_kerja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-2">
                                    <label for="foto" class="form-label text-white small mb-1">Foto Profil</label>
                                    <input type="file"
                                        class="form-control form-control-sm bg-dark text-white border-secondary @error('foto') is-invalid @enderror"
                                        id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted d-block mt-1">Format: JPG, PNG, JPEG (Max: 2MB)</small>
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <!-- Photo Preview -->
                                    <div class="mt-2 text-center" id="newPhotoPreview" style="display: none;">
                                        <div class="card bg-dark border-secondary p-2 d-inline-block">
                                            <img id="preview" class="rounded"
                                                style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                            <small class="text-muted d-block mt-1">Pratinjau Foto</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-0">
                                    <input type="checkbox" class="form-check-input" id="status_aktif"
                                        name="status_aktif" value="1" checked>
                                    <label class="form-check-label text-white small" for="status_aktif">
                                        <i class="fas fa-check-circle text-success me-1"></i>Status Aktif
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary">
                            <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-1"></i>Simpan Data Karyawan
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
        $(document).ready(function() {
            // Fade in animation
            $('.card').hide().fadeIn(400);
        });

        // Preview new photo before upload
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const newPhotoPreview = document.getElementById('newPhotoPreview');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validasi ukuran file (2MB)
                const maxSizeBytes = 2 * 1024 * 1024;
                if (file.size > maxSizeBytes) {
                    alert('Ukuran file terlalu besar. Maksimum 2MB.');
                    input.value = '';
                    newPhotoPreview.style.display = 'none';
                    return;
                }

                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, atau JPEG.');
                    input.value = '';
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

@push('css')
    <style>
        /* Dark Theme Consistent with Home */
        .form-control,
        .form-select {
            background-color: #2d3236 !important;
            border-color: #4a5056 !important;
            color: #fff !important;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #343a40 !important;
            border-color: #007bff !important;
            color: #fff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }

        .form-control::placeholder {
            color: #6c757d !important;
        }

        .form-select option {
            background-color: #2d3236 !important;
            color: #fff !important;
        }

        .form-check-input {
            background-color: #2d3236 !important;
            border-color: #4a5056 !important;
        }

        .form-check-input:checked {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .alert-info {
            background-color: rgba(23, 162, 184, 0.2) !important;
            border-color: #17a2b8 !important;
            color: #fff !important;
        }

        .border-secondary {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
            border: none !important;
            color: white !important;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4) !important;
        }

        .btn-secondary {
            background-color: #6c757d !important;
            border: none !important;
        }

        .btn-secondary:hover {
            background-color: #5a6268 !important;
        }

        /* Compact spacing */
        .form-label.small {
            font-size: 0.875rem !important;
            font-weight: 500 !important;
        }

        .form-control-sm,
        .form-select-sm {
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
        }

        /* Card gradient headers */
        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #208637 100%) !important;
        }

        /* Invalid feedback */
        .invalid-feedback {
            font-size: 0.8rem !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .row.g-3 {
                --bs-gutter-x: 0.75rem;
                --bs-gutter-y: 0.75rem;
            }
        }
    </style>
@endpush
