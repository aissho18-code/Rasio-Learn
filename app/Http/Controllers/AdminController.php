<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Menampilkan daftar pengguna (Guru & Siswa) di Dashboard Admin
    public function index()
    {
        $users = User::whereIn('role', ['guru', 'siswa'])->get();
        return view('dashboard-admin', compact('users'));
    }

    // Menyimpan akun baru (Guru/Siswa) yang dibuat oleh Admin
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:guru,siswa',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // OTOMATIS BUAT ROLE DI DATABASE JIKA BELUM ADA (MENCEGAH ERROR RoleDoesNotExist)
        Role::firstOrCreate(['name' => $request->role, 'guard_name' => 'web']);

        // TETAPKAN ROLE KE USER BARU
        $user->assignRole($request->role);

        return redirect()->back()->with('success', 'Akun berhasil ditambahkan!');
    }

    // Menghapus akun
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Akun berhasil dihapus!');
    }
}