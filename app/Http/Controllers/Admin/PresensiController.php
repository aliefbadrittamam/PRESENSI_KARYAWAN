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


class PresensiController extends Controller
{
    /**
     * Display rekap presensi page (ADMIN)
     */
    public function index(Request $request)
    {
        $fakultas = Fakultas::where('status_aktif', 1)->get();
        $departemen = Departemen::where('status_aktif', 1)->get();
        
        // Default values
        $tipeRekap = $request->input('tipe_rekap', 'bulanan');
        $periode = $request->input('periode');
        
        // Set default periode jika kosong
        if (empty($periode)) {
            $periode = Carbon::now()->format('Y-m');
        }
        
        $idFakultas = $request->input('id_fakultas');
        $idDepartemen = $request->input('id_departemen');
        
        // Query builder
        $query = Karyawan::with(['fakultas', 'departemen', 'jabatan'])
            ->where('status_aktif', 1);
        
        if ($idFakultas) {
            $query->where('id_fakultas', $idFakultas);
        }
        
        if ($idDepartemen) {
            $query->where('id_departemen', $idDepartemen);
        }
        
        $karyawanList = $query->get();
        
        // Get rekap data if filters applied (dan periode tidak kosong)
        $rekapData = null;
        if ($request->has('periode') && !empty($periode)) {
            $rekapData = $this->generateRekap($karyawanList, $tipeRekap, $periode);
        }
        
        // Generate months for dropdown (untuk kompatibilitas view)
        $months = $this->generateMonthOptions($periode);
        
        return view('presensi.rekap', compact(
            'fakultas',
            'departemen',
            'tipeRekap',
            'periode',
            'idFakultas',
            'idDepartemen',
            'karyawanList',
            'rekapData',
            'months' // TAMBAHKAN INI
        ));
    }
    
    /**
     * Generate month options (untuk dropdown)
     */
    private function generateMonthOptions($selectedMonth)
    {
        $months = [];
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $value = $date->format('Y-m');
            $label = $monthNames[$date->month] . ' ' . $date->year;
            
            $months[] = [
                'value' => $value,
                'label' => $label,
                'selected' => $value === $selectedMonth
            ];
        }

        return $months;
    }
    
    /**
     * Generate rekap data
     */
    private function generateRekap($karyawanList, $tipeRekap, $periode)
    {
        $rekapData = [];
        
        foreach ($karyawanList as $karyawan) {
            if ($tipeRekap === 'bulanan') {
                $data = $this->getRekapBulanan($karyawan, $periode);
            } else {
                $data = $this->getRekapMingguan($karyawan, $periode);
            }
            
            $rekapData[] = $data;
        }
        
        return $rekapData;
    }
    
    /**
     * Get rekap bulanan
     */
    private function getRekapBulanan($karyawan, $periode)
    {
        // Validasi periode
        if (empty($periode) || strpos($periode, '-') === false) {
            $periode = Carbon::now()->format('Y-m');
        }
        
        list($tahun, $bulan) = explode('-', $periode);
        
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        return $this->calculateRekap($karyawan, $startDate, $endDate, $periode);
    }
    
    /**
     * Get rekap mingguan
     */
    private function getRekapMingguan($karyawan, $periode)
    {
        // Validasi periode untuk format mingguan (Y-W01)
        if (empty($periode) || strpos($periode, '-W') === false) {
            $periode = Carbon::now()->format('Y') . '-W' . Carbon::now()->format('W');
        }
        
        // Format periode: Y-W (contoh: 2025-W01)
        $year = substr($periode, 0, 4);
        $week = (int) substr($periode, 6);
        
        $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $endDate = Carbon::now()->setISODate($year, $week)->endOfWeek();
        
        return $this->calculateRekap($karyawan, $startDate, $endDate, $periode);
    }
    
    /**
     * Calculate rekap statistics
     */
    private function calculateRekap($karyawan, $startDate, $endDate, $periode)
    {
        $presensiList = Presensi::where('id_karyawan', $karyawan->id_karyawan)
            ->whereBetween('tanggal_presensi', [$startDate, $endDate])
            ->get();
        
        // Count working days (exclude weekends)
        $totalHariKerja = 0;
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            if (!$currentDate->isWeekend()) {
                $totalHariKerja++;
            }
            $currentDate->addDay();
        }
        
        // Calculate statistics
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
        
        $persentaseTerlambat = $totalHariKerja > 0 
            ? ($jumlahTerlambat / $totalHariKerja) * 100 
            : 0;
        
        $persentaseTidakHadir = $totalHariKerja > 0 
            ? (($jumlahIzin + $jumlahSakit + $jumlahCuti + $jumlahAlpha) / $totalHariKerja) * 100 
            : 0;
        
        $rataRataTerlambat = $jumlahTerlambat > 0 
            ? $totalMenitTerlambat / $jumlahTerlambat 
            : 0;
        
        return [
            'karyawan' => $karyawan,
            'periode' => $periode,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_hari_kerja' => $totalHariKerja,
            'jumlah_hadir' => $jumlahHadir,
            'jumlah_terlambat' => $jumlahTerlambat,
            'jumlah_izin' => $jumlahIzin,
            'jumlah_sakit' => $jumlahSakit,
            'jumlah_cuti' => $jumlahCuti,
            'jumlah_alpha' => max(0, $jumlahAlpha),
            'persentase_kehadiran' => round($persentaseKehadiran, 2),
            'persentase_terlambat' => round($persentaseTerlambat, 2),
            'persentase_tidak_hadir' => round($persentaseTidakHadir, 2),
            'total_menit_terlambat' => $totalMenitTerlambat,
            'rata_rata_terlambat' => round($rataRataTerlambat, 2),
            'total_jam_kerja' => round($totalJamKerja, 2),
        ];
    }
    
    /**
     * Method rekap() untuk route /presensi/rekap
     * Alias dari index() untuk backward compatibility
     */
    public function rekap(Request $request)
    {
        return $this->index($request);
    }
    
    /**
     * Download PDF
     */
    public function downloadPdf(Request $request)
    {
        $tipeRekap = $request->input('tipe_rekap', 'bulanan');
        $periode = $request->input('periode');
        
        // Set default periode jika kosong
        if (empty($periode)) {
            $periode = Carbon::now()->format('Y-m');
        }
        
        $idFakultas = $request->input('id_fakultas');
        $idDepartemen = $request->input('id_departemen');
        
        // Query builder
        $query = Karyawan::with(['fakultas', 'departemen', 'jabatan'])
            ->where('status_aktif', 1);
        
        if ($idFakultas) {
            $query->where('id_fakultas', $idFakultas);
        }
        
        if ($idDepartemen) {
            $query->where('id_departemen', $idDepartemen);
        }
        
        $karyawanList = $query->get();
        $rekapData = $this->generateRekap($karyawanList, $tipeRekap, $periode);
        
        // Format periode untuk judul
        if ($tipeRekap === 'bulanan') {
            // Validasi format periode
            if (strpos($periode, '-') === false) {
                $periode = Carbon::now()->format('Y-m');
            }
            
            list($tahun, $bulan) = explode('-', $periode);
            $bulanNama = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $periodeText = ($bulanNama[$bulan] ?? 'Unknown') . ' ' . $tahun;
        } else {
            $year = substr($periode, 0, 4);
            $week = substr($periode, 6);
            $periodeText = "Minggu ke-$week Tahun $year";
        }
        
        $data = [
            'rekapData' => $rekapData,
            'tipeRekap' => $tipeRekap,
            'periodeText' => $periodeText,
            'tanggalCetak' => Carbon::now()->format('d/m/Y H:i:s'),
        ];
        
        $pdf = Pdf::loadView('admin.rekap.pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'Rekap_Presensi_' . ($tipeRekap === 'bulanan' ? 'Bulanan' : 'Mingguan') . '_' . str_replace('-', '_', $periode) . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Get departemen by fakultas (AJAX)
     */
    public function getDepartemenByFakultas($idFakultas)
    {
        $departemen = Departemen::where('id_fakultas', $idFakultas)
            ->where('status_aktif', 1)
            ->get();
        
        return response()->json($departemen);
    }
}