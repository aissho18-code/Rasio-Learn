<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Models\SiswaProfile;
use App\Models\GuruProfile;
use Spatie\Permission\Models\Role;

class KelasManagementController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount(['siswa'])->with('wali')->orderBy('nama_kelas')->get();
        return view('admin-kelas-index', compact('kelas'));
    }

    public function create()
    {
        $gurus = User::forRoles('guru')->get();
        return view('admin-kelas-form', ['kelas' => new Kelas(), 'gurus' => $gurus]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'nama_kelas' => 'required|string|max:150',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'jadwal' => 'nullable|string|max:255',
            'kapasitas' => 'nullable|integer|min:1',
        ]);
        Kelas::create($data);
        return redirect()->route('admin.kelas.index')->with('status', 'Kelas berhasil dibuat.');
    }

    public function edit(Kelas $kelas)
    {
        $gurus = User::forRoles('guru')->get();
        return view('admin-kelas-form', compact('kelas', 'gurus'));
    }

    public function update(Request $r, Kelas $kelas)
    {
        $data = $r->validate([
            'nama_kelas' => 'required|string|max:150',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'jadwal' => 'nullable|string|max:255',
            'kapasitas' => 'nullable|integer|min:1',
        ]);
        $kelas->update($data);
        return redirect()->route('admin.kelas.index')->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return back()->with('status', 'Kelas berhasil dihapus.');
    }

    public function addParticipant(Request $r, Kelas $kelas)
    {
        $p = $r->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:siswa,guru',
        ]);

        $user = User::find($p['user_id']);

        if ($p['role'] === 'siswa') {
            SiswaProfile::updateOrCreate(['user_id' => $user->id], ['kelas_id' => $kelas->id]);
            if ($user->role !== 'siswa') {
                $user->update(['role' => 'siswa']);
            }
        } else {
            GuruProfile::updateOrCreate(['user_id' => $user->id], ['mapel_id' => null]);
            if ($user->role !== 'guru') {
                $user->update(['role' => 'guru']);
            }
            Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
            $user->syncRoles(['guru']);
            $kelas->update(['wali_kelas_id' => $user->id]);
        }
        return back()->with('status', 'Peserta berhasil ditambahkan ke kelas.');
    }

    public function removeParticipant(Request $r, Kelas $kelas)
    {
        $p = $r->validate(['user_id' => 'required|exists:users,id', 'role' => 'required|in:siswa,guru']);
        $user = User::find($p['user_id']);
        if ($p['role'] === 'siswa') {
            SiswaProfile::where('user_id', $user->id)->update(['kelas_id' => null]);
        } else {
            if ($kelas->wali_kelas_id == $user->id) {
                $kelas->update(['wali_kelas_id' => null]);
            }
        }
        return back()->with('status', 'Peserta berhasil dihapus dari kelas.');
    }

    public function moveParticipant(Request $r, Kelas $kelas)
    {
        $p = $r->validate([
            'user_id' => 'required|exists:users,id',
            'target_kelas_id' => 'required|exists:kelas,id',
            'role' => 'required|in:siswa,guru',
        ]);
        $user = User::find($p['user_id']);
        $target = Kelas::find($p['target_kelas_id']);
        if ($p['role'] === 'siswa') {
            SiswaProfile::updateOrCreate(['user_id' => $user->id], ['kelas_id' => $target->id]);
        } else {
            if ($kelas->wali_kelas_id == $user->id) {
                $kelas->update(['wali_kelas_id' => null]);
            }
            $target->update(['wali_kelas_id' => $user->id]);
        }
        return back()->with('status', 'Peserta berhasil dipindah ke kelas.');
    }
}