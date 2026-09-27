<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Models\SiswaProfile;
use App\Models\GuruProfile;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(20);
        return view('admin-users-index', compact('users'));
    }

    public function create()
    {
        $roles = ['admin', 'guru', 'siswa'];
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('admin-users-form', ['user' => new User(), 'roles' => $roles, 'kelas' => $kelas]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,guru,siswa',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        // 1. Buat User Baru di Tabel users
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'plain_password' => $data['password'], // Simpan password teks asli
            'role' => $data['role'],
        ]);
        
        // 2. Sinkronkan dengan Spatie Permission
        if (class_exists(Role::class)) {
            Role::firstOrCreate(['name' => $data['role']]);
            $user->syncRoles([$data['role']]);
        }

        // 3. Buat/Update Profil Berdasarkan Role
        if ($data['role'] === 'siswa' && !empty($data['kelas_id'])) {
            SiswaProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['nis' => null, 'kelas_id' => $data['kelas_id']]
            );
        } elseif ($data['role'] === 'guru' && !empty($data['kelas_id'])) {
            GuruProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['nip' => null, 'mapel_id' => null]
            );
            Kelas::whereKey($data['kelas_id'])->update(['wali_kelas_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $roles = ['admin', 'guru', 'siswa'];
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $kelasId = $user->role === 'guru'
            ? Kelas::where('wali_kelas_id', $user->id)->value('id')
            : optional($user->siswaProfile)->kelas_id;
        return view('admin-users-form', compact('user', 'roles', 'kelas', 'kelasId'));
    }

    public function update(Request $r, User $user)
    {
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,guru,siswa',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $previousRole = $user->role;
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role']
        ];

        // Jika password diisi saat edit, update password & plain_password
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
            $updateData['plain_password'] = $data['password'];
        }

        $user->update($updateData);

        // 1. Sinkronkan Spatie Role (Menghapus role lama jika terjadi perubahan role)
        if (class_exists(Role::class)) {
            Role::firstOrCreate(['name' => $data['role']]);
            $user->syncRoles([$data['role']]);
        }

        // 2. Update/Reset Profil & Relasi Kelas
        if ($data['role'] === 'siswa') {
            SiswaProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['nis' => optional($user->siswaProfile)->nis, 'kelas_id' => $data['kelas_id']]
            );
        } else {
            SiswaProfile::where('user_id', $user->id)->update(['kelas_id' => null]);
        }

        if ($data['role'] === 'guru' && !empty($data['kelas_id'])) {
            GuruProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['nip' => optional($user->guruProfile)->nip, 'mapel_id' => null]
            );
            Kelas::whereKey($data['kelas_id'])->update(['wali_kelas_id' => $user->id]);
        }

        if ($previousRole === 'guru' && $data['role'] !== 'guru') {
            Kelas::where('wali_kelas_id', $user->id)->update(['wali_kelas_id' => null]);
        }

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('status', 'Pengguna berhasil dihapus.');
    }
}