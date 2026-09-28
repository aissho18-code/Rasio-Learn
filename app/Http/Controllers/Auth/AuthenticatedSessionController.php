<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
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
        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        $authenticatedUser = Auth::guard('web')->user();
        $dashboardRoute = $authenticatedUser?->dashboardRouteName();

        if ($dashboardRoute === null) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Role akun tidak valid. Hubungi administrator untuk memperbaiki akun Anda.',
            ]);
        }

        $authenticatedUser->forceFill(['last_activity_at' => now()])->saveQuietly();

        return redirect()->route($dashboardRoute);
    }

    public function destroy(Request $request)
    {
        $request->user()?->forceFill(['last_activity_at' => null])->saveQuietly();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}