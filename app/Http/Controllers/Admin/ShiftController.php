<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 
use App\Models\ShiftKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = ShiftKerja::all();
        return view('admin.shift.index', compact('shifts'));
    }

    public function create()
    {
        return view('admin.shift.create');
    }

    /**
     * ✅ ENHANCEMENT: Store dengan DB Transaction
     */
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

        try {
            DB::beginTransaction();

            ShiftKerja::create($request->all());

            DB::commit();

            return redirect()->route('admin.shift.index')
                ->with('success', 'Shift kerja berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating shift: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menambahkan shift: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(ShiftKerja $shift)
    {
        $shift->load(['presensi', 'presensi.karyawan']);
        return view('admin.shift.show', compact('shift'));
    }

    public function edit(ShiftKerja $shift)
    {
        return view('admin.shift.edit', compact('shift'));
    }

    /**
     * ✅ ENHANCEMENT: Update dengan DB Transaction
     */
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

        try {
            DB::beginTransaction();

            $shift->update($request->all());

            DB::commit();

            return redirect()->route('admin.shift.index')
                ->with('success', 'Shift kerja berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating shift: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal memperbarui shift: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ✅ ENHANCEMENT: Destroy dengan DB Transaction dan check usage
     */
    public function destroy(ShiftKerja $shift)
    {
        try {
            DB::beginTransaction();

            // ✅ ENHANCEMENT: Cek apakah shift memiliki presensi dengan lock
            $presensiCount = DB::table('presensi')
                ->where('id_shift', $shift->id_shift)
                ->lockForUpdate() // 🔒 Lock untuk prevent race condition
                ->count();

            if ($presensiCount > 0) {
                throw new \Exception('Tidak dapat menghapus shift karena masih memiliki data presensi.');
            }

            $shift->delete();

            DB::commit();

            return redirect()->route('admin.shift.index')
                ->with('success', 'Shift kerja berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting shift: ' . $e->getMessage());
            
            return redirect()->route('admin.shift.index')
                ->with('error', $e->getMessage());
        }
    }
}