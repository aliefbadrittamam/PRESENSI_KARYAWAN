<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BarcodeLoginController extends Controller
{
    public function login($token)
    {
        $user = User::where('barcode_token', $token)->where('status', 'active')->first();

        if (!$user) {
            return redirect()->route('barcode.scanner')->with('error', 'Token tidak valid.');
        }

        Auth::login($user);

        // Check user role and redirect accordingly
        if ($user->role === 'admin') {
            return redirect()->route('home')->with('success', 'Login via barcode berhasil!');
        }
        
        // arahkan ke dashboard user
        return redirect()->route('user.dashboard')->with('success', 'Login via barcode berhasil!');
    }

    public function scanner()
    {
        // return view ke halaman barcode scanner
        return view('auth.barcode-scanner');
    }
}
