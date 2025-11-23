@extends('layouts.app')

@section('title', 'Data Presensi')
@section('icon', 'fa-calendar-check')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-white mb-0"><i class="fas fa-calendar-check me-2"></i>Data Presensi</h4>
        <div>
            <a href="{{ route('presensi.create') }}" class="btn btn-primary-modern btn-modern me-2">
                <i class="fas fa-plus-circle me-2"></i>Tambah Presensi
            </a>
            <a href="{{ route('admin.presensi.rekap') }}" class="btn btn-info btn-modern">
                <i class="fas fa-chart-bar me-2"></i>Rekap Presensi
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="card-modern mb-4">
        <div class="card-body">
            <form action="{{ route('admin.presensi.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-white">Tanggal</label>
                    <input type="date" class="form-control form-control-modern" name="tanggal"
                        value="{{ request('tanggal', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-white">Karyawan</label>
                    <select class="form-select form-control-modern" name="karyawan">
                        <option value="">Semua Karyawan</option>
                        @foreach ($karyawan as $k)
                            <option value="{{ $k->id_karyawan }}"
                                {{ request('karyawan') == $k->id_karyawan ? 'selected' : '' }}>
                                {{ $k->nama_lengkap }} ({{ $k->nip }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-white">Status Kehadiran</label>
                    <select class="form-select form-control-modern" name="status">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat
                        </option>
                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-modern btn-modern w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card-modern">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-modern table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Karyawan</th>
                            <th>Kode Jabatan</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Total Jam</th>
                            <th>Status</th>
                            <th>Verifikasi</th>
                            <th width="100" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensi as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                {{-- Karyawan --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($item->karyawan && $item->karyawan->foto)
                                            <img src="{{ asset('storage/' . $item->karyawan->foto) }}"
                                                class="rounded-circle me-2" width="32" height="32"
                                                alt="{{ $item->karyawan->nama_lengkap }}">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                                                style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif


                                        <div>
                                            <strong>{{ $item->karyawan->nama_lengkap }}</strong>
                                            <small class="d-block text-muted">{{ $item->karyawan->nip }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Jabatan --}}
                                <td>
                                    @if ($item->karyawan->jabatan)
                                        <span class="badge bg-info text-dark">
                                            {{ $item->karyawan->jabatan->kode_jabatan }}
                                        </span><br>
                                        <small class="text-muted">{{ $item->karyawan->jabatan->nama_jabatan }}</small>
                                    @else
                                        <em class="text-muted">-</em>
                                    @endif
                                </td>

                                {{-- Tanggal --}}
                                <td><strong>{{ \Carbon\Carbon::parse($item->tanggal_presensi)->format('d/m/Y') }}</strong>
                                </td>

                                {{-- Shift --}}
                                <td><span class="badge bg-dark badge-modern">{{ $item->shift->kode_shift ?? '-' }}</span>
                                </td>

                                {{-- Jam Masuk --}}
                                <td>
                                    @if ($item->jam_masuk)
                                        <strong>{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</strong>
                                        @if ($item->keterlambatan_menit > 0)
                                            <small class="d-block text-warning">+{{ $item->keterlambatan_menit }}m</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Jam Keluar --}}
                                <td>
                                    @if ($item->jam_keluar)
                                        <strong>{{ \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Total Jam --}}
                                <td>
                                    @if ($item->total_jam_kerja)
                                        <span class="badge bg-info badge-modern">
                                            {{ number_format($item->total_jam_kerja, 1) }} jam
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    <span class="badge bg-{{ $item->status_kehadiran_color }} badge-modern">
                                        {{ strtoupper($item->status_kehadiran) }}
                                    </span>
                                </td>

                                {{-- Verifikasi --}}
                                <td>
                                    <span
                                        class="badge bg-{{ $item->status_verifikasi == 'verified'
                                            ? 'success'
                                            : ($item->status_verifikasi == 'rejected'
                                                ? 'danger'
                                                : 'warning') }} badge-modern">
                                        {{ strtoupper($item->status_verifikasi) }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.presensi.show', $item->id_presensi) }}"
                                            class="btn btn-info btn-modern" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('presensi.edit', $item->id_presensi) }}"
                                            class="btn btn-warning btn-modern" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data presensi.</p>
                                    <a href="{{ route('presensi.create') }}" class="btn btn-primary-modern btn-modern">
                                        <i class="fas fa-plus me-2"></i>Tambah Presensi
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($presensi->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan {{ $presensi->firstItem() }} - {{ $presensi->lastItem() }} dari
                        {{ $presensi->total() }} data
                    </div>
                    {{ $presensi->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Statistik -->
    <div class="row mt-4">
        @foreach (['total' => 'Total', 'hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $key => $label)
            <div class="col-md-2">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats[$key] ?? 0 }}</div>
                    <div class="stats-label">{{ $label }}</div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(el) {
            return new bootstrap.Tooltip(el)
        })
    </script>
@endpush