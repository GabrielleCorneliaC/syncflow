<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{


    /** Tampilkan halaman login */
    public function showLogin()
    {
        return view('auth.login');
    }

    /** Proses login via email + password */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); 

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        $user = User::where('email', $request->email)->first();

        if ($user && $user->google_id) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun ini didaftarkan melalui Google. Silakan klik tombol Login via Google di bawah.']);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }


    /** Tampilkan halaman register */
    public function showRegister()
    {
        return view('auth.register');
    }

    /** Proses register via form manual */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Password::min(8)],
        ], [
            'first_name.required'  => 'Nama depan wajib diisi.',
            'last_name.required'   => 'Nama belakang wajib diisi.',
            'email.required'       => 'Email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email sudah terdaftar, coba Login.',
            'password.required'    => 'Password wajib diisi.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
            'password.min'         => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'name'     => trim($request->first_name . ' ' . $request->last_name),
            'email'    => $request->email,
            'password' => $request->password, // di-hash otomatis via cast
            'role'     => 'member',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil dibuat. Selamat datang, ' . $user->name . '!');
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Kamu berhasil logout.');
    }


    /**
     * Redirect ke halaman consent Google.
     * Route: GET /auth/google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback dari Google setelah user mengizinkan.
     * Route: GET /auth/google/callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Login via Google gagal. Coba lagi.']);
        }

        $googleName = $googleUser->getName();         
        $googleEmail = $googleUser->getEmail();       
        $googleId    = $googleUser->getId();

        $user = User::where('google_id', $googleId)
                    ->orWhere('email', $googleEmail)
                    ->first();

        if ($user) {
            if (! $user->google_id) {
                $user->update(['google_id' => $googleId]);
            }
        } else {
            $user = User::create([
                'name'      => $googleName,
                'email'     => $googleEmail,
                'google_id' => $googleId,
                'password'  => Hash::make(Str::random(32)), 
                'avatar'    => $googleUser->getAvatar(),    
                'role'      => 'member',
            ]);
        }

        Auth::login($user, true); 
        request()->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Login via Google berhasil. Selamat datang, ' . $user->name . '!');
    }



    /** Tampilkan halaman forgot password */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /** Proses langsung reset password tanpa verifikasi email */
    public function resetPasswordDirect(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
        ], [
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'password.required'  => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak ditemukan di sistem kami.']);
        }

        if ($user->google_id) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun ini didaftarkan melalui Google. Silakan login menggunakan tombol Google.']);
        }

        $user->update([
            'password' => $request->password
        ]);

        return redirect()->route('login')
            ->with('success', 'Password berhasil diubah! Silakan login dengan password baru.');
    }
}
