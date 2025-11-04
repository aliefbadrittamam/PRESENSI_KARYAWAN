<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LokasiPresensi;
use App\Models\Fakultas;
use Illuminate\Support\Facades\DB;

class LokasiPresensiController extends Controller
{
    /**
     * Display a listing of lokasi presensi
     */
    public function index()
    {
        $lokasiList = LokasiPresensi::with('fakultas')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.presensi.lokasi_presensi.index', compact('lokasiList'));
    }

    /**
     * Show the form for creating a new lokasi
     */
    public function create()
    {
        $fakultas = Fakultas::where('status_aktif', 1)->get();
        return view('admin.presensi.lokasi_presensi.create', compact('fakultas'));
    }

    /**
     * Store a newly created lokasi
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:10|max:10000',
            'jenis_lokasi' => 'required|in:kantor,gedung,laboratorium,lainnya',
            'id_fakultas' => 'nullable|exists:fakultas,id_fakultas',
            'status_aktif' => 'required|boolean',
            'waktu_operasional_mulai' => 'nullable|date_format:H:i',
            'waktu_operasional_selesai' => 'nullable|date_format:H:i',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            LokasiPresensi::create($validated);

            return redirect()
                ->route('admin.lokasi-presensi.index')
                ->with('success', 'Lokasi presensi berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified lokasi
     */
    public function show($id)
    {
        $lokasi = LokasiPresensi::with('fakultas')->findOrFail($id);
        return view('admin.presensi.lokasi_presensi.show', compact('lokasi'));
    }

    /**
     * Show the form for editing the specified lokasi
     */
    public function edit($id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);
        $fakultas = Fakultas::where('status_aktif', 1)->get();
        
        return view('admin.presensi.lokasi_presensi.edit', compact('lokasi', 'fakultas'));
    }

    /**
     * Update the specified lokasi
     */
    public function update(Request $request, $id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);

        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:10|max:10000',
            'jenis_lokasi' => 'required|in:kantor,gedung,laboratorium,lainnya',
            'id_fakultas' => 'nullable|exists:fakultas,id_fakultas',
            'status_aktif' => 'required|boolean',
            'waktu_operasional_mulai' => 'nullable|date_format:H:i',
            'waktu_operasional_selesai' => 'nullable|date_format:H:i',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $lokasi->update($validated);

            return redirect()
                ->route('admin.lokasi-presensi.index')
                ->with('success', 'Lokasi presensi berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified lokasi
     */
    public function destroy($id)
    {
        try {
            $lokasi = LokasiPresensi::findOrFail($id);
            
            // Check if lokasi is being used
            $presensiCount = DB::table('presensi')
                ->where(function($query) use ($lokasi) {
                    $query->where('latitude_masuk', $lokasi->latitude)
                          ->where('longitude_masuk', $lokasi->longitude);
                })
                ->orWhere(function($query) use ($lokasi) {
                    $query->where('latitude_keluar', $lokasi->latitude)
                          ->where('longitude_keluar', $lokasi->longitude);
                })
                ->count();

            if ($presensiCount > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Lokasi tidak dapat dihapus karena sudah digunakan dalam presensi!');
            }

            $lokasi->delete();

            return redirect()
                ->route('admin.lokasi-presensi.index')
                ->with('success', 'Lokasi presensi berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Get current location coordinates (for AJAX)
     */
    public function getCoordinates(Request $request)
    {
        $address = $request->input('address');
        
        // Using Google Geocoding API or other service
        // For now, return dummy data or implement actual geocoding
        
        return response()->json([
            'success' => true,
            'latitude' => -7.2575,
            'longitude' => 112.7521,
            'message' => 'Koordinat berhasil didapatkan'
        ]);
    }
}