@extends('layouts.app')

@section('title', 'Tambah Shift Kerja')
@section('icon', 'fa-plus-circle')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Shift Kerja Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.shift.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode_shift" class="form-label text-white">Kode Shift *</label>
                                <input type="text" class="form-control form-control-modern @error('kode_shift') is-invalid @enderror" 
                                       id="kode_shift" name="kode_shift" value="{{ old('kode_shift') }}" 
                                       placeholder="Contoh: S1, S2, PAGI, SIANG" required>
                                <div class="form-text text-muted">Kode unik untuk shift kerja</div>
                                @error('kode_shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nama_shift" class="form-label text-white">Nama Shift *</label>
                                <input type="text" class="form-control form-control-modern @error('nama_shift') is-invalid @enderror" 
                                       id="nama_shift" name="nama_shift" value="{{ old('nama_shift') }}" 
                                       placeholder="Contoh: Shift Pagi, Shift Malam" required>
                                @error('nama_shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jam_mulai" class="form-label text-white">Jam Mulai *</label>
                                        <input type="time" class="form-control form-control-modern @error('jam_mulai') is-invalid @enderror" 
                                               id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                                        @error('jam_mulai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jam_selesai" class="form-label text-white">Jam Selesai *</label>
                                        <input type="time" class="form-control form-control-modern @error('jam_selesai') is-invalid @enderror" 
                                               id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
                                        @error('jam_selesai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="toleransi_keterlambatan" class="form-label text-white">Toleransi Keterlambatan (menit)</label>
                                <input type="number" class="form-control form-control-modern @error('toleransi_keterlambatan') is-invalid @enderror" 
                                       id="toleransi_keterlambatan" name="toleransi_keterlambatan" 
                                       value="{{ old('toleransi_keterlambatan', 15) }}" min="0" max="120">
                                <div class="form-text text-muted">Waktu toleransi keterlambatan dalam menit</div>
                                @error('toleransi_keterlambatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label text-white">Keterangan</label>
                                <textarea class="form-control form-control-modern @error('keterangan') is-invalid @enderror" 
                                          id="keterangan" name="keterangan" rows="3" 
                                          placeholder="Keterangan tambahan tentang shift...">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="status_aktif" name="status_aktif" value="1" checked>
                                <label class="form-check-label text-white" for="status_aktif">Shift Aktif</label>
                            </div>

                            <!-- Preview Card -->
                            <div class="card-modern p-3 mt-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-eye me-2"></i>Pratinjau Shift</h6>
                                <div class="text-center">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h6 id="previewNama" class="text-white mb-1">Nama Shift</h6>
                                    <span id="previewKode" class="badge bg-primary badge-modern">KODE</span>
                                    <div class="mt-2">
                                        <small class="text-muted d-block">Jam Kerja</small>
                                        <strong id="previewJam" class="text-white">00:00 - 00:00</strong>
                                    </div>
                                    <div class="mt-1">
                                        <small class="text-muted">Durasi:</small>
                                        <span id="previewDurasi" class="badge bg-info ms-1">0 jam</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.shift.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-save me-2"></i>Simpan Shift
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
    // Auto-format kode shift to uppercase
    document.getElementById('kode_shift').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Update preview in real-time
    function updatePreview() {
        const nama = document.getElementById('nama_shift').value || 'Nama Shift';
        const kode = document.getElementById('kode_shift').value || 'KODE';
        const jamMulai = document.getElementById('jam_mulai').value || '00:00';
        const jamSelesai = document.getElementById('jam_selesai').value || '00:00';
        
        document.getElementById('previewNama').textContent = nama;
        document.getElementById('previewKode').textContent = kode;
        document.getElementById('previewJam').textContent = `${jamMulai} - ${jamSelesai}`;

        // Calculate duration
        if (jamMulai && jamSelesai) {
            const start = new Date(`2000-01-01T${jamMulai}`);
            let end = new Date(`2000-01-01T${jamSelesai}`);
            
            // Handle overnight shift
            if (end < start) {
                end.setDate(end.getDate() + 1);
            }
            
            const durationHours = (end - start) / (1000 * 60 * 60);
            document.getElementById('previewDurasi').textContent = `${durationHours.toFixed(1)} jam`;
        }
    }

    // Add event listeners for real-time preview
    document.getElementById('nama_shift').addEventListener('input', updatePreview);
    document.getElementById('kode_shift').addEventListener('input', updatePreview);
    document.getElementById('jam_mulai').addEventListener('input', updatePreview);
    document.getElementById('jam_selesai').addEventListener('input', updatePreview);

    // Initialize preview
    updatePreview();
</script>
@endpush