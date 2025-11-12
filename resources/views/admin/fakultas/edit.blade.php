@extends('layouts.app')

@section('title', 'Edit Fakultas')
@section('icon', 'fa-edit')

@section('content')
<!-- Breadcrumb Enhancement -->
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-2">
                <li class="breadcrumb-item"><a href="{{ route('admin.fakultas.index') }}"><i class="fas fa-university me-1"></i>Fakultas</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.fakultas.show', $fakultas->id_fakultas) }}">{{ $fakultas->kode_fakultas }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h3 class="mb-1"><i class="fas fa-edit text-warning me-2"></i>Edit Data Fakultas</h3>
        <p class="text-muted mb-0">Perbarui informasi fakultas {{ $fakultas->nama_fakultas }}</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Main Form Card -->
        <div class="card card-modern shadow-sm bg-dark">
            <div class="card-header bg-primary text-dark">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Form Edit Fakultas</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.fakultas.update', $fakultas) }}" method="POST" id="formEditFakultas">
                    @csrf
                    @method('PUT')
                    
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
                                           value="{{ old('kode_fakultas', $fakultas->kode_fakultas) }}" 
                                           maxlength="10"
                                           required>
                                    @error('kode_fakultas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Kode unik untuk fakultas
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
                                           value="{{ old('nama_fakultas', $fakultas->nama_fakultas) }}" 
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
                                           value="{{ old('dekan', $fakultas->dekan) }}" 
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
                                               {{ old('status_aktif', $fakultas->status_aktif) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-normal text-white" for="status_aktif">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            Fakultas Aktif
                                        </label>
                                    </div>
                                    @if(!$fakultas->status_aktif)
                                    <div class="alert alert-warning border-0 mt-2 mb-0 p-2">
                                        <small>
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Fakultas saat ini non-aktif
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning if has relations -->
                    @if($fakultas->departemen->count() > 0 || $fakultas->karyawan->count() > 0)
                    <div class="alert alert-info border-0 mb-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
                            <div>
                                <strong>Informasi Penting:</strong>
                                <p class="mb-0 small">
                                    Fakultas ini memiliki <strong>{{ $fakultas->departemen->count() }} departemen</strong> dan 
                                    <strong>{{ $fakultas->karyawan->count() }} karyawan</strong>. 
                                    @if(!old('status_aktif', $fakultas->status_aktif))
                                        Menonaktifkan fakultas akan mempengaruhi data terkait.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Required Fields Notice -->
                    <div class="alert alert-secondary border-0 mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Field yang bertanda <span class="text-danger">*</span> wajib diisi</small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                        <a href="{{ route('admin.fakultas.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <div>
                            <a href="{{ route('admin.fakultas.show', $fakultas->id_fakultas) }}" 
                               class="btn btn-info btn-modern me-2">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </a>
                            <button type="submit" class="btn btn-warning btn-modern">
                                <i class="fas fa-save me-2"></i>Update Fakultas
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <!-- Statistics Card -->
        <div class="card card-modern shadow-sm mb-3 bg-dark">
            <div class="card-header bg-primary text-white border-0">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Saat Ini</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="text-center p-3 bg-secondary rounded">
                            <i class="fas fa-building fa-2x text-primary mb-2"></i>
                            <h3 class="mb-0 fw-bold text-white">{{ $fakultas->departemen->count() }}</h3>
                            <small class="text-muted">Departemen</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-secondary rounded">
                            <i class="fas fa-users fa-2x text-success mb-2"></i>
                            <h3 class="mb-0 fw-bold text-white">{{ $fakultas->karyawan->count() }}</h3>
                            <small class="text-muted">Karyawan</small>
                        </div>
                    </div>
                </div>

                <hr class="border-secondary">

                <!-- Additional Stats -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">
                        <i class="fas fa-user-check text-info me-1"></i>Karyawan Aktif
                    </small>
                    <span class="badge bg-info">{{ $fakultas->karyawan->where('status_aktif', true)->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">
                        <i class="fas fa-calendar-plus text-success me-1"></i>Dibuat Pada
                    </small>
                    <small class="text-white">{{ $fakultas->created_at->format('d/m/Y') }}</small>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-calendar-edit text-warning me-1"></i>Terakhir Update
                    </small>
                    <small class="text-white">{{ $fakultas->updated_at->format('d/m/Y') }}</small>
                </div>
            </div>
        </div>

        <!-- Dekan Info -->
        @if($fakultas->dekan)
        <div class="card card-modern shadow-sm mb-3 bg-dark">
            <div class="card-header bg-info text-white border-0">
                <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Pimpinan</h6>
            </div>
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 70px; height: 70px;">
                    <i class="fas fa-user fa-2x text-info"></i>
                </div>
                <h6 class="mb-1 fw-bold text-white">{{ $fakultas->dekan }}</h6>
                <small class="text-muted">Dekan {{ $fakultas->nama_fakultas }}</small>
            </div>
        </div>
        @endif

        <!-- Help Card -->
        <div class="card card-modern shadow-sm border-warning bg-dark">
            <div class="card-header bg-warning text-dark border-0">
                <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small text-white">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Perubahan kode akan mempengaruhi data terkait
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Status non-aktif akan mempengaruhi departemen
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Pastikan data sudah benar sebelum menyimpan
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

        // Show warning when deactivating faculty with relations
        $('#status_aktif').on('change', function() {
            if (!this.checked) {
                const departemenCount = {{ $fakultas->departemen->count() }};
                const karyawanCount = {{ $fakultas->karyawan->count() }};
                
                if (departemenCount > 0 || karyawanCount > 0) {
                    const message = `Fakultas ini memiliki ${departemenCount} departemen dan ${karyawanCount} karyawan.\n\nYakin ingin menonaktifkan fakultas ini?`;
                    
                    if (!confirm(message)) {
                        this.checked = true;
                    }
                }
            }
        });

        // Form validation
        $('#formEditFakultas').on('submit', function(e) {
            const kode = $('#kode_fakultas').val();
            const nama = $('#nama_fakultas').val();
            
            if (!kode || !nama) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi (*)');
                return false;
            }
        });
    });
</script>
@endpush