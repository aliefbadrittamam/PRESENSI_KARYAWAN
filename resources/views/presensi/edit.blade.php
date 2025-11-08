@extends('layouts.app')

@section('title', 'Edit Presensi')
@section('icon', 'fa-edit')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Data Presensi
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('presensi.update', $presensi->id_presensi) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Data Karyawan</h6>
                            
                            <div class="mb-3">
                                <label for="id_karyawan" class="form-label text-white">Karyawan *</label>
                                <select class="form-select form-control-modern @error('id_karyawan') is-invalid @enderror" 
                                        id="id_karyawan" name="id_karyawan" required>
                                    <option value="">Pilih Karyawan</option>
                                    @foreach($karyawan as $k)
                                        <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan', $presensi->id_karyawan) == $k->id_karyawan ? 'selected' : '' }}>
                                            {{ $k->nama_lengkap }} ({{ $k->nip }}) - {{ $k->jabatan->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_karyawan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="id_shift" class="form-label text-white">Shift Kerja *</label>
                                <select class="form-select form-control-modern @error('id_shift') is-invalid @enderror" 
                                        id="id_shift" name="id_shift" required>
                                    <option value="">Pilih Shift</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id_shift }}" {{ old('id_shift', $presensi->id_shift) == $shift->id_shift ? 'selected' : '' }}>
                                            {{ $shift->nama_shift }} ({{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_presensi" class="form-label text-white">Tanggal Presensi *</label>
                                <input type="date" class="form-control form-control-modern @error('tanggal_presensi') is-invalid @enderror" 
                                       id="tanggal_presensi" name="tanggal_presensi" 
                                       value="{{ old('tanggal_presensi', $presensi->tanggal_presensi->format('Y-m-d')) }}" required>
                                @error('tanggal_presensi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status_kehadiran" class="form-label text-white">Status Kehadiran *</label>
                                <select class="form-select form-control-modern @error('status_kehadiran') is-invalid @enderror" 
                                        id="status_kehadiran" name="status_kehadiran" required>
                                    <option value="">Pilih Status</option>
                                    <option value="hadir" {{ old('status_kehadiran', $presensi->status_kehadiran) == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="terlambat" {{ old('status_kehadiran', $presensi->status_kehadiran) == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                    <option value="izin" {{ old('status_kehadiran', $presensi->status_kehadiran) == 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="sakit" {{ old('status_kehadiran', $presensi->status_kehadiran) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="cuti" {{ old('status_kehadiran', $presensi->status_kehadiran) == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                    <option value="alpha" {{ old('status_kehadiran', $presensi->status_kehadiran) == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                </select>
                                @error('status_kehadiran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-clock me-2"></i>Waktu Presensi</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jam_masuk" class="form-label text-white">Jam Masuk</label>
                                        <input type="time" class="form-control form-control-modern @error('jam_masuk') is-invalid @enderror" 
                                               id="jam_masuk" name="jam_masuk" 
                                               value="{{ old('jam_masuk', $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '') }}">
                                        @error('jam_masuk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jam_keluar" class="form-label text-white">Jam Keluar</label>
                                        <input type="time" class="form-control form-control-modern @error('jam_keluar') is-invalid @enderror" 
                                               id="jam_keluar" name="jam_keluar" 
                                               value="{{ old('jam_keluar', $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') : '') }}">
                                        @error('jam_keluar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Presensi</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="latitude_masuk" class="form-label text-white">Latitude Masuk</label>
                                        <input type="number" step="any" class="form-control form-control-modern @error('latitude_masuk') is-invalid @enderror" 
                                               id="latitude_masuk" name="latitude_masuk" 
                                               value="{{ old('latitude_masuk', $presensi->latitude_masuk) }}">
                                        @error('latitude_masuk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="longitude_masuk" class="form-label text-white">Longitude Masuk</label>
                                        <input type="number" step="any" class="form-control form-control-modern @error('longitude_masuk') is-invalid @enderror" 
                                               id="longitude_masuk" name="longitude_masuk" 
                                               value="{{ old('longitude_masuk', $presensi->longitude_masuk) }}">
                                        @error('longitude_masuk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="latitude_keluar" class="form-label text-white">Latitude Keluar</label>
                                        <input type="number" step="any" class="form-control form-control-modern @error('latitude_keluar') is-invalid @enderror" 
                                               id="latitude_keluar" name="latitude_keluar" 
                                               value="{{ old('latitude_keluar', $presensi->latitude_keluar) }}">
                                        @error('latitude_keluar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="longitude_keluar" class="form-label text-white">Longitude Keluar</label>
                                        <input type="number" step="any" class="form-control form-control-modern @error('longitude_keluar') is-invalid @enderror" 
                                               id="longitude_keluar" name="longitude_keluar" 
                                               value="{{ old('longitude_keluar', $presensi->longitude_keluar) }}">
                                        @error('longitude_keluar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="catatan" class="form-label text-white">Catatan</label>
                                <textarea class="form-control form-control-modern @error('catatan') is-invalid @enderror" 
                                          id="catatan" name="catatan" rows="3">{{ old('catatan', $presensi->catatan) }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current Info -->
                            <div class="card-modern p-3 mt-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Saat Ini</h6>
                                <div class="text-white-50">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Keterlambatan:</span>
                                        <span class="badge bg-{{ $presensi->keterlambatan_menit > 0 ? 'warning' : 'success' }}">
                                            {{ $presensi->keterlambatan_menit }} menit
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Total Jam Kerja:</span>
                                        <span class="badge bg-info">
                                            {{ $presensi->total_jam_kerja ? number_format($presensi->total_jam_kerja, 1) . ' jam' : '-' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Status Verifikasi:</span>
                                        <span class="badge bg-{{ $presensi->status_verifikasi == 'verified' ? 'success' : ($presensi->status_verifikasi == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ strtoupper($presensi->status_verifikasi) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.presensi.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <div>
                            <a href="{{ route('admin.presensi.show', $presensi->id_presensi) }}" class="btn btn-info btn-modern me-2">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </a>
                            <button type="submit" class="btn btn-primary-modern btn-modern">
                                <i class="fas fa-save me-2"></i>Update Presensi
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
    // Auto-calculate total hours when times are changed
    function calculateTotalHours() {
        const jamMasuk = document.getElementById('jam_masuk').value;
        const jamKeluar = document.getElementById('jam_keluar').value;
        
        if (jamMasuk && jamKeluar) {
            const start = new Date(`2000-01-01T${jamMasuk}`);
            let end = new Date(`2000-01-01T${jamKeluar}`);
            
            // Handle overnight
            if (end < start) {
                end.setDate(end.getDate() + 1);
            }
            
            const totalMinutes = (end - start) / (1000 * 60);
            const totalHours = totalMinutes / 60;
            
            // You can display this somewhere or just let the backend calculate
            console.log('Total hours:', totalHours.toFixed(1));
        }
    }

    document.getElementById('jam_masuk').addEventListener('change', calculateTotalHours);
    document.getElementById('jam_keluar').addEventListener('change', calculateTotalHours);
</script>
@endpush