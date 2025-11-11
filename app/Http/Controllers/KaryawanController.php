<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jabatan;
use App\Models\Fakultas;
use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::with(['jabatan', 'departemen', 'fakultas'])->paginate(10);
        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        $jabatan = Jabatan::all();
        $departemen = Departemen::where('status_aktif', true)->get();
        $fakultas = Fakultas::where('status_aktif', true)->get();

        return view('admin.karyawan.create', compact('jabatan', 'departemen', 'fakultas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nip' => 'required|unique:karyawan,nip|max:20',
                'nama_lengkap' => 'required|max:100',
                'jenis_kelamin' => 'required|in:L,P',
                'tanggal_lahir' => 'required|date',
                'email' => 'required|email|unique:karyawan,email|unique:users,email',
                'nomor_telepon' => 'required|max:15',
                'id_jabatan' => 'required|exists:jabatan,id_jabatan',
                'id_departemen' => 'required|exists:departemen,id_departemen',
                'id_fakultas' => 'required|exists:fakultas,id_fakultas',
                'status_aktif' => 'sometimes|boolean',
                'tanggal_mulai_kerja' => 'required|date',
                'tanggal_berhenti_kerja' => 'nullable|date|after:tanggal_mulai_kerja',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ],
            [
                'nip.unique' => 'NIP sudah terdaftar',
                'email.unique' => 'Email sudah terdaftar',
                'tanggal_berhenti_kerja.after' => 'Tanggal berhenti kerja harus setelah tanggal mulai kerja',
            ],
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $barcode = Str::uuid()->toString();

        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'role' => 'user',
            'status' => 'active',
            'phone' => $request->nomor_telepon,
            'barcode_token' => $barcode,
        ]);

        $data = [
            'nip' => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'email' => $request->email,
            'nomor_telepon' => $request->nomor_telepon,
            'id_jabatan' => $request->id_jabatan,
            'id_departemen' => $request->id_departemen,
            'id_fakultas' => $request->id_fakultas,
            'tanggal_mulai_kerja' => $request->tanggal_mulai_kerja,
            'tanggal_berhenti_kerja' => $request->tanggal_berhenti_kerja,
            'status_aktif' => $request->has('status_aktif') ? true : false,
            'user_id' => $user->id,
        ];

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto-karyawan', 'public');
            $data['foto'] = $fotoPath;
        }

        Karyawan::create($data);

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load(['jabatan', 'departemen', 'fakultas']);
        return view('admin.karyawan.show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan)
    {
        $jabatan = Jabatan::all();
        $departemen = Departemen::where('status_aktif', true)->get();
        $fakultas = Fakultas::where('status_aktif', true)->get();

        return view('admin.karyawan.edit', compact('karyawan', 'jabatan', 'departemen', 'fakultas'));
    }

    public function update(Request $request, Karyawan $karyawan): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nip' => 'required|max:20|unique:karyawan,nip,' . $karyawan->id_karyawan . ',id_karyawan',
                'nama_lengkap' => 'required|max:100',
                'jenis_kelamin' => 'required|in:L,P',
                'tanggal_lahir' => 'required|date',
                'email' => 'required|email|unique:karyawan,email,' . $karyawan->id_karyawan . ',id_karyawan',
                'nomor_telepon' => 'required|max:15',
                'id_jabatan' => 'required|exists:jabatan,id_jabatan',
                'id_departemen' => 'required|exists:departemen,id_departemen',
                'id_fakultas' => 'required|exists:fakultas,id_fakultas',
                'status_aktif' => 'sometimes|boolean',
                'tanggal_mulai_kerja' => 'required|date',
                'tanggal_berhenti_kerja' => 'nullable|date|after:tanggal_mulai_kerja',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ],
            [
                'nip.unique' => 'NIP sudah terdaftar',
                'email.unique' => 'Email sudah terdaftar',
                'tanggal_berhenti_kerja.after' => 'Tanggal berhenti kerja harus setelah tanggal mulai kerja',
            ],
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        // Handle status_aktif checkbox
        $data['status_aktif'] = $request->has('status_aktif') ? true : false;

        // Handle file upload
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }

            $fotoPath = $request->file('foto')->store('foto-karyawan', 'public');
            $data['foto'] = $fotoPath;
        }

        $karyawan->update($data);

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan): RedirectResponse
    {
        // Delete photo if exists
        if ($karyawan->foto) {
            Storage::disk('public')->delete($karyawan->foto);
        }

        $karyawan->delete();

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
