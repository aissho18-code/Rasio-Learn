<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        return view('profile-show', compact('user'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        return view('profile-edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($data);

        if ($request->filled('nis') && ($user->role === 'siswa' || $user->hasRole('siswa'))) {
            \App\Models\SiswaProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['nis' => $request->input('nis')]
            );
        }

        return redirect()->route('profile.show')->with('status', 'Profil berhasil diperbarui.');
    }

    public function editPassword(Request $request)
    {
        $user = $request->user();
        return view('profile-password', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->password = Hash::make($data['password']);
        $user->plain_password = $data['password']; // Sinkronisasi teks asli jika dipakai di admin
        $user->save();

        return redirect()->route('profile.show')->with('status', 'Password berhasil diperbarui.');
    }

    public function uploadAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->avatar = $path;
        $user->save();

        return back()->with('status', 'Foto profil berhasil diperbarui.');
    }
}