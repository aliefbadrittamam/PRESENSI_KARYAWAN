@extends('layouts.app')

@section('title', 'Tambah Departemen')
@section('icon', 'fa-plus-circle')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Departemen Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.departemen.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode_departemen" class="form-label text-white">Kode Departemen *</label>
                                <input type="text" class="form-control form-control-modern @error('kode_departemen') is-invalid @enderror" 
                                       id="kode_departemen" name="kode_departemen" value="{{ old('kode_departemen') }}" 
                                       placeholder="Contoh: TIF, MKA, AKT" required>
                                <div class="form-text text-muted">Kode unik untuk departemen (max: 10 karakter)</div>
                                @error('kode_departemen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nama_departemen" class="form-label text-white">Nama Departemen *</label>
                                <input type="text" class="form-control form-control-modern @error('nama_departemen') is-invalid @enderror" 
                                       id="nama_departemen" name="nama_departemen" value="{{ old('nama_departemen') }}" 
                                       placeholder="Contoh: Teknik Informatika, Manajemen, Akuntansi" required>
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
                                        <option value="{{ $fak->id_fakultas }}" {{ old('id_fakultas') == $fak->id_fakultas ? 'selected' : '' }}>
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
                                          id="deskripsi" name="deskripsi" rows="5" 
                                          placeholder="Deskripsi singkat tentang departemen...">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Status</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="status_aktif" name="status_aktif" value="1" checked>
                                    <label class="form-check-label text-white" for="status_aktif">Departemen Aktif</label>
                                </div>
                                <div class="form-text text-muted">Non-aktifkan jika departemen sudah tidak beroperasi</div>
                            </div>

                            <!-- Preview Card -->
                            <div class="card-modern p-3 mt-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-eye me-2"></i>Pratinjau</h6>
                                <div class="text-center">
                                    <i class="fas fa-building fa-2x text-info mb-2"></i>
                                    <h6 id="previewNama" class="text-white mb-1">Nama Departemen</h6>
                                    <span id="previewKode" class="badge bg-primary badge-modern">KODE</span>
                                    <div id="previewFakultas" class="mt-2">
                                        <small class="text-muted">Fakultas: <span id="previewFakultasNama">-</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.departemen.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-save me-2"></i>Simpan Departemen
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
    // Auto-format kode departemen to uppercase
    document.getElementById('kode_departemen').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Update preview in real-time
    function updatePreview() {
        const nama = document.getElementById('nama_departemen').value || 'Nama Departemen';
        const kode = document.getElementById('kode_departemen').value || 'KODE';
        
        document.getElementById('previewNama').textContent = nama;
        document.getElementById('previewKode').textContent = kode;

        // Update fakultas name
        const fakultasSelect = document.getElementById('id_fakultas');
        const selectedOption = fakultasSelect.options[fakultasSelect.selectedIndex];
        const fakultasNama = selectedOption.text ? selectedOption.text.split(' (')[0] : '-';
        document.getElementById('previewFakultasNama').textContent = fakultasNama;
    }

    // Add event listeners for real-time preview
    document.getElementById('nama_departemen').addEventListener('input', updatePreview);
    document.getElementById('kode_departemen').addEventListener('input', updatePreview);
    document.getElementById('id_fakultas').addEventListener('change', updatePreview);

    // Initialize preview
    updatePreview();
</script>
@endpush