<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JabatanController extends Controller
{
    public function index()
    {
         $jabatan = Jabatan::orderBy('id_jabatan', 'asc')->paginate(10);
         return view('admin.jabatan.index', compact('jabatan'));
    }

    public function create()
    {
        return view('admin.jabatan.create');
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'kode_jabatan' => 'required|string|max:10|unique:jabatan,kode_jabatan',
        'nama_jabatan' => 'required|string|max:100',
        'jenis_jabatan' => 'required|in:struktural,fungsional',
        'keterangan' => 'nullable|string',
    ]);

    Jabatan::create($validated);

    return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil ditambahkan.');
}



    public function show(Jabatan $jabatan)
    {
    $jabatan->load(['karyawan', 'karyawan.departemen']);
    return view('jabatan.show', compact('jabatan'));
    }

    public function edit(Jabatan $jabatan)
    {
    return view('admin.jabatan.edit', compact('jabatan'));
    }
   public function update(Request $request, Jabatan $jabatan)
{
    $validated = $request->validate([
        'kode_jabatan' => 'required|string|max:10|unique:jabatan,kode_jabatan,' . $jabatan->id_jabatan . ',id_jabatan',
        'nama_jabatan' => 'required|string|max:100',
        'jenis_jabatan' => 'required|in:struktural,fungsional',
        'keterangan' => 'nullable|string',
    ]);

    $jabatan->update($validated);

    return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil diperbarui.');
}


    public function destroy(Jabatan $jabatan)
    {
        if ($jabatan->karyawan()->count() > 0) {
            return redirect()->route('admin.jabatan.index')
                ->with('error', 'Tidak dapat menghapus jabatan karena masih memiliki karyawan.');
        }

        $jabatan->delete();

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}