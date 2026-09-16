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

        // Generate OTP for registration
        $otp = rand(100000, 999999);
        \Illuminate\Support\Facades\Cache::put('register_otp_' . $user->email, $otp, now()->addMinutes(10));
        
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp, 'register'));
        } catch (\Exception $e) {
            // Ignore mail errors in case of missing env configs
        }

        return redirect()->route('verify-otp', ['email' => $user->email])->with('success', 'Pendaftaran akun berhasil! Silakan cek email Anda untuk mendapatkan kode OTP.');
    }

    public function showVerifyOtp(Request $request)
    {
        if (!$request->has('email')) {
            return redirect()->route('login');
        }
        return view('auth.verify-otp', ['email' => $request->email]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric',
        ]);

        $cachedOtp = \Illuminate\Support\Facades\Cache::get('register_otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.'])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();

        \Illuminate\Support\Facades\Cache::forget('register_otp_' . $request->email);

        Auth::login($user);

        return redirect()->route('member.dashboard')->with('success', 'Email berhasil diverifikasi. Silakan lengkapi profil dan unggah berkas persyaratan Anda.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem.'
        ]);

        $otp = rand(100000, 999999);
        \Illuminate\Support\Facades\Cache::put('reset_otp_' . $request->email, $otp, now()->addMinutes(10));
        
        try {
            \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\OtpVerificationMail($otp, 'reset'));
        } catch (\Exception $e) {
            // Ignore mail errors
        }

        return redirect()->route('reset-password', ['email' => $request->email])->with('success', 'Kode OTP reset sandi telah dikirim ke email Anda.');
    }

    public function showResetPassword(Request $request)
    {
        if (!$request->has('email')) {
            return redirect()->route('forgot-password');
        }
        return view('auth.reset-password', ['email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $cachedOtp = \Illuminate\Support\Facades\Cache::get('reset_otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.'])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        \Illuminate\Support\Facades\Cache::forget('reset_otp_' . $request->email);

        return redirect()->route('login')->with('success', 'Kata sandi berhasil diatur ulang. Silakan login dengan kata sandi baru Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
