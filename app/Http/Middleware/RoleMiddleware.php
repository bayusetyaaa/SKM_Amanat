<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Calon anggota harus sudah verifikasi email OTP
        if (auth()->user()->role === 'calon_anggota' && is_null(auth()->user()->email_verified_at)) {
            $userEmail = auth()->user()->email;
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('verify-otp', ['email' => $userEmail])
                ->with('warning', 'Silakan selesaikan verifikasi kode OTP terlebih dahulu.');
        }

        if (auth()->user()->role !== $role) {
            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('member.dashboard');
            }
        }

        return $next($request);
    }
}
