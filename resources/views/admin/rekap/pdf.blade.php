<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Presensi {{ ucfirst($tipeRekap) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        
        .header h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 11px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-section table {
            width: 100%;
        }
        
        .info-section td {
            padding: 3px 0;
            font-size: 10px;
        }
        
        .info-section td:first-child {
            width: 150px;
            font-weight: bold;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table.data-table th,
        table.data-table td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: center;
            font-size: 9px;
        }
        
        table.data-table th {
            background-color: #4a5568;
            color: white;
            font-weight: bold;
        }
        
        table.data-table td:nth-child(2),
        table.data-table td:nth-child(3) {
            text-align: left;
        }
        
        table.data-table tbody tr:nth-child(even) {
            background-color: #f7fafc;
        }
        
        table.data-table tfoot {
            background-color: #e2e8f0;
            font-weight: bold;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            color: white;
        }
        
        .badge-success { background-color: #48bb78; }
        .badge-warning { background-color: #ed8936; }
        .badge-info { background-color: #4299e1; }
        .badge-secondary { background-color: #718096; }
        .badge-primary { background-color: #5a67d8; }
        .badge-danger { background-color: #f56565; }
        
        .footer {
            margin-top: 30px;
            font-size: 9px;
            text-align: right;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN REKAP PRESENSI KARYAWAN</h2>
        <p>Periode {{ ucfirst($tipeRekap) }}: {{ $periodeText }}</p>
    </div>
    
    <div class="info-section">
        <table>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ $tanggalCetak }}</td>
            </tr>
            <tr>
                <td>Tipe Rekap</td>
                <td>: {{ ucfirst($tipeRekap) }}</td>
            </tr>
            <tr>
                <td>Jumlah Karyawan</td>
                <td>: {{ count($rekapData) }} orang</td>
            </tr>
        </table>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">NIP</th>
                <th style="width: 15%;">Nama</th>
                <th style="width: 12%;">Fakultas</th>
                <th style="width: 12%;">Departemen</th>
                <th style="width: 5%;">HK</th>
                <th style="width: 5%;">H</th>
                <th style="width: 5%;">T</th>
                <th style="width: 5%;">I</th>
                <th style="width: 5%;">S</th>
                <th style="width: 5%;">C</th>
                <th style="width: 5%;">A</th>
                <th style="width: 8%;">% Hadir</th>
                <th style="width: 7%;">Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapData as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $data['karyawan']->nip }}</td>
                <td>{{ $data['karyawan']->nama_lengkap }}</td>
                <td>{{ $data['karyawan']->fakultas->kode_fakultas ?? '-' }}</td>
                <td>{{ $data['karyawan']->departemen->kode_departemen ?? '-' }}</td>
                <td>{{ $data['total_hari_kerja'] }}</td>
                <td><span class="badge badge-success">{{ $data['jumlah_hadir'] }}</span></td>
                <td>
                    <span class="badge badge-warning">{{ $data['jumlah_terlambat'] }}</span>
                    @if($data['jumlah_terlambat'] > 0)
                    <br><small style="font-size: 7px;">({{ $data['rata_rata_terlambat'] }}')</small>
                    @endif
                </td>
                <td><span class="badge badge-info">{{ $data['jumlah_izin'] }}</span></td>
                <td><span class="badge badge-secondary">{{ $data['jumlah_sakit'] }}</span></td>
                <td><span class="badge badge-primary">{{ $data['jumlah_cuti'] }}</span></td>
                <td><span class="badge badge-danger">{{ $data['jumlah_alpha'] }}</span></td>
                <td>
                    @if($data['persentase_kehadiran'] >= 90)
                        <span class="badge badge-success">{{ $data['persentase_kehadiran'] }}%</span>
                    @elseif($data['persentase_kehadiran'] >= 75)
                        <span class="badge badge-warning">{{ $data['persentase_kehadiran'] }}%</span>
                    @else
                        <span class="badge badge-danger">{{ $data['persentase_kehadiran'] }}%</span>
                    @endif
                </td>
                <td>{{ $data['total_jam_kerja'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;">TOTAL:</td>
                <td>{{ collect($rekapData)->sum('total_hari_kerja') }}</td>
                <td>{{ collect($rekapData)->sum('jumlah_hadir') }}</td>
                <td>{{ collect($rekapData)->sum('jumlah_terlambat') }}</td>
                <td>{{ collect($rekapData)->sum('jumlah_izin') }}</td>
                <td>{{ collect($rekapData)->sum('jumlah_sakit') }}</td>
                <td>{{ collect($rekapData)->sum('jumlah_cuti') }}</td>
                <td>{{ collect($rekapData)->sum('jumlah_alpha') }}</td>
                <td>-</td>
                <td>{{ round(collect($rekapData)->sum('total_jam_kerja'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p><strong>Keterangan:</strong></p>
        <p>HK = Hari Kerja | H = Hadir | T = Terlambat | I = Izin | S = Sakit | C = Cuti | A = Alpha</p>
        <p style="margin-top: 10px;">Dokumen ini dicetak secara otomatis oleh sistem</p>
    </div>
</body>
</html>