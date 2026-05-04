<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ===== LOGIN =====
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && !$user->is_active) {
            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi Super Admin.'])
                         ->withInput($request->only('email'));
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Update last login
            auth()->user()->update(['last_login_at' => now()]);

            // Log activity
            ActivityLog::log('login', 'Login ke sistem', auth()->user());

            return redirect()->intended(route('dashboard'))
                             ->with('success', 'Selamat datang, ' . auth()->user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    // ===== REGISTER =====
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.required'         => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah terdaftar.',
            'password.required'     => 'Password wajib diisi.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'password.min'          => 'Password minimal 8 karakter.',
        ]);

        // Auto assign role user
        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => User::ROLE_USER,
            'is_active' => true,
        ]);

        ActivityLog::log('create', "Registrasi akun baru: {$user->name}", $user);

        // Hapus auto login
        // Auth::login($user);
        // $request->session()->regenerate();
        // $user->update(['last_login_at' => now()]);

        return redirect()->route('login')
                         ->with('success', 'Akun Anda berhasil dibuat. Silakan login untuk melanjutkan.');
    }

    // ===== LOGOUT =====
    public function logout(Request $request): RedirectResponse
    {
        ActivityLog::log('logout', 'Logout dari sistem', auth()->user());

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}