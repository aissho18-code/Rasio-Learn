<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use Illuminate\Http\Request;

class GuruPengumumanController extends Controller
{
    public function index(Request $request)
    {
        $guru = $request->user();

        $kelasGuru = Kelas::where('wali_kelas_id', $guru->id)
            ->orderBy('nama_kelas')
            ->get();

        $pengumuman = Pengumuman::with('kelas')
            ->where('guru_id', $guru->id)
            ->latest('diterbitkan_at')
            ->latest()
            ->get();

        return view('guru-pengumuman-index', compact('pengumuman', 'kelasGuru'));
    }

    public function create(Request $request)
    {
        $kelasGuru = Kelas::where('wali_kelas_id', $request->user()->id)
            ->orderBy('nama_kelas')
            ->get();

        return view('guru-pengumuman-form', [
            'pengumuman' => new Pengumuman(),
            'kelasGuru' => $kelasGuru,
        ]);
    }

    public function store(Request $request)
    {
        $guru = $request->user();

        $data = $request->validate([
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string', 'max:5000'],
        ]);

        $this->ensureTeacherOwnsClass($guru->id, $data['kelas_id'] ?? null);

        Pengumuman::create([
            'guru_id' => $guru->id,
            'kelas_id' => $data['kelas_id'] ?? null,
            'judul' => $data['judul'],
            'isi' => $data['isi'],
            'diterbitkan_at' => now(),
        ]);

        return redirect()
            ->route('guru.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    public function edit(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $this->ensureTeacherOwnsAnnouncement($request->user()->id, $pengumuman);

        $kelasGuru = Kelas::where('wali_kelas_id', $request->user()->id)
            ->orderBy('nama_kelas')
            ->get();

        return view('guru-pengumuman-form', compact('pengumuman', 'kelasGuru'));
    }

    public function update(Request $request, $id)
    {
        $guru = $request->user();
        $pengumuman = Pengumuman::findOrFail($id);

        $this->ensureTeacherOwnsAnnouncement($guru->id, $pengumuman);

        $data = $request->validate([
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string', 'max:5000'],
        ]);

        $this->ensureTeacherOwnsClass($guru->id, $data['kelas_id'] ?? null);

        // Update record lama dan perbarui waktu terbit
        $pengumuman->update([
            'kelas_id' => $data['kelas_id'] ?? null,
            'judul' => $data['judul'],
            'isi' => $data['isi'],
            'diterbitkan_at' => now(),
        ]);

        // Reset status baca seluruh siswa agar muncul sebagai pengumuman baru
        PengumumanRead::where('pengumuman_id', $pengumuman->id)->delete();

        return redirect()
            ->route('guru.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui dan dikirim ulang sebagai pengumuman baru.');
    }

    public function destroy(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $this->ensureTeacherOwnsAnnouncement($request->user()->id, $pengumuman);

        PengumumanRead::where('pengumuman_id', $pengumuman->id)->delete();
        $pengumuman->delete();

        return redirect()
            ->route('guru.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function ensureTeacherOwnsAnnouncement(int $guruId, Pengumuman $pengumuman): void
    {
        abort_unless((int) $pengumuman->guru_id === $guruId, 403, 'Anda tidak memiliki akses ke pengumuman ini.');
    }

    private function ensureTeacherOwnsClass(int $guruId, ?int $kelasId): void
    {
        if (!$kelasId) return;

        $ownsClass = Kelas::where('id', $kelasId)
            ->where('wali_kelas_id', $guruId)
            ->exists();

        abort_unless($ownsClass, 403, 'Kelas tersebut bukan kelas yang Anda ampu.');
    }
}