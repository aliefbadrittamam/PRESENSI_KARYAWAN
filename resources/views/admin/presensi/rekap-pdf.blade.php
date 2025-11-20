<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Presensi Karyawan</title>
    <style>
        @page {
            margin: 15mm 10mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: bold;
        }
        
        .header h3 {
            margin: 3px 0;
            font-size: 13px;
            color: #555;
        }
        
        .info {
            margin-bottom: 12px;
        }
        
        .info-table {
            width: 100%;
            border: none;
        }
        
        .info-table td {
            border: none;
            padding: 3px 0;
            font-size: 9px;
        }
        
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        
        table, th, td {
            border: 1px solid #333;
        }
        
        th {
            background-color: #2c3e50;
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
        }
        
        td {
            padding: 5px 4px;
            text-align: center;
            font-size: 8px;
        }
        
        .text-left {
            text-align: left;
            padding-left: 6px;
        }
        
        .text-right {
            text-align: right;
            padding-right: 6px;
        }
        
        /* Badge Styling */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7px;
        }
        
        .badge-success { 
            background-color: #27ae60; 
            color: white; 
        }
        
        .badge-warning { 
            background-color: #f39c12; 
            color: white; 
        }
        
        .badge-danger { 
            background-color: #e74c3c; 
            color: white; 
        }
        
        .badge-info { 
            background-color: #3498db; 
            color: white; 
        }
        
        .badge-secondary { 
            background-color: #95a5a6; 
            color: white; 
        }
        
        .badge-primary { 
            background-color: #2980b9; 
            color: white; 
        }
        
        tfoot {
            background-color: #ecf0f1;
            font-weight: bold;
        }
        
        tfoot td {
            font-weight: bold;
            background-color: #ecf0f1;
        }
        
        .small-text {
            font-size: 7px;
            color: #666;
        }
        
        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAP PRESENSI KARYAWAN</h2>
        <h3>{{ strtoupper($tipeRekap) }} - {{ $periodeText }}</h3>
    </div>

    <div class="info">
        <table class="info-table">
            <tr>
                <td class="info-label">Fakultas</td>
                <td>: {{ $fakultasNama }}</td>
                <td class="info-label" style="width: 120px;">Tanggal Cetak</td>
                <td>: {{ date('d/m/Y H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="info-label">Departemen</td>
                <td>: {{ $departemenNama }}</td>
                <td class="info-label">Total Karyawan</td>
                <td>: {{ count($rekapData) }} orang</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 2%;">No</th>
                <th style="width: 6%;">NIP</th>
                <th style="width: 12%;">Nama Karyawan</th>
                <th style="width: 10%;">Fakultas</th>
                <th style="width: 10%;">Departemen</th>
                <th style="width: 4%;">H.<br>Kerja</th>
                <th style="width: 4%;">Hadir</th>
                <th style="width: 6%;">Terlambat</th>
                <th style="width: 4%;">Izin</th>
                <th style="width: 4%;">Sakit</th>
                <th style="width: 4%;">Cuti</th>
                <th style="width: 4%;">Alpha</th>
                <th style="width: 6%;">%<br>Hadir</th>
                <th style="width: 6%;">Total<br>Jam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $data['karyawan']->nip }}</td>
                <td class="text-left">{{ $data['karyawan']->nama_lengkap }}</td>
                <td class="text-left">{{ $data['karyawan']->fakultas->nama_fakultas }}</td>
                <td class="text-left">{{ $data['karyawan']->departemen->nama_departemen }}</td>
                <td>{{ $data['total_hari_kerja'] }}</td>
                <td>
                    <span class="badge badge-success">{{ $data['jumlah_hadir'] }}</span>
                </td>
                <td>
                    <span class="badge badge-warning">{{ $data['jumlah_terlambat'] }}</span>
                    @if($data['jumlah_terlambat'] > 0)
                    <br><span class="small-text">({{ $data['rata_rata_terlambat'] }}m)</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-info">{{ $data['jumlah_izin'] }}</span>
                </td>
                <td>
                    <span class="badge badge-secondary">{{ $data['jumlah_sakit'] }}</span>
                </td>
                <td>
                    <span class="badge badge-primary">{{ $data['jumlah_cuti'] }}</span>
                </td>
                <td>
                    <span class="badge badge-danger">{{ $data['jumlah_alpha'] }}</span>
                </td>
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
            @empty
            <tr>
                <td colspan="14" style="padding: 20px;">
                    <strong>Tidak ada data karyawan untuk periode ini</strong>
                </td>
            </tr>
            @endforelse
        </tbody>
        @if(count($rekapData) > 0)
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>TOTAL KESELURUHAN:</strong></td>
                <td><strong>{{ collect($rekapData)->sum('total_hari_kerja') }}</strong></td>
                <td><strong>{{ collect($rekapData)->sum('jumlah_hadir') }}</strong></td>
                <td><strong>{{ collect($rekapData)->sum('jumlah_terlambat') }}</strong></td>
                <td><strong>{{ collect($rekapData)->sum('jumlah_izin') }}</strong></td>
                <td><strong>{{ collect($rekapData)->sum('jumlah_sakit') }}</strong></td>
                <td><strong>{{ collect($rekapData)->sum('jumlah_cuti') }}</strong></td>
                <td><strong>{{ collect($rekapData)->sum('jumlah_alpha') }}</strong></td>
                <td>-</td>
                <td><strong>{{ round(collect($rekapData)->sum('total_jam_kerja'), 2) }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>
    
    <div class="footer">
        <p>Dicetak dari Sistem Presensi Karyawan - {{ config('app.name', 'Laravel') }}</p>
    </div>
</body>
</html>