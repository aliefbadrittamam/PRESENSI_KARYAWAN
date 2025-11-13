<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 
use App\Models\Departemen;
use App\Models\Fakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartemenController extends Controller
{
    public function index()
    {
        $departemen = Departemen::with('fakultas')->get();
        return view('admin.departemen.index', compact('departemen'));
    }

    public function create()
    {
        $fakultas = Fakultas::where('status_aktif', true)->get();
        return view('admin.departemen.create', compact('fakultas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_departemen' => 'required|unique:departemen|max:10',
            'nama_departemen' => 'required|max:100',
            'id_fakultas' => 'required|exists:fakultas,id_fakultas',
            'deskripsi' => 'nullable',
            'status_aktif' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Departemen::create($request->all());

        return redirect()->route('admin.departemen.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function show(Departemen $departemen)
    {
    $departemen->load(['fakultas', 'karyawan', 'karyawan.jabatan']);
    return view('admin.departemen.show', compact('departemen'));
    }

    public function edit(Departemen $departemen)
    {
    $fakultas = Fakultas::where('status_aktif', true)->get();
    return view('admin.departemen.edit', compact('departemen', 'fakultas'));
    }

    public function update(Request $request, Departemen $departemen)
    {
        $validator = Validator::make($request->all(), [
            'kode_departemen' => 'required|max:10|unique:departemen,kode_departemen,' . $departemen->id_departemen . ',id_departemen',
            'nama_departemen' => 'required|max:100',
            'id_fakultas' => 'required|exists:fakultas,id_fakultas',
            'deskripsi' => 'nullable',
            'status_aktif' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $departemen->update($request->all());

        return redirect()->route('admin.departemen.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Departemen $departemen)
    {
        if ($departemen->karyawan()->count() > 0) {
            return redirect()->route('admin.departemen.index')
                ->with('error', 'Tidak dapat menghapus departemen karena masih memiliki karyawan.');
        }

        $departemen->delete();

        return redirect()->route('admin.departemen.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}