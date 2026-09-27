<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class DirectPasswordResetController extends Controller
{
    /**
     * Tampilan form input email lupa password.
     */
    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Verifikasi email user & simpan di session sementara.
     */
    public function verifyEmail(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $key = 'direct-password-reset:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withInput()->withErrors([
                'email' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        RateLimiter::hit($key, 60);

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return back()->withInput()->withErrors([
                'email' => 'Email tidak terdaftar di sistem Ratio Learn.',
            ]);
        }

        if (isset($user->active) && ! $user->active) {
            return back()->withInput()->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan oleh admin.',
            ]);
        }

        // Simpan email di session sementara
        $request->session()->put('direct_password_reset_email', $data['email']);

        return redirect()->route('password.direct.reset');
    }

    /**
     * Tampilan form pembuatan password baru.
     */
    public function resetForm(Request $request)
    {
        $email = $request->session()->get('direct_password_reset_email');

        if (! $email) {
            return redirect()->route('password.direct.request')->withErrors([
                'email' => 'Silakan masukkan email terlebih dahulu.',
            ]);
        }

        return view('auth.reset-password-direct', compact('email'));
    }

    /**
     * Eksekusi update password baru ke database.
     */
    public function updatePassword(Request $request)
    {
        $email = $request->session()->get('direct_password_reset_email');

        if (! $email) {
            return redirect()->route('password.direct.request')->withErrors([
                'email' => 'Sesi reset password telah berakhir. Silakan ulangi.',
            ]);
        }

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi verifikasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::where('email', $email)->first();

        if (! $user) {
            $request->session()->forget('direct_password_reset_email');
            return redirect()->route('password.direct.request')->withErrors([
                'email' => 'Akun tidak ditemukan.',
            ]);
        }

        $fillData = [
            'password' => Hash::make($data['password']),
            'remember_token' => null,
        ];

        // Update kolom plain_password jika ada di database
        if (Schema::hasColumn('users', 'plain_password')) {
            $fillData['plain_password'] = $data['password'];
        }

        $user->forceFill($fillData)->save();

        // Bersihkan session reset
        $request->session()->forget('direct_password_reset_email');

        return redirect()->route('login')->with('status', 'Password berhasil diperbarui! Silakan login dengan password baru Anda.');
    }
}