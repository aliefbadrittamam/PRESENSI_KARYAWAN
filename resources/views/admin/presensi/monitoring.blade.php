@extends('layouts.app')

@section('title', 'Monitoring Presensi')
@section('icon', 'fa-desktop')

@section('content')
    <div class="row">
        <!-- Filter Card -->
        <div class="col-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter me-2"></i>Filter Presensi
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.presensi.monitoring') }}" method="GET" id="filterForm">
                        <div class="row">
                            <!-- Tanggal Dari -->
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Tanggal Dari</label>
                                    <input type="date" name="tanggal_dari" class="form-control form-control-sm"
                                        value="{{ request('tanggal_dari') }}">
                                </div>
                            </div>

                            <!-- Tanggal Sampai -->
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Tanggal Sampai</label>
                                    <input type="date" name="tanggal_sampai" class="form-control form-control-sm"
                                        value="{{ request('tanggal_sampai') }}">
                                </div>
                            </div>

                            <!-- Search Nama/NIP -->
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Cari Nama/NIP</label>
                                    <input type="text" name="search" class="form-control form-control-sm"
                                        placeholder="Nama atau NIP" value="{{ request('search') }}">
                                </div>
                            </div>

                            <!-- Karyawan -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Karyawan</label>
                                    <select name="karyawan_id" class="form-control form-control-sm">
                                        <option value="">Semua Karyawan</option>
                                        @foreach ($karyawan as $k)
                                            <option value="{{ $k->id_karyawan }}"
                                                {{ request('karyawan_id') == $k->id_karyawan ? 'selected' : '' }}>
                                                {{ $k->nama_lengkap }} ({{ $k->nip }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Fakultas -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Fakultas</label>
                                    <select name="fakultas_id" class="form-control form-control-sm">
                                        <option value="">Semua Fakultas</option>
                                        @foreach ($fakultas as $f)
                                            <option value="{{ $f->id_fakultas }}"
                                                {{ request('fakultas_id') == $f->id_fakultas ? 'selected' : '' }}>
                                                {{ $f->nama_fakultas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Departemen -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Departemen</label>
                                    <select name="departemen_id" class="form-control form-control-sm">
                                        <option value="">Semua Departemen</option>
                                        @foreach ($departemen as $d)
                                            <option value="{{ $d->id_departemen }}"
                                                {{ request('departemen_id') == $d->id_departemen ? 'selected' : '' }}>
                                                {{ $d->nama_departemen }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Shift -->
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Shift</label>
                                    <select name="shift_id" class="form-control form-control-sm">
                                        <option value="">Semua Shift</option>
                                        @foreach ($shift as $s)
                                            <option value="{{ $s->id_shift }}"
                                                {{ request('shift_id') == $s->id_shift ? 'selected' : '' }}>
                                                {{ $s->nama_shift }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status Kehadiran -->
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Status Kehadiran</label>
                                    <select name="status_kehadiran" class="form-control form-control-sm">
                                        <option value="">Semua Status</option>
                                        <option value="hadir"
                                            {{ request('status_kehadiran') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                        <option value="terlambat"
                                            {{ request('status_kehadiran') == 'terlambat' ? 'selected' : '' }}>Terlambat
                                        </option>
                                        <option value="izin"
                                            {{ request('status_kehadiran') == 'izin' ? 'selected' : '' }}>Izin</option>
                                        <option value="sakit"
                                            {{ request('status_kehadiran') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                        <option value="cuti"
                                            {{ request('status_kehadiran') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                        <option value="alpha"
                                            {{ request('status_kehadiran') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Status Verifikasi -->
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Status Verifikasi</label>
                                    <select name="status_verifikasi" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        <option value="pending"
                                            {{ request('status_verifikasi') == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="verified"
                                            {{ request('status_verifikasi') == 'verified' ? 'selected' : '' }}>Verified
                                        </option>
                                        <option value="rejected"
                                            {{ request('status_verifikasi') == 'rejected' ? 'selected' : '' }}>Rejected
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small mb-1 d-block">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-sm mr-1">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.presensi.monitoring') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="col-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list me-2"></i>Data Presensi ({{ $presensi->total() }} records)
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Tanggal</th>
                                    <th>Karyawan</th>
                                    <th>Departemen</th>
                                    <th>Shift</th>
                                    <th class="text-center">Jam Masuk</th>
                                    <th class="text-center">Jam Keluar</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Foto</th>
                                    <th class="text-center" width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presensi as $item)
                                    <tr>
                                        <td class="text-white-50">{{ $loop->iteration }}</td>
                                        <td class="text-white">
                                            {{ \Carbon\Carbon::parse($item->tanggal_presensi)->format('d M Y') }}</td>
                                        <td class="text-white">
                                            @if ($item->karyawan)
                                                <strong>{{ $item->karyawan->nama_lengkap }}</strong>
                                                <br><small class="text-muted">{{ $item->karyawan->nip }}</small>
                                            @else
                                                <span class="text-danger"><i>Karyawan tidak ditemukan</i></span>
                                            @endif
                                        </td>
                                        <td class="text-white-50">
                                            {{ $item->karyawan->departemen->nama_departemen ?? '-' }}</td>
                                        <td class="text-white-50">{{ $item->shift->nama_shift ?? '-' }}</td>
                                        <td class="text-center text-white-50">
                                            {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}
                                            @if ($item->keterlambatan_menit > 0)
                                                <br><small
                                                    class="badge bg-warning text-dark">+{{ $item->keterlambatan_menit }}m</small>
                                            @endif
                                        </td>
                                        <td class="text-center text-white-50">
                                            {{ $item->jam_keluar ? \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if ($item->status_kehadiran == 'hadir')
                                                <span class="badge bg-success">Hadir</span>
                                            @elseif($item->status_kehadiran == 'terlambat')
                                                <span class="badge bg-warning text-dark">Terlambat</span>
                                            @elseif($item->status_kehadiran == 'izin')
                                                <span class="badge bg-info">Izin</span>
                                            @elseif($item->status_kehadiran == 'sakit')
                                                <span class="badge bg-primary">Sakit</span>
                                            @elseif($item->status_kehadiran == 'cuti')
                                                <span class="badge bg-secondary">Cuti</span>
                                            @else
                                                <span class="badge bg-danger">Alpha</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->foto_masuk || $item->foto_keluar)
                                                <button class="btn btn-info btn-sm"
                                                    onclick="showPhotos({{ json_encode($item) }})">
                                                    <i class="fas fa-images"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.presensi.monitoring.show', $item->id_presensi) }}"
                                                class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-white-50 py-4">
                                            <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                            Tidak ada data presensi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($presensi->hasPages())
                    <div class="card-footer clearfix">
                        <div class="float-left">
                            <small class="text-muted">
                                Menampilkan {{ $presensi->firstItem() }} - {{ $presensi->lastItem() }} dari
                                {{ $presensi->total() }} data
                            </small>
                        </div>
                        <div class="float-right">
                            {{ $presensi->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Photo Viewer -->
    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-images me-2"></i>Foto Presensi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6" id="fotoMasukContainer">
                            <h6 class="text-center mb-2">Foto Masuk</h6>
                            <img id="fotoMasuk" class="img-fluid rounded border" alt="Foto Masuk">
                            <p class="text-center mt-2 small text-muted" id="waktuMasuk"></p>
                        </div>
                        <div class="col-md-6" id="fotoKeluarContainer">
                            <h6 class="text-center mb-2">Foto Keluar</h6>
                            <img id="fotoKeluar" class="img-fluid rounded border" alt="Foto Keluar">
                            <p class="text-center mt-2 small text-muted" id="waktuKeluar"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Fade in effect
            $('.card').hide().fadeIn(400);
        });

        function showPhotos(item) {
            // Set foto masuk
            if (item.foto_masuk) {
                document.getElementById('fotoMasuk').src = '/public/' + item.foto_masuk;
                document.getElementById('waktuMasuk').textContent = item.jam_masuk || '-';
                document.getElementById('fotoMasukContainer').style.display = 'block';
            } else {
                document.getElementById('fotoMasukContainer').style.display = 'none';
            }

            // Set foto keluar
            if (item.foto_keluar) {
                document.getElementById('fotoKeluar').src = '/public/' + item.foto_keluar;
                document.getElementById('waktuKeluar').textContent = item.jam_keluar || '-';
                document.getElementById('fotoKeluarContainer').style.display = 'block';
            } else {
                document.getElementById('fotoKeluarContainer').style.display = 'none';
            }

            $('#photoModal').modal('show');
        }
    </script>
@endpush
