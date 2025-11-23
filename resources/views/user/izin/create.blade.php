{{-- File: resources/views/user/izin/create.blade.php --}}
@extends('layouts.user')

@section('title', 'Ajukan Izin')

@section('content')
<div class="container-desktop">
    <!-- Header -->
    @include('user.components.header', ['karyawan' => $karyawan])

    <!-- Form Card -->
    <div class="izin-container">
        <div class="izin-card">
            <div class="card-header-custom">
                <h4 class="mb-0">
                    <i class="fas fa-file-medical me-2"></i>
                    Pengajuan Izin
                </h4>
            </div>

            <div class="card-body-custom">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Terjadi Kesalahan!</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('user.izin.store') }}" method="POST" enctype="multipart/form-data" id="izinForm">
                    @csrf

                    <!-- Tipe Izin -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-clipboard-list me-2 text-primary"></i>
                            Jenis Izin <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="form-check-card">
                                    <input class="form-check-input" type="radio" name="tipe_izin" id="tipe_izin_izin" value="izin" {{ old('tipe_izin') == 'izin' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="tipe_izin_izin">
                                        <i class="fas fa-calendar-alt text-info"></i>
                                        <span>Izin</span>
                                        <small class="d-block text-muted">Izin tidak masuk kerja</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check-card">
                                    <input class="form-check-input" type="radio" name="tipe_izin" id="tipe_izin_sakit" value="sakit" {{ old('tipe_izin') == 'sakit' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="tipe_izin_sakit">
                                        <i class="fas fa-hospital text-danger"></i>
                                        <span>Sakit</span>
                                        <small class="d-block text-muted">Sakit/tidak bisa hadir</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-day me-2 text-success"></i>
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control form-control-lg" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-check me-2 text-danger"></i>
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control form-control-lg" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <!-- Info Jumlah Hari -->
                    <div class="alert alert-info mb-4" id="durasiInfo" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Durasi:</strong> <span id="jumlahHari">0</span> hari
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-comment-dots me-2 text-warning"></i>
                            Alasan/Keterangan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-lg" name="keterangan" id="keterangan" rows="4" placeholder="Jelaskan alasan izin Anda (minimal 10 karakter)..." required minlength="10" maxlength="500">{{ old('keterangan') }}</textarea>
                        <div class="form-text">
                            <span id="charCount">0</span>/500 karakter
                        </div>
                    </div>

                    <!-- File Pendukung -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-paperclip me-2 text-secondary"></i>
                            File Pendukung (Opsional)
                        </label>
                        <input type="file" class="form-control form-control-lg" name="file_pendukung" id="file_pendukung" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Surat keterangan dokter atau file pendukung lainnya. Format: PDF, JPG, PNG (Max: 2MB)
                        </div>
                        <div id="filePreview" class="mt-2" style="display: none;">
                            <div class="alert alert-success">
                                <i class="fas fa-file-alt me-2"></i>
                                <span id="fileName"></span>
                                <button type="button" class="btn btn-sm btn-danger float-end" id="removeFile">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="info-box mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>
                            Informasi Penting
                        </h6>
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-check-circle text-success"></i>
                                Pengajuan izin minimal H-1 (sehari sebelumnya)
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success"></i>
                                Untuk sakit, harap lampirkan surat keterangan dokter
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success"></i>
                                Pengajuan akan diverifikasi oleh admin/supervisor
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success"></i>
                                Anda akan mendapat notifikasi setelah izin disetujui/ditolak
                            </li>
                        </ul>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i>
                            Kirim Pengajuan Izin
                        </button>
                        <a href="{{ route('user.izin.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Navigation -->
@include('user.components.bottom-nav')

<!-- Sidebar Menu -->
@include('user.components.sidebar-menu')
@endsection

@push('styles')
<style>
    .izin-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 1rem;
    }

    .izin-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
    }

    .card-body-custom {
        padding: 2rem;
    }

    .form-check-card {
        position: relative;
        padding: 1.5rem;
        border: 2px solid #e0e0e0;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
    }

    .form-check-card:hover {
        border-color: #667eea;
        background: #f8f9ff;
    }

    .form-check-card input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .form-check-card input[type="radio"]:checked + label {
        color: #667eea;
    }

    .form-check-card input[type="radio"]:checked ~ label::before {
        content: '';
        position: absolute;
        top: 10px;
        right: 10px;
        width: 24px;
        height: 24px;
        background: #667eea;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-check-card input[type="radio"]:checked ~ label::after {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 17px;
        color: white;
        font-weight: bold;
    }

    .form-check-label {
        cursor: pointer;
        width: 100%;
    }

    .form-check-label i {
        font-size: 2rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .form-check-label span {
        font-weight: 600;
        font-size: 1.1rem;
        display: block;
    }

    .info-box {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 1.5rem;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-list li {
        padding: 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .info-list i {
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .card-body-custom {
            padding: 1.5rem;
        }
        
        .izin-container {
            padding: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Calculate duration
    function calculateDuration() {
        const startDate = document.getElementById('tanggal_mulai').value;
        const endDate = document.getElementById('tanggal_selesai').value;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            if (diffDays > 0) {
                document.getElementById('jumlahHari').textContent = diffDays;
                document.getElementById('durasiInfo').style.display = 'block';
            } else {
                document.getElementById('durasiInfo').style.display = 'none';
            }
        }
    }

    document.getElementById('tanggal_mulai').addEventListener('change', function() {
        document.getElementById('tanggal_selesai').min = this.value;
        calculateDuration();
    });

    document.getElementById('tanggal_selesai').addEventListener('change', calculateDuration);

    // Character count
    document.getElementById('keterangan').addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length;
    });

    // File preview
    document.getElementById('file_pendukung').addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('filePreview').style.display = 'block';
        }
    });

    document.getElementById('removeFile').addEventListener('click', function() {
        document.getElementById('file_pendukung').value = '';
        document.getElementById('filePreview').style.display = 'none';
    });

    // Form submission
    document.getElementById('izinForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    });
</script>
@endpush