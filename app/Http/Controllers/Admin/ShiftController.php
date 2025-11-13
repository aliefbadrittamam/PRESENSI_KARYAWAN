<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 
use App\Models\ShiftKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = ShiftKerja::all();
        return view('admin.shift.index', compact('shifts'));
    }

    public function create()
    {
        return view('shift.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_shift' => 'required|unique:shift_kerja|max:20',
            'nama_shift' => 'required|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|different:jam_mulai',
            'toleransi_keterlambatan' => 'nullable|integer|min:0|max:120',
            'keterangan' => 'nullable',
            'status_aktif' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ShiftKerja::create($request->all());

        return redirect()->route('admin.shift.index')
            ->with('success', 'Shift kerja berhasil ditambahkan.');
    }

    public function show(ShiftKerja $shift)
    {
        $shift->load(['presensi', 'presensi.karyawan']);
        return view('shift.show', compact('shift'));
    }

    public function edit(ShiftKerja $shift)
    {
        return view('shift.edit', compact('shift'));
    }

    public function update(Request $request, ShiftKerja $shift)
    {
        $validator = Validator::make($request->all(), [
            'kode_shift' => 'required|max:20|unique:shift_kerja,kode_shift,' . $shift->id_shift . ',id_shift',
            'nama_shift' => 'required|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|different:jam_mulai',
            'toleransi_keterlambatan' => 'nullable|integer|min:0|max:120',
            'keterangan' => 'nullable',
            'status_aktif' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $shift->update($request->all());

        return redirect()->route('admin.shift.index')
            ->with('success', 'Shift kerja berhasil diperbarui.');
    }

    public function destroy(ShiftKerja $shift)
    {
        // Cek apakah shift memiliki presensi
        if ($shift->presensi()->count() > 0) {
            return redirect()->route('admin.shift.index')
                ->with('error', 'Tidak dapat menghapus shift karena masih memiliki data presensi.');
        }

        $shift->delete();

        return redirect()->route('admin.shift.index')
            ->with('success', 'Shift kerja berhasil dihapus.');
    }
}