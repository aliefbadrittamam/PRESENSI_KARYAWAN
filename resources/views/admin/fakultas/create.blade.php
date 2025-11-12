@extends('layouts.app')

@section('title', 'Tambah Fakultas')
@section('icon', 'fa-plus-circle')

@section('content')
<!-- Breadcrumb Enhancement -->
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-2">
                <li class="breadcrumb-item"><a href="{{ route('admin.fakultas.index') }}"><i class="fas fa-university me-1"></i>Fakultas</a></li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
        <h3 class="mb-1"><i class="fas fa-plus-circle text-primary me-2"></i>Tambah Fakultas Baru</h3>
        <p class="text-muted mb-0">Lengkapi form di bawah untuk menambahkan fakultas baru</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Main Form Card -->
        <div class="card card-modern shadow-sm bg-dark">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Informasi Fakultas</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.fakultas.store') }}" method="POST" id="formFakultas">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Kode Fakultas -->
                            <div class="mb-4">
                                <label for="kode_fakultas" class="form-label fw-bold text-white">
                                    Kode Fakultas <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary text-white border-secondary">
                                        <i class="fas fa-barcode text-primary"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control form-control-modern @error('kode_fakultas') is-invalid @enderror" 
                                           id="kode_fakultas" 
                                           name="kode_fakultas" 
                                           value="{{ old('kode_fakultas') }}" 
                                           placeholder="Contoh: FIT, FEB, FIK" 
                                           maxlength="10"
                                           required>
                                    @error('kode_fakultas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Kode unik untuk fakultas (maksimal 10 karakter)
                                </small>
                            </div>

                            <!-- Nama Fakultas -->
                            <div class="mb-4">
                                <label for="nama_fakultas" class="form-label fw-bold text-white">
                                    Nama Fakultas <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary text-white border-secondary">
                                        <i class="fas fa-university text-primary"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control form-control-modern @error('nama_fakultas') is-invalid @enderror" 
                                           id="nama_fakultas" 
                                           name="nama_fakultas" 
                                           value="{{ old('nama_fakultas') }}" 
                                           placeholder="Contoh: Fakultas Ilmu Komputer" 
                                           required>
                                    @error('nama_fakultas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Nama lengkap fakultas
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Dekan -->
                            <div class="mb-4">
                                <label for="dekan" class="form-label fw-bold text-white">
                                    Nama Dekan
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary text-white border-secondary">
                                        <i class="fas fa-user-tie text-primary"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control form-control-modern @error('dekan') is-invalid @enderror" 
                                           id="dekan" 
                                           name="dekan" 
                                           value="{{ old('dekan') }}" 
                                           placeholder="Nama Dekan saat ini">
                                    @error('dekan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Opsional - Bisa diisi kemudian
                                </small>
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-white">Status Fakultas</label>
                                <div class="card bg-secondary border-0 p-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="status_aktif" 
                                               name="status_aktif" 
                                               value="1" 
                                               checked>
                                        <label class="form-check-label fw-normal text-white" for="status_aktif">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            Fakultas Aktif
                                        </label>
                                    </div>
                                    <small class="text-muted mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Non-aktifkan jika fakultas sudah tidak beroperasi
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Required Fields Notice -->
                    <div class="alert alert-info border-0 mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Field yang bertanda <span class="text-danger">*</span> wajib diisi</small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('admin.fakultas.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary btn-modern">
                            <i class="fas fa-save me-2"></i>Simpan Fakultas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <!-- Preview Card -->
        <div class="card card-modern shadow-sm mb-3 bg-dark">
            <div class="card-header bg-secondary border-0">
                <h6 class="mb-0 text-white"><i class="fas fa-chart-bar text-primary me-2"></i>Informasi Awal</h6>
            </div>
            <div class="card-body text-center p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-secondary rounded">
                            <i class="fas fa-building fa-2x text-primary mb-2"></i>
                            <h4 class="mb-0 fw-bold text-white">0</h4>
                            <small class="text-muted">Departemen</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-secondary rounded">
                            <i class="fas fa-users fa-2x text-success mb-2"></i>
                            <h4 class="mb-0 fw-bold text-white">0</h4>
                            <small class="text-muted">Karyawan</small>
                        </div>
                    </div>
                </div>
                <hr class="my-3 border-secondary">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Data akan bertambah seiring penambahan departemen dan karyawan
                </small>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card card-modern shadow-sm border-primary bg-dark">
            <div class="card-header bg-primary text-white border-0">
                <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>Bantuan</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small text-white">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Pastikan kode fakultas unik
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Gunakan nama fakultas yang jelas
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Nama dekan bisa diisi kemudian
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Aktifkan status jika fakultas beroperasi
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-format kode fakultas to uppercase
        $('#kode_fakultas').on('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Auto-capitalize each word in nama fakultas
        $('#nama_fakultas').on('input', function() {
            this.value = this.value.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        });

        // Form validation feedback
        $('#formFakultas').on('submit', function(e) {
            const kode = $('#kode_fakultas').val();
            const nama = $('#nama_fakultas').val();
            
            if (!kode || !nama) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi (*)');
                return false;
            }
        });

        // Character counter for kode fakultas
        $('#kode_fakultas').on('input', function() {
            const length = $(this).val().length;
            const maxLength = 10;
            
            if (length > 0) {
                $(this).next('.form-text').html(
                    `<i class="fas fa-info-circle me-1"></i>Kode unik untuk fakultas (${length}/${maxLength} karakter)`
                );
            }
        });
    });
</script>
@endpush