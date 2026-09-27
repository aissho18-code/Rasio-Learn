<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request)
    {
        return view('auth.login', [
            'loginRole' => $request->query('role', 'siswa'),
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:admin,guru,siswa'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // 2. Cek Keberadaan Email
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        // 3. Cek Status Keaktifan Akun (jika ada kolom active)
        if (isset($user->active) && ! $user->active) {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda sedang dinonaktifkan oleh admin.',
            ]);
        }

        // 4. Validasi Password
        if (! Auth::validate([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ])) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        // 5. Ambil Role Asli User (Utamakan Spatie Permission, Fallback ke kolom role)
        $actualRole = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : null;
        if (! $actualRole && ! empty($user->role)) {
            $actualRole = $user->role;
        }

        // 6. Tolak Login Jika Role Form Tidak Cocok dengan Role Akun Sebenarnya
        if ($actualRole !== $credentials['role']) {
            throw ValidationException::withMessages([
                'email' => sprintf(
                    'Akun ini terdaftar sebagai %s. Silakan pilih tab login sebagai %s.',
                    strtoupper($actualRole ?? 'pengguna'),
                    strtoupper($actualRole ?? 'pengguna')
                ),
            ]);
        }

        // 7. Melakukan Autentikasi Login
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // 8. Redirect Sesuai Role
        return redirect()->intended(match ($actualRole) {
            'admin' => route('dashboard'),
            'guru'  => route('dashboard'),
            'siswa' => route('dashboard'),
            default => route('dashboard'),
        });
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}