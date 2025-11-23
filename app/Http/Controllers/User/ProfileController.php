<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Check apakah user memiliki password yang valid
        $hasPassword = $this->checkUserHasPassword($user);

        return view('user.profile.index', compact('karyawan', 'user', 'hasPassword'));
    }

    /**
     * Check if user has a valid password set
     */
    private function checkUserHasPassword(User $user): bool
    {
        // Cek apakah password field ada dan tidak kosong
        if (empty($user->password)) {
            return false;
        }
        
        return true;
    }

    /**
     * ✅ ENHANCEMENT: Create new password dengan DB Transaction
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

        // ✅ ENHANCEMENT: DB Transaction
        try {
            DB::beginTransaction();

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Password berhasil dibuat! Anda sekarang dapat login menggunakan password ini.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating password: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal membuat password. Silakan coba lagi.');
        }
    }

    /**
     * ✅ ENHANCEMENT: Update existing password dengan DB Transaction
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

        // ✅ ENHANCEMENT: DB Transaction
        try {
            DB::beginTransaction();

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Password berhasil diubah!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating password: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal mengubah password. Silakan coba lagi.');
        }
    }

    /**
     * ✅ ENHANCEMENT: Update profile photo dengan UUID filename dan DB Transaction
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

        // ✅ ENHANCEMENT: DB Transaction
        try {
            DB::beginTransaction();

            $oldPhoto = $karyawan->foto;

            // ✅ ENHANCEMENT: Store photo dengan unique filename
            $uuid = Str::uuid()->toString();
            $microtime = microtime(true);
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filename = "karyawan_{$karyawan->nip}_{$uuid}_{$microtime}.{$extension}";
            
            $path = $request->file('photo')->storeAs('foto-karyawan', $filename, 'public');

            // Update database
            $karyawan->update([
                'foto' => $path
            ]);

            // Delete old photo if exists (after successful update)
            if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                Storage::disk('public')->delete($oldPhoto);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating photo: ' . $e->getMessage());
            
            // Delete uploaded file jika rollback
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return redirect()->back()->with('error', 'Gagal memperbarui foto. Silakan coba lagi.');
        }
    }

    /**
     * ✅ ENHANCEMENT: Regenerate QR Code dengan retry mechanism dan DB Transaction
     */
    public function regenerateQRCode(Request $request)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // ✅ ENHANCEMENT: Generate new token dengan retry
            $newToken = $this->generateUniqueBarcode();

            $user->update([
                'barcode_token' => $newToken
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'QR Code baru berhasil digenerate! QR Code lama sudah tidak berlaku.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error regenerating QR code: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal generate QR Code baru. Silakan coba lagi.');
        }
    }

    /**
     * ✅ ENHANCEMENT: Update profile information dengan DB Transaction
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

        // ✅ ENHANCEMENT: DB Transaction untuk update User + Karyawan
        try {
            DB::beginTransaction();

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

            DB::commit();

            return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating profile: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

    /**
     * ✅ ENHANCEMENT: Generate unique barcode dengan retry mechanism
     */
    private function generateUniqueBarcode($maxRetries = 5)
    {
        for ($i = 0; $i < $maxRetries; $i++) {
            $barcode = Str::uuid()->toString();
            
            // Check if exists
            if (!User::where('barcode_token', $barcode)->exists()) {
                return $barcode;
            }
        }
        
        throw new \Exception('Gagal generate unique barcode setelah ' . $maxRetries . ' percobaan');
    }
}