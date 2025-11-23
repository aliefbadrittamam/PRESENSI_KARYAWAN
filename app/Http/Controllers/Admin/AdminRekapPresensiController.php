<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Fakultas;
use App\Models\Departemen;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminRekapPresensiController extends Controller
{
    /**
     * Display rekap presensi page
     */
    public function index(Request $request)
    {
        // Get filter parameters dengan default value
        $tipeRekap = $request->input('tipe_rekap', 'bulanan');
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $idFakultas = $request->input('id_fakultas', '');
        $idDepartemen = $request->input('id_departemen', '');

        // Get fakultas dan departemen untuk dropdown
        $fakultas = Fakultas::where('status_aktif', 1)->get();
        $departemen = Departemen::where('status_aktif', 1)->get();

        // Generate rekap data - SELALU generate data, tidak perlu cek apakah ada parameter
        $rekapData = $this->generateRekapData($tipeRekap, $periode, $idFakultas, $idDepartemen);

        return view('admin.presensi.rekap', compact(
            'fakultas',
            'departemen',
            'rekapData',
            'tipeRekap',
            'periode',
            'idFakultas',
            'idDepartemen'
        ));
    }

    /**
     * Generate rekap data
     */
    private function generateRekapData($tipeRekap, $periode, $idFakultas = null, $idDepartemen = null)
    {
        $rekapData = [];

        // Validasi dan set default periode jika kosong atau invalid
        if (empty($periode)) {
            if ($tipeRekap === 'bulanan') {
                $periode = Carbon::now()->format('Y-m');
            } else {
                $periode = Carbon::now()->format('Y-\WW');
            }
        }

        // Parse periode
        if ($tipeRekap === 'bulanan') {
            $periodeParts = explode('-', $periode);
            
            // Validasi format periode bulanan
            if (count($periodeParts) < 2) {
                // Jika format tidak valid, gunakan bulan sekarang
                $periode = Carbon::now()->format('Y-m');
                $periodeParts = explode('-', $periode);
            }
            
            $tahun = $periodeParts[0];
            $bulan = $periodeParts[1];
            
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        } else {
            // Mingguan format: 2025-W45
            $weekData = explode('-W', $periode);
            
            // Validasi format periode mingguan
            if (count($weekData) < 2) {
                // Jika format tidak valid, gunakan minggu sekarang
                $periode = Carbon::now()->format('Y-\WW');
                $weekData = explode('-W', $periode);
            }
            
            $tahun = $weekData[0];
            $minggu = $weekData[1];
            
            try {
                $startDate = Carbon::now()->setISODate($tahun, $minggu)->startOfWeek();
                $endDate = Carbon::now()->setISODate($tahun, $minggu)->endOfWeek();
            } catch (\Exception $e) {
                // Jika ada error, gunakan minggu sekarang
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
            }
        }

        // Build query karyawan
        $karyawanQuery = Karyawan::with(['fakultas', 'departemen', 'jabatan'])
            ->where('status_aktif', 1);

        if ($idFakultas) {
            $karyawanQuery->where('id_fakultas', $idFakultas);
        }

        if ($idDepartemen) {
            $karyawanQuery->where('id_departemen', $idDepartemen);
        }

        $karyawanList = $karyawanQuery->get();

        // Calculate working days
        $totalHariKerja = 0;
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            if (!$currentDate->isWeekend()) {
                $totalHariKerja++;
            }
            $currentDate->addDay();
        }

        // Generate rekap untuk setiap karyawan
        foreach ($karyawanList as $karyawan) {
            $presensiList = Presensi::where('id_karyawan', $karyawan->id_karyawan)
                ->whereBetween('tanggal_presensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();

            $jumlahHadir = $presensiList->where('status_kehadiran', 'hadir')->count();
            $jumlahTerlambat = $presensiList->where('status_kehadiran', 'terlambat')->count();
            $jumlahIzin = $presensiList->where('status_kehadiran', 'izin')->count();
            $jumlahSakit = $presensiList->where('status_kehadiran', 'sakit')->count();
            $jumlahCuti = $presensiList->where('status_kehadiran', 'cuti')->count();
            $jumlahAlpha = $totalHariKerja - ($jumlahHadir + $jumlahTerlambat + $jumlahIzin + $jumlahSakit + $jumlahCuti);

            $totalMenitTerlambat = $presensiList->sum('keterlambatan_menit');
            $totalJamKerja = $presensiList->sum('total_jam_kerja');

            $persentaseKehadiran = $totalHariKerja > 0 
                ? (($jumlahHadir + $jumlahTerlambat) / $totalHariKerja) * 100 
                : 0;

            $rekapData[] = [
                'karyawan' => $karyawan,
                'total_hari_kerja' => $totalHariKerja,
                'jumlah_hadir' => $jumlahHadir,
                'jumlah_terlambat' => $jumlahTerlambat,
                'jumlah_izin' => $jumlahIzin,
                'jumlah_sakit' => $jumlahSakit,
                'jumlah_cuti' => $jumlahCuti,
                'jumlah_alpha' => max(0, $jumlahAlpha),
                'persentase_kehadiran' => round($persentaseKehadiran, 2),
                'total_menit_terlambat' => $totalMenitTerlambat,
                'rata_rata_terlambat' => $jumlahTerlambat > 0 
                    ? round($totalMenitTerlambat / $jumlahTerlambat, 2) 
                    : 0,
                'total_jam_kerja' => round($totalJamKerja, 2),
            ];
        }

        return $rekapData;
    }

    /**
     * Download PDF
     */
    public function downloadPdf(Request $request)
    {
        $tipeRekap = $request->input('tipe_rekap', 'bulanan');
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $idFakultas = $request->input('id_fakultas', '');
        $idDepartemen = $request->input('id_departemen', '');

        // Validasi dan set default periode jika kosong
        if (empty($periode)) {
            if ($tipeRekap === 'bulanan') {
                $periode = Carbon::now()->format('Y-m');
            } else {
                $periode = Carbon::now()->format('Y-\WW');
            }
        }

        $rekapData = $this->generateRekapData($tipeRekap, $periode, $idFakultas, $idDepartemen);

        // Get fakultas dan departemen info
        $fakultasNama = $idFakultas 
            ? Fakultas::find($idFakultas)->nama_fakultas 
            : 'Semua Fakultas';
        
        $departemenNama = $idDepartemen 
            ? Departemen::find($idDepartemen)->nama_departemen 
            : 'Semua Departemen';

        // Format periode untuk judul
        if ($tipeRekap === 'bulanan') {
            $periodeParts = explode('-', $periode);
            if (count($periodeParts) >= 2) {
                $tahun = $periodeParts[0];
                $bulan = $periodeParts[1];
                $bulanNama = [
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                ];
                $periodeText = ($bulanNama[$bulan] ?? 'Bulan ' . $bulan) . ' ' . $tahun;
            } else {
                $periodeText = 'Periode Tidak Valid';
            }
        } else {
            $weekData = explode('-W', $periode);
            if (count($weekData) >= 2) {
                $periodeText = 'Minggu ke-' . $weekData[1] . ' Tahun ' . $weekData[0];
            } else {
                $periodeText = 'Periode Tidak Valid';
            }
        }

        // Load PDF dengan setting landscape dan ukuran Folio (8.5 x 13 inches)
        $pdf = Pdf::loadView('admin.presensi.rekap-pdf', compact(
            'rekapData',
            'tipeRekap',
            'periodeText',
            'fakultasNama',
            'departemenNama'
        ))
        ->setPaper('folio', 'landscape'); // Folio landscape
        // Alternatif ukuran:
        // ->setPaper('f4', 'landscape'); // F4 landscape (215 x 330 mm)
        // ->setPaper([0, 0, 935.43, 612.28], 'landscape'); // Custom Folio exact size

        $filename = 'Rekap_Presensi_' . str_replace(' ', '_', $periodeText) . '.pdf';
        
        // Stream untuk preview di tab baru (bukan download langsung)
        return $pdf->stream($filename);
    }
}