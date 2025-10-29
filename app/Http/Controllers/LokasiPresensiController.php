<?php

namespace App\Http\Controllers;

use App\Models\LokasiPresensi;
use App\Models\Fakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LokasiPresensiController extends Controller
{
    public function index()
    {
        $lokasi = LokasiPresensi::with('fakultas')->get();
        return view('lokasi.index', compact('lokasi'));
    }

    public function create()
    {
        $fakultas = Fakultas::where('status_aktif', true)->get();
        return view('lokasi.create', compact('fakultas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lokasi' => 'required|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:10|max:1000',
            'jenis_lokasi' => 'required|in:kantor,gedung,laboratorium,lainnya',
            'id_fakultas' => 'nullable|exists:fakultas,id_fakultas',
            'waktu_operasional_mulai' => 'nullable|date_format:H:i',
            'waktu_operasional_selesai' => 'nullable|date_format:H:i|after:waktu_operasional_mulai',
            'keterangan' => 'nullable',
            'status_aktif' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        LokasiPresensi::create($request->all());

        return redirect()->route('lokasi.index')
            ->with('success', 'Lokasi presensi berhasil ditambahkan.');
    }

    public function show(LokasiPresensi $lokasi)
    {
        $lokasi->load('fakultas');
        return view('lokasi.show', compact('lokasi'));
    }

    public function edit(LokasiPresensi $lokasi)
    {
        $fakultas = Fakultas::where('status_aktif', true)->get();
        return view('lokasi.edit', compact('lokasi', 'fakultas'));
    }

    public function update(Request $request, LokasiPresensi $lokasi)
    {
        $validator = Validator::make($request->all(), [
            'nama_lokasi' => 'required|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:10|max:1000',
            'jenis_lokasi' => 'required|in:kantor,gedung,laboratorium,lainnya',
            'id_fakultas' => 'nullable|exists:fakultas,id_fakultas',
            'waktu_operasional_mulai' => 'nullable|date_format:H:i',
            'waktu_operasional_selesai' => 'nullable|date_format:H:i|after:waktu_operasional_mulai',
            'keterangan' => 'nullable',
            'status_aktif' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $lokasi->update($request->all());

        return redirect()->route('lokasi.index')
            ->with('success', 'Lokasi presensi berhasil diperbarui.');
    }

    public function destroy(LokasiPresensi $lokasi)
    {
        $lokasi->delete();

        return redirect()->route('lokasi.index')
            ->with('success', 'Lokasi presensi berhasil dihapus.');
    }
}