<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Karyawan;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::with(['jabatan', 'departemen', 'fakultas'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        // ✅ Check apakah user memiliki password yang valid
        // Password dianggap "ada" jika bukan default/kosong
        $hasPassword = $this->checkUserHasPassword($user);

        return view('user.profile.index', compact('karyawan', 'user', 'hasPassword'));
    }

    /**
     * Check if user has a valid password set
     * 
     * @param User $user
     * @return bool
     */
    private function checkUserHasPassword(User $user): bool
    {
        // Cek apakah password field ada dan tidak kosong
        if (empty($user->password)) {
            return false;
        }

        // Cek apakah password masih default (optional - jika ada pattern khusus)
        // Misalnya jika password default adalah 6 digit angka
        // Kita bisa cek dengan mencoba beberapa pattern umum
        
        // Untuk keamanan, kita anggap jika password ada di database = valid
        // Kecuali jika ada field tambahan 'password_set_at' atau 'is_temp_password'
        return true;
    }

    /**
     * Create new password (untuk user yang belum punya password)
     */
    public function createPassword(Request $request)
    {
        $user = Auth::user();

        // Validasi bahwa user memang belum punya password
        if ($this->checkUserHasPassword($user) && !empty($user->password)) {
            return redirect()->back()->with('error', 'Anda sudah memiliki password. Gunakan form "Ubah Password".');
        }

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'Password berhasil dibuat! Anda sekarang dapat login menggunakan password ini.');
    }

    /**
     * Update existing password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Password lama wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
            'new_password.different' => 'Password baru harus berbeda dari password lama',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password lama tidak sesuai.'])->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah!');
    }

    /**
     * Update profile photo
     */
    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'photo.required' => 'Foto wajib dipilih',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
            'photo.max' => 'Ukuran foto maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        // Delete old photo if exists
        if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
            Storage::disk('public')->delete($karyawan->foto);
        }

        // Store new photo
        $path = $request->file('photo')->store('foto-karyawan', 'public');

        // Update database
        $karyawan->update([
            'foto' => $path
        ]);

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Regenerate QR Code token
     */
    public function regenerateQRCode(Request $request)
    {
        $user = Auth::user();

        // Generate new barcode token
        $newToken = Str::uuid()->toString();

        $user->update([
            'barcode_token' => $newToken
        ]);

        return redirect()->back()->with('success', 'QR Code baru berhasil digenerate! QR Code lama sudah tidak berlaku.');
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nomor_telepon' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email,' . $user->id . '|unique:karyawan,email,' . $karyawan->id_karyawan . ',id_karyawan',
        ], [
            'nomor_telepon.required' => 'Nomor telepon wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update user
        $user->update([
            'email' => $request->email,
            'phone' => $request->nomor_telepon,
        ]);

        // Update karyawan
        $karyawan->update([
            'email' => $request->email,
            'nomor_telepon' => $request->nomor_telepon,
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }
}