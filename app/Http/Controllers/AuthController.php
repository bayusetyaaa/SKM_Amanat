<?php

namespace App\Http\Controllers;

use App\Models\ProfilCalonAnggota;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
            'seleksi_administrasi' => null,
            'tes_tulis_wawancara' => null,
            'cakruma' => null,
        ]);

        // Simpan OTP ke database (berlaku 15 menit)
        $otp = (string) rand(100000, 999999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => 'register_' . $user->email],
            ['token' => $otp, 'created_at' => now()]
        );
        
        try {
            Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp, 'register'));
        } catch (\Exception $e) {
            // Ignore mail errors in case of missing env configs
        }

        return redirect()->route('verify-otp', ['email' => $user->email])->with('success', 'Pendaftaran akun berhasil! Silakan periksa kotak masuk atau spam email Anda untuk kode OTP.');
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
            'otp' => 'required|string',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', 'register_' . $request->email)
            ->first();

        if (!$record || trim((string)$record->token) !== trim((string)$request->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP tidak sesuai. Pastikan Anda memasukkan 6 digit angka yang benar.'])->withInput();
        }

        // Cek kadaluarsa OTP (15 menit)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa (berlaku 15 menit). Silakan klik tombol "Kirim Ulang Kode OTP".'])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();

        // Hapus token yang sudah digunakan
        DB::table('password_reset_tokens')
            ->where('email', 'register_' . $request->email)
            ->delete();

        Auth::login($user);

        return redirect()->route('member.dashboard')->with('success', 'Email berhasil diverifikasi! Selamat datang di SKM Amanat.');
    }

    public function resendVerifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('success', 'Akun Anda sudah terverifikasi sebelumnya. Silakan login.');
        }

        $otp = (string) rand(100000, 999999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => 'register_' . $user->email],
            ['token' => $otp, 'created_at' => now()]
        );

        try {
            Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp, 'register'));
        } catch (\Exception $e) {
            // Ignore
        }

        return back()->with('success', 'Kode OTP baru telah berhasil dikirimkan ke email Anda.');
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

        $otp = (string) rand(100000, 999999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => 'reset_' . $request->email],
            ['token' => $otp, 'created_at' => now()]
        );
        
        try {
            Mail::to($request->email)->send(new \App\Mail\OtpVerificationMail($otp, 'reset'));
        } catch (\Exception $e) {
            // Ignore mail errors
        }

        return redirect()->route('reset-password', ['email' => $request->email])->with('success', 'Kode OTP reset sandi telah dikirim ke email Anda.');
    }

    public function resendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $otp = (string) rand(100000, 999999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => 'reset_' . $request->email],
            ['token' => $otp, 'created_at' => now()]
        );
        
        try {
            Mail::to($request->email)->send(new \App\Mail\OtpVerificationMail($otp, 'reset'));
        } catch (\Exception $e) {
            // Ignore mail errors
        }

        return back()->with('success', 'Kode OTP reset sandi baru telah dikirim ke email Anda.');
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
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', 'reset_' . $request->email)
            ->first();

        if (!$record || trim((string)$record->token) !== trim((string)$request->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP tidak sesuai. Silakan periksa kembali kode di email Anda.'])->withInput();
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP reset sandi sudah kadaluarsa (berlaku 15 menit). Silakan kirim ulang kode.'])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
        }
        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', 'reset_' . $request->email)
            ->delete();

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

