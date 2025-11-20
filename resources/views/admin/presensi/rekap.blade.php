@extends('layouts.app')

@section('title', 'Rekap Presensi Karyawan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Rekap Presensi Karyawan
                    </h4>
                </div>
                
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.presensi.rekap') }}" id="filterForm">
                        <div class="row">
                            <!-- Tipe Rekap -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tipe Rekap</label>
                                    <select name="tipe_rekap" class="form-control" id="tipeRekap" required>
                                        <option value="bulanan" {{ $tipeRekap === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                        <option value="mingguan" {{ $tipeRekap === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Periode -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Periode</label>
                                    <input type="month" name="periode" class="form-control" id="periodeBulanan" 
                                        value="{{ $periode }}" {{ $tipeRekap === 'bulanan' ? '' : 'style=display:none' }} required>
                                    <input type="week" name="periode" class="form-control" id="periodeMingguan" 
                                        value="{{ $periode }}" {{ $tipeRekap === 'mingguan' ? '' : 'style=display:none' }} required>
                                </div>
                            </div>
                            
                            <!-- Fakultas -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Fakultas (Opsional)</label>
                                    <select name="id_fakultas" class="form-control" id="fakultasSelect">
                                        <option value="">Semua Fakultas</option>
                                        @foreach($fakultas as $fak)
                                            <option value="{{ $fak->id_fakultas }}" {{ $idFakultas == $fak->id_fakultas ? 'selected' : '' }}>
                                                {{ $fak->nama_fakultas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Departemen -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Departemen (Opsional)</label>
                                    <select name="id_departemen" class="form-control" id="departemenSelect">
                                        <option value="">Semua Departemen</option>
                                        @foreach($departemen as $dept)
                                            <option value="{{ $dept->id_departemen }}" 
                                                data-fakultas="{{ $dept->id_fakultas }}"
                                                {{ $idDepartemen == $dept->id_departemen ? 'selected' : '' }}>
                                                {{ $dept->nama_departemen }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Tampilkan Rekap
                                </button>
                                @if($rekapData && count($rekapData) > 0)
                                <button type="button" class="btn btn-success" id="downloadPdfBtn">
                                    <i class="fas fa-file-pdf"></i> Download PDF
                                </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            @if($rekapData && count($rekapData) > 0)
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Hasil Rekap Presensi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIP</th>
                                    <th>Nama Karyawan</th>
                                    <th>Fakultas</th>
                                    <th>Departemen</th>
                                    <th>Hari Kerja</th>
                                    <th>Hadir</th>
                                    <th>Terlambat</th>
                                    <th>Izin</th>
                                    <th>Sakit</th>
                                    <th>Cuti</th>
                                    <th>Alpha</th>
                                    <th>% Kehadiran</th>
                                    <th>Total Jam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $data['karyawan']->nip }}</td>
                                    <td>{{ $data['karyawan']->nama_lengkap }}</td>
                                    <td>{{ $data['karyawan']->fakultas->nama_fakultas }}</td>
                                    <td>{{ $data['karyawan']->departemen->nama_departemen }}</td>
                                    <td class="text-center">{{ $data['total_hari_kerja'] }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $data['jumlah_hadir'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">{{ $data['jumlah_terlambat'] }}</span>
                                        @if($data['jumlah_terlambat'] > 0)
                                        <br><small class="text-muted">({{ $data['rata_rata_terlambat'] }} mnt/hari)</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $data['jumlah_izin'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">{{ $data['jumlah_sakit'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $data['jumlah_cuti'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-danger">{{ $data['jumlah_alpha'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($data['persentase_kehadiran'] >= 90)
                                            <span class="badge badge-success">{{ $data['persentase_kehadiran'] }}%</span>
                                        @elseif($data['persentase_kehadiran'] >= 75)
                                            <span class="badge badge-warning">{{ $data['persentase_kehadiran'] }}%</span>
                                        @else
                                            <span class="badge badge-danger">{{ $data['persentase_kehadiran'] }}%</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $data['total_jam_kerja'] }} jam</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-light">
                                <tr>
                                    <th colspan="5" class="text-right">TOTAL:</th>
                                    <th class="text-center">{{ collect($rekapData)->sum('total_hari_kerja') }}</th>
                                    <th class="text-center">{{ collect($rekapData)->sum('jumlah_hadir') }}</th>
                                    <th class="text-center">{{ collect($rekapData)->sum('jumlah_terlambat') }}</th>
                                    <th class="text-center">{{ collect($rekapData)->sum('jumlah_izin') }}</th>
                                    <th class="text-center">{{ collect($rekapData)->sum('jumlah_sakit') }}</th>
                                    <th class="text-center">{{ collect($rekapData)->sum('jumlah_cuti') }}</th>
                                    <th class="text-center">{{ collect($rekapData)->sum('jumlah_alpha') }}</th>
                                    <th class="text-center">-</th>
                                    <th class="text-center">{{ round(collect($rekapData)->sum('total_jam_kerja'), 2) }} jam</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card mt-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada data presensi untuk periode yang dipilih</h5>
                    <p class="text-muted">Silakan pilih periode lain atau ubah filter</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle periode input based on tipe rekap
    $('#tipeRekap').on('change', function() {
        const tipe = $(this).val();
        if (tipe === 'bulanan') {
            $('#periodeBulanan').show().prop('required', true);
            $('#periodeMingguan').hide().prop('required', false);
        } else {
            $('#periodeBulanan').hide().prop('required', false);
            $('#periodeMingguan').show().prop('required', true);
        }
    });
    
    // Filter departemen by fakultas
    $('#fakultasSelect').on('change', function() {
        const idFakultas = $(this).val();
        const $departemenSelect = $('#departemenSelect');
        
        if (idFakultas) {
            $departemenSelect.find('option').each(function() {
                const $option = $(this);
                if ($option.val() === '') {
                    $option.show();
                } else if ($option.data('fakultas') == idFakultas) {
                    $option.show();
                } else {
                    $option.hide();
                }
            });
            $departemenSelect.val('');
        } else {
            $departemenSelect.find('option').show();
        }
    });
    
    // Trigger on page load
    $('#fakultasSelect').trigger('change');
    
    // Download PDF
    $('#downloadPdfBtn').on('click', function() {
        const formData = $('#filterForm').serialize();
        window.open('{{ route("admin.presensi.download-pdf") }}?' + formData, '_blank');
    });
});
</script>
@endpush