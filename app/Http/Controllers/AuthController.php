<?php

namespace App\Http\Controllers;

use App\Models\ProfilCalonAnggota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // Cek login via email atau username/nama
        $fieldType = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (Auth::attempt([$fieldType => $credentials['email'], 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('member.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email/username atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'nim' => 'nullable|string|max:30',
            'prodi' => 'nullable|string|max:100',
            'angkatan' => 'nullable|string|max:10',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'calon_anggota',
        ]);

        ProfilCalonAnggota::create([
            'user_id' => $user->id,
            'nim' => $validated['nim'] ?? null,
            'prodi' => $validated['prodi'] ?? null,
            'angkatan' => $validated['angkatan'] ?? date('Y'),
            'no_hp' => $validated['no_hp'] ?? null,
            'seleksi_administrasi' => 'proses_seleksi',
            'tes_tulis_wawancara' => 'proses_seleksi',
            'cakruma' => 'proses_seleksi',
        ]);

        Auth::login($user);

        return redirect()->route('member.dashboard')->with('success', 'Pendaftaran akun berhasil! Silakan lengkapi profil dan unggah berkas persyaratan Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
