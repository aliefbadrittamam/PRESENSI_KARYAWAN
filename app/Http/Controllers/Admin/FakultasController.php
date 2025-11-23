<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Fakultas;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class FakultasController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::all();
        return view('admin.fakultas.index', compact('fakultas'));
    }

    public function create()
    {
        return view('admin.fakultas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'kode_fakultas' => 'required|unique:fakultas|max:10',
            'nama_fakultas' => 'required|max:100',
            'dekan' => 'nullable|max:100',
            'status_aktif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['status_aktif'] = $request->has('status_aktif') ? true : false;

        Fakultas::create($data);

        return redirect()->route('admin.fakultas.index')
            ->with('success', 'Fakultas berhasil ditambahkan.');
    }

    public function show($id)
    {
        $fakultas = Fakultas::with(['departemen', 'karyawan'])->findOrFail($id);
        return view('admin.fakultas.show', compact('fakultas'));
    }

public function edit(Fakultas $fakultas)
{
    return view('admin.fakultas.edit', compact('fakultas'));
}


    public function update(Request $request, Fakultas $fakultas): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'kode_fakultas' => 'required|max:10|unique:fakultas,kode_fakultas,' . $fakultas->id_fakultas . ',id_fakultas',
            'nama_fakultas' => 'required|max:100',
            'dekan' => 'nullable|max:100',
            'status_aktif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['status_aktif'] = $request->has('status_aktif') ? true : false;

        $fakultas->update($data);

        return redirect()->route('admin.fakultas.index')
            ->with('success', 'Fakultas berhasil diperbarui.');
    }

    public function destroy(Fakultas $fakultas): RedirectResponse
    {
        // Cek apakah fakultas memiliki relasi
        if ($fakultas->departemen()->count() > 0) {
            return redirect()->route('admin.fakultas.index')
                ->with('error', 'Tidak dapat menghapus fakultas karena masih memiliki departemen.');
        }

        $fakultas->delete();

        return redirect()->route('admin.fakultas.index')
            ->with('success', 'Fakultas berhasil dihapus.');
    }
}