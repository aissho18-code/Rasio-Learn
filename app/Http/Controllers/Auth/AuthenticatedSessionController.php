<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        if (! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        $actualRole = $user->role;
        $selectedRole = $credentials['role'] ?? null;

        if (! in_array($actualRole, ['admin', 'guru', 'siswa'], true)) {
            $request->session()->forget('url.intended');

            throw ValidationException::withMessages([
                'email' => 'Role akun tidak valid. Hubungi administrator untuk memperbaiki akun Anda.',
            ]);
        }

        if ($selectedRole && $selectedRole !== $actualRole) {
            $request->session()->forget('url.intended');

            throw ValidationException::withMessages([
                'email' => sprintf(
                    'Akun ini terdaftar sebagai %s. Silakan pilih role %s untuk melanjutkan.',
                    ucfirst($actualRole),
                    ucfirst($actualRole)
                ),
            ]);
        }

        Auth::guard('web')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return redirect()->route(match ($actualRole) {
            'admin' => 'dashboard',
            'guru' => 'guru.dashboard',
            'siswa' => 'dashboard',
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