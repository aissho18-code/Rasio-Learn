<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use Illuminate\Http\Request;

class AdminPengumumanController extends Controller
{
    public function index(Request $request)
    {
        $pengumuman = Pengumuman::with('kelas')
            ->where('guru_id', $request->user()->id)
            ->latest('diterbitkan_at')
            ->latest()
            ->get();

        return view('admin-pengumuman-index', compact('pengumuman'));
    }

    public function create()
    {
        return view('admin-pengumuman-form', [
            'pengumuman' => new Pengumuman(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'target_audience' => ['required', 'in:guru,siswa,semua'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string', 'max:5000'],
        ]);

        Pengumuman::create([
            'guru_id' => $request->user()->id,
            'kelas_id' => null,
            'target_audience' => $data['target_audience'],
            'judul' => $data['judul'],
            'isi' => $data['isi'],
            'diterbitkan_at' => now(),
        ]);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    public function edit(Request $request, int $id)
    {
        return view('admin-pengumuman-form', [
            'pengumuman' => $this->findOwnedAnnouncement($request, $id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $pengumuman = $this->findOwnedAnnouncement($request, $id);
        $data = $request->validate([
            'target_audience' => ['required', 'in:guru,siswa,semua'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string', 'max:5000'],
        ]);

        $pengumuman->update([
            'target_audience' => $data['target_audience'],
            'judul' => $data['judul'],
            'isi' => $data['isi'],
            'diterbitkan_at' => now(),
        ]);
        PengumumanRead::where('pengumuman_id', $pengumuman->id)->delete();

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        $pengumuman = $this->findOwnedAnnouncement($request, $id);
        PengumumanRead::where('pengumuman_id', $pengumuman->id)->delete();
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function findOwnedAnnouncement(Request $request, int $id): Pengumuman
    {
        return Pengumuman::where('guru_id', $request->user()->id)->findOrFail($id);
    }
}