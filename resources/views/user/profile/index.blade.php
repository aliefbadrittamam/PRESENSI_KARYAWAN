{{-- File: resources/views/user/profile/index.blade.php --}}
@extends('layouts.user')

@section('title', 'Profil Saya')

@section('content')
<div class="container-desktop">
    @include('user.components.header', ['karyawan' => $karyawan])

    <div class="profile-container">
        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Profile Header Card -->
        <div class="profile-header-card mb-4">
            <div class="profile-content-wrapper">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div class="profile-photo-wrapper">
                            @if($karyawan->foto)
                                <img src="{{ asset('public/' . $karyawan->foto) }}" alt="Foto Profil" class="profile-photo">
                            @else
                                <div class="profile-photo-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <button type="button" class="btn-edit-photo" data-bs-toggle="modal" data-bs-target="#photoModal">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h3 class="mb-2 profile-name">{{ $karyawan->nama_lengkap }}</h3>
                        <p class="profile-nip mb-3">
                            <i class="fas fa-id-card me-2"></i>{{ $karyawan->nip }}
                        </p>
                        <div class="profile-badges">
                            <span class="badge badge-profile">
                                <i class="fas fa-briefcase me-1"></i>
                                {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
                            </span>
                            <span class="badge badge-profile">
                                <i class="fas fa-building me-1"></i>
                                {{ $karyawan->departemen->nama_departemen ?? '-' }}
                            </span>
                            <span class="badge badge-profile">
                                <i class="fas fa-university me-1"></i>
                                {{ $karyawan->fakultas->nama_fakultas ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Informasi Pribadi -->
            <div class="col-lg-6 mb-4">
                <div class="info-card">
                    <div class="card-header-custom bg-gradient-sage">
                        <h5 class="mb-0">
                            <i class="fas fa-user-circle me-2"></i>
                            Informasi Pribadi
                        </h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="info-item">
                            <label><i class="fas fa-id-badge text-primary-sage me-2"></i>NIP</label>
                            <div class="info-value">{{ $karyawan->nip }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-user text-secondary-sage me-2"></i>Nama Lengkap</label>
                            <div class="info-value">{{ $karyawan->nama_lengkap }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-venus-mars text-soft-blue me-2"></i>Jenis Kelamin</label>
                            <div class="info-value">{{ $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-birthday-cake text-accent-gold me-2"></i>Tanggal Lahir</label>
                            <div class="info-value">{{ \Carbon\Carbon::parse($karyawan->tanggal_lahir)->isoFormat('DD MMMM YYYY') }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-envelope text-soft-pink me-2"></i>Email</label>
                            <div class="info-value">{{ $karyawan->email }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-phone text-medium-gray me-2"></i>No. Telepon</label>
                            <div class="info-value">{{ $karyawan->nomor_telepon }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Kepegawaian -->
            <div class="col-lg-6 mb-4">
                <div class="info-card">
                    <div class="card-header-custom bg-gradient-secondary-sage">
                        <h5 class="mb-0">
                            <i class="fas fa-briefcase me-2"></i>
                            Informasi Kepegawaian
                        </h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="info-item">
                            <label><i class="fas fa-user-tie text-primary-sage me-2"></i>Jabatan</label>
                            <div class="info-value">{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-sitemap text-secondary-sage me-2"></i>Departemen</label>
                            <div class="info-value">{{ $karyawan->departemen->nama_departemen ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-university text-soft-blue me-2"></i>Fakultas</label>
                            <div class="info-value">{{ $karyawan->fakultas->nama_fakultas ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-calendar-check text-accent-gold me-2"></i>Tanggal Mulai Kerja</label>
                            <div class="info-value">{{ \Carbon\Carbon::parse($karyawan->tanggal_mulai_kerja)->isoFormat('DD MMMM YYYY') }}</div>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-circle text-primary-sage me-2"></i>Status</label>
                            <div class="info-value">
                                @if($karyawan->status_aktif)
                                    <span class="badge bg-primary-sage">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="col-lg-6 mb-4">
                <div class="info-card">
                    <div class="card-header-custom bg-gradient-soft-blue">
                        <h5 class="mb-0">
                            <i class="fas fa-qrcode me-2"></i>
                            QR Code Login
                        </h5>
                    </div>
                    <div class="card-body-custom text-center">
                        <p class="text-muted mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            QR Code untuk login cepat tanpa password
                        </p>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-sage btn-lg" onclick="showQRCodeModal('{{ $karyawan->nama_lengkap }}', '{{ $karyawan->nip }}', '{{ $user->barcode_token }}')">
                                <i class="fas fa-qrcode me-2"></i>
                                Tampilkan QR Code
                            </button>
                            <form action="{{ route('karyawan.regenerate-qrcode') }}" method="POST" onsubmit="return confirm('Generate QR Code baru? QR Code lama tidak akan bisa digunakan.')">
                                @csrf
                                <button type="submit" class="btn btn-outline-sage btn-lg w-100">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Generate QR Baru
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Card - Dynamic -->
            <div class="col-lg-6 mb-4">
                <div class="info-card">
                    <div class="card-header-custom {{ is_null($user->password) ? 'bg-gradient-soft-pink' : 'bg-gradient-accent-gold' }}">
                        <h5 class="mb-0">
                            <i class="fas fa-{{ is_null($user->password) ? 'exclamation-triangle' : 'lock' }} me-2"></i>
                            {{ is_null($user->password) ? 'Buat Password' : 'Ubah Password' }}
                        </h5>
                    </div>
                    <div class="card-body-custom">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(is_null($user->password))
                            <!-- Alert untuk user yang belum punya password -->
                            <div class="alert alert-warning-sage mb-3">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Perhatian!</strong> Anda belum memiliki password. Silakan buat password untuk keamanan akun Anda.
                            </div>

                            <!-- Form Buat Password Baru -->
                            <form action="{{ route('karyawan.create-password') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-key text-primary-sage me-2"></i>
                                        Password Baru <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" name="new_password" id="new_password" required minlength="8" placeholder="Minimal 8 karakter">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye" id="toggle-new_password"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Minimal 8 karakter, kombinasi huruf dan angka
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-check-double text-secondary-sage me-2"></i>
                                        Konfirmasi Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-check"></i></span>
                                        <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation" required minlength="8" placeholder="Ulangi password">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                            <i class="fas fa-eye" id="toggle-new_password_confirmation"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-sage w-100 btn-lg">
                                    <i class="fas fa-key me-2"></i>
                                    Buat Password
                                </button>
                            </form>
                        @else
                            <!-- Form Ubah Password (untuk user yang sudah punya password) -->
                            <div class="alert alert-info-sage mb-3">
                                <i class="fas fa-shield-alt me-2"></i>
                                Ubah password Anda secara berkala untuk menjaga keamanan akun.
                            </div>

                            <form action="{{ route('karyawan.update-password') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-lock text-medium-gray me-2"></i>
                                        Password Lama <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" name="current_password" id="current_password" required placeholder="Masukkan password lama">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                            <i class="fas fa-eye" id="toggle-current_password"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-key text-primary-sage me-2"></i>
                                        Password Baru <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="password" class="form-control" name="new_password" id="new_password_update" required minlength="8" placeholder="Minimal 8 karakter">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_update')">
                                            <i class="fas fa-eye" id="toggle-new_password_update"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Minimal 8 karakter, kombinasi huruf dan angka
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-check-double text-secondary-sage me-2"></i>
                                        Konfirmasi Password Baru <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-check"></i></span>
                                        <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation_update" required minlength="8" placeholder="Ulangi password baru">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation_update')">
                                            <i class="fas fa-eye" id="toggle-new_password_confirmation_update"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-accent-gold w-100 btn-lg">
                                    <i class="fas fa-save me-2"></i>
                                    Ubah Password
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Photo -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>
                    Ubah Foto Profil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('karyawan.update-photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                @csrf
                <div class="modal-body">
                    @if($errors->has('photo'))
                        <div class="alert alert-danger">
                            {{ $errors->first('photo') }}
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Pilih Foto <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="photo" id="photoInput" accept="image/jpeg,image/png,image/jpg" required>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Format: JPG, PNG. Maksimal 2MB
                        </small>
                    </div>
                    <div id="preview-container" style="display: none;">
                        <label class="form-label">Preview:</label>
                        <img id="preview-image" class="img-fluid rounded" style="max-height: 300px; width: 100%; object-fit: contain;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-sage" id="submitPhotoBtn">
                        <i class="fas fa-upload me-2"></i>Upload Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrcodeModal" tabindex="-1" aria-labelledby="qrcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-sage text-white">
                <h5 class="modal-title" id="qrcodeModalLabel">
                    <i class="fas fa-qrcode me-2"></i>QR Code Login
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="qrDownloadWrapper" class="text-center p-4">
                    <div class="qr-card">
                        <div class="qr-header mb-3">
                            <h5 id="namaKaryawan" class="mb-1">—</h5>
                            <p id="nipKaryawan" class="text-muted mb-0">—</p>
                        </div>
                        <div id="qrcodeContainer" class="d-flex justify-content-center mb-3"></div>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Scan QR Code untuk login otomatis
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <button type="button" class="btn btn-secondary-sage" id="downloadQRCode">
                    <i class="fas fa-download me-2"></i>Download
                </button>
                <button type="button" class="btn btn-sage" id="printQRCode">
                    <i class="fas fa-print me-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

@include('user.components.bottom-nav')
@include('user.components.sidebar-menu')
@endsection

@push('styles')
<style>
    :root {
        /* 🌿 Warna Sage Green (sesuai dashboard) */
        --primary-sage: #9DC183;
        --secondary-sage: #6FA976;
        --accent-cream: #FDF6EC;
        --accent-gold: #E4C988;
        --light-gray: #F5F7FA;
        --medium-gray: #A0AEC0;
        --dark-gray: #4A5568;
        --soft-pink: #F2C6B4;
        --soft-blue: #BFD8D2;
        --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
    }

    .profile-header-card {
        background: var(--primary-sage);
        color: white;
        padding: 2rem;
        border-radius: 24px;
        box-shadow: 0 8px 16px rgba(157, 193, 131, 0.3);
    }

    .profile-content-wrapper {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .profile-name {
        color: var(--dark-gray);
        font-weight: 700;
    }

    .profile-nip {
        color: var(--medium-gray);
        font-size: 0.95rem;
    }

    .profile-photo-wrapper {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }

    .profile-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--primary-sage);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .profile-photo-placeholder {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: var(--light-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid var(--primary-sage);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .profile-photo-placeholder i {
        font-size: 4rem;
        color: var(--medium-gray);
    }

    .btn-edit-photo {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        border: none;
        color: var(--primary-sage);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-edit-photo:hover {
        background: var(--primary-sage);
        color: white;
        transform: scale(1.1);
    }

    .profile-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .profile-badges .badge {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .badge-profile {
        background: var(--light-gray);
        color: var(--dark-gray);
        border: 1px solid #e0e0e0;
    }

    /* Badge Colors - Sage Theme */
    .bg-primary-sage {
        background: var(--primary-sage) !important;
        color: white;
    }

    .bg-secondary-sage {
        background: var(--secondary-sage) !important;
        color: white;
    }

    .bg-soft-blue {
        background: var(--soft-blue) !important;
        color: var(--dark-gray);
    }

    /* Text Colors */
    .text-primary-sage {
        color: var(--primary-sage) !important;
    }

    .text-secondary-sage {
        color: var(--secondary-sage) !important;
    }

    .text-accent-gold {
        color: var(--accent-gold) !important;
    }

    .text-soft-pink {
        color: var(--soft-pink) !important;
    }

    .text-soft-blue {
        color: var(--soft-blue) !important;
    }

    .text-medium-gray {
        color: var(--medium-gray) !important;
    }

    .info-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        height: 100%;
    }

    .card-header-custom {
        color: white;
        padding: 1.25rem 1.5rem;
    }

    /* Card Headers - Flat Colors */
    .bg-gradient-sage {
        background: var(--primary-sage);
    }

    .bg-gradient-secondary-sage {
        background: var(--secondary-sage);
    }

    .bg-gradient-soft-blue {
        background: var(--soft-blue);
        color: var(--dark-gray);
    }

    .bg-gradient-accent-gold {
        background: var(--accent-gold);
        color: white;
    }

    .bg-gradient-soft-pink {
        background: var(--soft-pink);
        color: var(--dark-gray);
    }

    .card-body-custom {
        padding: 1.5rem;
    }

    .info-item {
        padding: 1rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-item label {
        display: block;
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .info-value {
        font-size: 1rem;
        color: var(--dark-gray);
        font-weight: 500;
    }

    /* Button Styles - Flat Colors */
    .btn-sage {
        background: var(--primary-sage);
        border: none;
        color: white;
        transition: all 0.3s;
    }

    .btn-sage:hover {
        background: var(--secondary-sage);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(157, 193, 131, 0.4);
    }

    .btn-outline-sage {
        border: 2px solid var(--primary-sage);
        color: var(--primary-sage);
        background: transparent;
        transition: all 0.3s;
    }

    .btn-outline-sage:hover {
        background: var(--primary-sage);
        color: white;
        transform: translateY(-2px);
    }

    .btn-secondary-sage {
        background: var(--secondary-sage);
        border: none;
        color: white;
        transition: all 0.3s;
    }

    .btn-secondary-sage:hover {
        background: var(--primary-sage);
        color: white;
        transform: translateY(-2px);
    }

    .btn-accent-gold {
        background: var(--accent-gold);
        border: none;
        color: white;
        transition: all 0.3s;
    }

    .btn-accent-gold:hover {
        background: #D4B978;
        color: white;
        transform: translateY(-2px);
    }

    .bg-sage {
        background: var(--primary-sage) !important;
    }

    .qr-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
    }

    #qrcodeContainer {
        min-height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Password Toggle Button */
    .input-group .btn-outline-secondary {
        border-color: #ced4da;
    }

    .input-group .btn-outline-secondary:hover {
        background-color: var(--light-gray);
        border-color: var(--primary-sage);
        color: var(--primary-sage);
    }

    /* Alert Enhancements - Flat Colors */
    .alert {
        border-radius: 12px;
        border: none;
    }

    .alert-warning-sage {
        background: #FFF3E0;
        color: #E65100;
        border-left: 4px solid var(--accent-gold);
    }

    .alert-info-sage {
        background: #E8F5E9;
        color: #2E7D32;
        border-left: 4px solid var(--secondary-sage);
    }

    .alert-success {
        background: #E8F5E9;
        color: #2E7D32;
        border-left: 4px solid var(--primary-sage);
    }

    .alert-danger {
        background: #FFEBEE;
        color: #C62828;
        border-left: 4px solid #E57373;
    }

    @media (max-width: 768px) {
        .profile-container {
            padding: 0.5rem;
        }

        .profile-header-card {
            padding: 1.5rem;
        }

        .profile-content-wrapper {
            padding: 1.5rem;
        }

        .profile-badges {
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // Toggle Password Visibility
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById('toggle-' + inputId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Preview photo before upload
    const photoInput = document.getElementById('photoInput');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    const submitPhotoBtn = document.getElementById('submitPhotoBtn');

    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak valid. Gunakan JPG atau PNG.');
                    photoInput.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }

                // Validate file size (max 2MB)
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    photoInput.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    }

    // Form submission
    const photoForm = document.getElementById('photoForm');
    if (photoForm) {
        photoForm.addEventListener('submit', function(e) {
            submitPhotoBtn.disabled = true;
            submitPhotoBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
        });
    }

    // QR Code functionality
    document.addEventListener('DOMContentLoaded', function() {
        const qrcodeModalEl = document.getElementById('qrcodeModal');
        const qrcodeModal = new bootstrap.Modal(qrcodeModalEl);
        let currentQRCode = null;

        qrcodeModalEl.addEventListener('hidden.bs.modal', function() {
            const qrcodeContainer = document.getElementById('qrcodeContainer');
            qrcodeContainer.innerHTML = '';
            if (currentQRCode && typeof currentQRCode.clear === 'function') {
                try { currentQRCode.clear(); } catch(e) {}
            }
            currentQRCode = null;
        });

        window.showQRCodeModal = function(nama, nip, qrcodeToken) {
            document.getElementById('namaKaryawan').textContent = nama || '—';
            document.getElementById('nipKaryawan').textContent = nip ? ('NIP: ' + nip) : '—';

            const qrcodeContainer = document.getElementById('qrcodeContainer');
            qrcodeContainer.innerHTML = '';

            if (!qrcodeToken || !qrcodeToken.toString().trim()) {
                qrcodeContainer.innerHTML = '<div class="text-danger p-3"><i class="fas fa-exclamation-triangle mb-2"></i><br>QR Code token tidak tersedia.</div>';
                qrcodeModal.show();
                return;
            }

            const baseUrl = window.location.origin;
            const loginUrl = baseUrl + '/barcode-login/' + encodeURIComponent(qrcodeToken);

            try {
                currentQRCode = new QRCode(qrcodeContainer, {
                    text: loginUrl,
                    width: 280,
                    height: 280,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });

                setTimeout(() => qrcodeModal.show(), 50);
            } catch(err) {
                qrcodeContainer.innerHTML = '<div class="text-danger p-3"><i class="fas fa-times-circle mb-2"></i><br>Gagal generate QR Code.</div>';
                qrcodeModal.show();
            }
        };

        // Download QR Code
        document.getElementById('downloadQRCode').addEventListener('click', function() {
            const wrapper = document.getElementById('qrDownloadWrapper');
            html2canvas(wrapper, {
                backgroundColor: '#ffffff',
                scale: 3,
                logging: false
            }).then(canvas => {
                canvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    const nipText = document.getElementById('nipKaryawan').textContent.replace(/NIP:\s*/gi, '').replace(/\s+/g, '_') || 'karyawan';
                    a.download = `QRCode_${nipText}.png`;
                    a.click();
                    URL.revokeObjectURL(url);
                }, 'image/png');
            });
        });

        // Print QR Code
        document.getElementById('printQRCode').addEventListener('click', function() {
            const qrcodeCanvas = document.querySelector('#qrcodeContainer canvas');
            if (!qrcodeCanvas) {
                alert('Tidak ada QR Code untuk dicetak.');
                return;
            }

            const dataUrl = qrcodeCanvas.toDataURL('image/png');
            const nama = document.getElementById('namaKaryawan').textContent || '';
            const nip = document.getElementById('nipKaryawan').textContent || '';

            const printWindow = window.open('', '_blank', 'width=800,height=600');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8"/>
                    <title>Cetak QR Code - ${nama}</title>
                    <style>
                        body{font-family:Arial;display:flex;justify-content:center;align-items:center;min-height:100vh;padding:20px}
                        .qr-card{text-align:center;padding:30px}
                        h3{margin:0 0 8px 0}
                        img{width:280px;height:280px}
                    </style>
                </head>
                <body>
                    <div class="qr-card">
                        <h3>${nama}</h3>
                        <p>${nip}</p>
                        <img src="${dataUrl}" alt="QR Code"/>
                    </div>
                    <script>window.onload = function(){ setTimeout(function(){ window.print(); }, 250); }<\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        });
    });
</script>
@endpush