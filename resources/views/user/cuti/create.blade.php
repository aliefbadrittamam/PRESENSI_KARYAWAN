@extends('layouts.user')

@section('title', 'Ajukan Cuti')

@section('content')
<div class="container-desktop">
    @include('user.components.header', ['karyawan' => $karyawan])

    <div class="cuti-container">
        <div class="cuti-card">
            <div class="card-header-custom">
                <h4 class="mb-0">
                    <i class="fas fa-umbrella-beach me-2"></i>
                    Pengajuan Cuti
                </h4>
            </div>

            <div class="card-body-custom">
                <!-- Sisa Cuti Info -->
                <div class="alert alert-info mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-1">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Sisa Kuota Cuti Tahunan Anda</strong>
                            </h6>
                            <p class="mb-0">Kuota cuti dapat digunakan untuk pengajuan cuti tahunan</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                            <h2 class="mb-0">
                                <span class="badge bg-primary" style="font-size: 1.5rem;">
                                    {{ $sisaCuti }} Hari
                                </span>
                            </h2>
                        </div>
                    </div>
                </div>

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

                <form action="{{ route('user.cuti.store') }}" method="POST" enctype="multipart/form-data" id="cutiForm">
                    @csrf

                    <!-- Jenis Cuti -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-list-ul me-2 text-primary"></i>
                            Jenis Cuti <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-md-4 col-6">
                                <div class="form-check-card">
                                    <input class="form-check-input" type="radio" name="jenis_cuti" id="jenis_tahunan" value="tahunan" {{ old('jenis_cuti') == 'tahunan' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="jenis_tahunan">
                                        <i class="fas fa-calendar text-primary"></i>
                                        <span>Tahunan</span>
                                        <small class="d-block text-muted">Cuti reguler</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="form-check-card">
                                    <input class="form-check-input" type="radio" name="jenis_cuti" id="jenis_sakit" value="sakit" {{ old('jenis_cuti') == 'sakit' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="jenis_sakit">
                                        <i class="fas fa-hospital text-danger"></i>
                                        <span>Sakit</span>
                                        <small class="d-block text-muted">Cuti sakit</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="form-check-card">
                                    <input class="form-check-input" type="radio" name="jenis_cuti" id="jenis_melahirkan" value="melahirkan" {{ old('jenis_cuti') == 'melahirkan' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="jenis_melahirkan">
                                        <i class="fas fa-baby text-info"></i>
                                        <span>Melahirkan</span>
                                        <small class="d-block text-muted">3 bulan</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="form-check-card">
                                    <input class="form-check-input" type="radio" name="jenis_cuti" id="jenis_menikah" value="menikah" {{ old('jenis_cuti') == 'menikah' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="jenis_menikah">
                                        <i class="fas fa-ring text-success"></i>
                                        <span>Menikah</span>
                                        <small class="d-block text-muted">Cuti nikah</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="form-check-card">
                                    <input class="form-check-input" type="radio" name="jenis_cuti" id="jenis_khusus" value="khusus" {{ old('jenis_cuti') == 'khusus' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="jenis_khusus">
                                        <i class="fas fa-star text-warning"></i>
                                        <span>Khusus</span>
                                        <small class="d-block text-muted">Lainnya</small>
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
                    <div class="alert alert-success mb-4" id="durasiInfo" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Durasi:</strong> <span id="jumlahHari">0</span> hari
                        <span id="warningKuota" class="text-danger" style="display: none;">
                            <br><i class="fas fa-exclamation-triangle me-1"></i>
                            Melebihi sisa kuota cuti tahunan!
                        </span>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-comment-dots me-2 text-warning"></i>
                            Alasan/Keterangan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-lg" name="keterangan" id="keterangan" rows="4" placeholder="Jelaskan alasan cuti Anda (minimal 10 karakter)..." required minlength="10" maxlength="500">{{ old('keterangan') }}</textarea>
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
                            Dokumen pendukung seperti surat undangan, surat keterangan, dll. Format: PDF, JPG, PNG (Max: 2MB)
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
                            Ketentuan Cuti
                        </h6>
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-check-circle text-success"></i>
                                <strong>Cuti Tahunan:</strong> Kuota 12 hari per tahun, pengajuan minimal H-7
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success"></i>
                                <strong>Cuti Sakit:</strong> Wajib melampirkan surat keterangan dokter
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success"></i>
                                <strong>Cuti Melahirkan:</strong> Maksimal 3 bulan (90 hari)
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success"></i>
                                <strong>Cuti Menikah:</strong> Maksimal 3 hari, lampirkan surat undangan
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success"></i>
                                Semua pengajuan cuti harus mendapat persetujuan dari atasan
                            </li>
                        </ul>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i>
                            Kirim Pengajuan Cuti
                        </button>
                        <a href="{{ route('user.cuti.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('user.components.bottom-nav')
@include('user.components.sidebar-menu')
@endsection

@push('styles')
<style>
    .cuti-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 1rem;
    }

    .cuti-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .card-header-custom {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 1.5rem;
    }

    .card-body-custom {
        padding: 2rem;
    }

    .form-check-card {
        position: relative;
        padding: 1.2rem;
        border: 2px solid #e0e0e0;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
        height: 100%;
    }

    .form-check-card:hover {
        border-color: #f093fb;
        background: #fff5f8;
    }

    .form-check-card input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .form-check-card input[type="radio"]:checked + label {
        color: #f5576c;
    }

    .form-check-card input[type="radio"]:checked ~ label::before {
        content: '';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 20px;
        height: 20px;
        background: #f5576c;
        border-radius: 50%;
    }

    .form-check-card input[type="radio"]:checked ~ label::after {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 14px;
        color: white;
        font-weight: bold;
        font-size: 0.85rem;
    }

    .form-check-label {
        cursor: pointer;
        width: 100%;
    }

    .form-check-label i {
        font-size: 1.8rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .form-check-label span {
        font-weight: 600;
        font-size: 1rem;
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
        align-items: flex-start;
        gap: 0.75rem;
    }

    .info-list i {
        font-size: 1rem;
        margin-top: 0.2rem;
    }

    @media (max-width: 768px) {
        .card-body-custom {
            padding: 1.5rem;
        }
        
        .cuti-container {
            padding: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const sisaCuti = {{ $sisaCuti }};

    // Calculate duration
    function calculateDuration() {
        const startDate = document.getElementById('tanggal_mulai').value;
        const endDate = document.getElementById('tanggal_selesai').value;
        const jenisCuti = document.querySelector('input[name="jenis_cuti"]:checked')?.value;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            if (diffDays > 0) {
                document.getElementById('jumlahHari').textContent = diffDays;
                document.getElementById('durasiInfo').style.display = 'block';
                
                // Warning jika melebihi kuota cuti tahunan
                if (jenisCuti === 'tahunan' && diffDays > sisaCuti) {
                    document.getElementById('warningKuota').style.display = 'inline';
                } else {
                    document.getElementById('warningKuota').style.display = 'none';
                }
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

    document.querySelectorAll('input[name="jenis_cuti"]').forEach(radio => {
        radio.addEventListener('change', calculateDuration);
    });

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
    document.getElementById('cutiForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    });
</script>
@endpush