<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedByRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Jika user sudah login
        if (Auth::check()) {
            $role = Auth::user()->role;

            // Arahkan sesuai role
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($role === 'user') {
                return redirect()->route('karyawan.dashboard');
            }
        }

        return $next($request);
    }
}
