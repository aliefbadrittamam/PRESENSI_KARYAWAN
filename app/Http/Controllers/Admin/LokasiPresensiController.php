<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\LokasiPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LokasiPresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lokasiList = LokasiPresensi::with('fakultas')->orderBy('created_at', 'desc')->get();
        return view('admin.presensi.lokasi_presensi.index', compact('lokasiList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fakultasList = Fakultas::all();
        return view('admin.presensi.lokasi_presensi.create', compact('fakultasList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:10|max:5000',
            'jenis_lokasi' => 'required|in:kantor,gedung,laboratorium,lainnya',
            'id_fakultas' => 'required|exists:fakultas,id_fakultas',
            'status_aktif' => 'required|boolean',
            'waktu_operasional_mulai' => 'nullable|date_format:H:i',
            'waktu_operasional_selesai' => 'nullable|date_format:H:i|after:waktu_operasional_mulai',
            'keterangan' => 'nullable|string',
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi',
            'latitude.required' => 'Latitude wajib diisi',
            'latitude.between' => 'Latitude harus berada di antara -90 dan 90',
            'longitude.required' => 'Longitude wajib diisi',
            'longitude.between' => 'Longitude harus berada di antara -180 dan 180',
            'radius_meter.required' => 'Radius wajib diisi',
            'radius_meter.min' => 'Radius minimal 10 meter',
            'radius_meter.max' => 'Radius maksimal 5000 meter',
            'jenis_lokasi.required' => 'Jenis lokasi wajib dipilih',
            'id_fakultas.required' => 'Fakultas wajib dipilih',
            'id_fakultas.exists' => 'Fakultas tidak ditemukan',
            'waktu_operasional_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai',
        ]);

        // ✅ VALIDASI: Cek apakah fakultas sudah memiliki lokasi presensi
        $existingLokasi = LokasiPresensi::where('id_fakultas', $request->id_fakultas)->first();
        
        if ($existingLokasi) {
            $fakultas = Fakultas::find($request->id_fakultas);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Fakultas ' . $fakultas->nama_fakultas . ' sudah memiliki lokasi presensi: ' . $existingLokasi->nama_lokasi . '. Satu fakultas hanya dapat memiliki satu lokasi presensi.');
        }

        try {
            DB::beginTransaction();

            LokasiPresensi::create([
                'nama_lokasi' => $request->nama_lokasi,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_meter' => $request->radius_meter,
                'jenis_lokasi' => $request->jenis_lokasi,
                'id_fakultas' => $request->id_fakultas,
                'status_aktif' => $request->status_aktif,
                'waktu_operasional_mulai' => $request->waktu_operasional_mulai,
                'waktu_operasional_selesai' => $request->waktu_operasional_selesai,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.presensi.lokasi_presensi.index')
                ->with('success', 'Lokasi presensi berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan lokasi presensi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lokasi = LokasiPresensi::with('fakultas')->findOrFail($id);
        return view('admin.presensi.lokasi_presensi.show', compact('lokasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);
        $fakultasList = Fakultas::all();
        return view('admin.presensi.lokasi_presensi.edit', compact('lokasi', 'fakultasList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);

        $request->validate([
            'nama_lokasi' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:10|max:5000',
            'jenis_lokasi' => 'required|in:kantor,gedung,laboratorium,lainnya',
            'id_fakultas' => 'required|exists:fakultas,id_fakultas',
            'status_aktif' => 'required|boolean',
            'waktu_operasional_mulai' => 'nullable|date_format:H:i',
            'waktu_operasional_selesai' => 'nullable|date_format:H:i|after:waktu_operasional_mulai',
            'keterangan' => 'nullable|string',
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi',
            'latitude.required' => 'Latitude wajib diisi',
            'latitude.between' => 'Latitude harus berada di antara -90 dan 90',
            'longitude.required' => 'Longitude wajib diisi',
            'longitude.between' => 'Longitude harus berada di antara -180 dan 180',
            'radius_meter.required' => 'Radius wajib diisi',
            'radius_meter.min' => 'Radius minimal 10 meter',
            'radius_meter.max' => 'Radius maksimal 5000 meter',
            'jenis_lokasi.required' => 'Jenis lokasi wajib dipilih',
            'id_fakultas.required' => 'Fakultas wajib dipilih',
            'id_fakultas.exists' => 'Fakultas tidak ditemukan',
            'waktu_operasional_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai',
        ]);

        // ✅ VALIDASI: Cek apakah fakultas sudah memiliki lokasi presensi (kecuali lokasi yang sedang diedit)
        $existingLokasi = LokasiPresensi::where('id_fakultas', $request->id_fakultas)
            ->where('id_lokasi', '!=', $id)
            ->first();
        
        if ($existingLokasi) {
            $fakultas = Fakultas::find($request->id_fakultas);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Fakultas ' . $fakultas->nama_fakultas . ' sudah memiliki lokasi presensi: ' . $existingLokasi->nama_lokasi . '. Satu fakultas hanya dapat memiliki satu lokasi presensi.');
        }

        try {
            DB::beginTransaction();

            $lokasi->update([
                'nama_lokasi' => $request->nama_lokasi,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_meter' => $request->radius_meter,
                'jenis_lokasi' => $request->jenis_lokasi,
                'id_fakultas' => $request->id_fakultas,
                'status_aktif' => $request->status_aktif,
                'waktu_operasional_mulai' => $request->waktu_operasional_mulai,
                'waktu_operasional_selesai' => $request->waktu_operasional_selesai,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.lokasi-presensi.index')
                ->with('success', 'Lokasi presensi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui lokasi presensi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $lokasi = LokasiPresensi::findOrFail($id);
            $lokasi->delete();

            return redirect()
                ->route('admin.lokasi-presensi.index')
                ->with('success', 'Lokasi presensi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus lokasi presensi: ' . $e->getMessage());
        }
    }

    /**
     * Get coordinates from address (untuk AJAX)
     */
    public function getCoordinates(Request $request)
    {
        // Implementasi geocoding jika diperlukan
        return response()->json([
            'success' => false,
            'message' => 'Fitur geocoding belum diimplementasikan'
        ]);
    }
}