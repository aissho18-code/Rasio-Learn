<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use Illuminate\Http\Request;

class SiswaPengumumanController extends Controller
{
    public function index(Request $request)
    {
        $siswa = $request->user();
        $kelasId = optional($siswa->siswaProfile)->kelas_id;

        if (!$kelasId) {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Anda belum masuk ke dalam kelas. Silakan hubungi Guru atau Admin.');
        }

        $pengumuman = Pengumuman::query()
            ->whereIn('target_audience', ['siswa', 'semua'])
            ->where(function ($query) use ($kelasId) {
                $query->whereNull('kelas_id')
                    ->orWhere('kelas_id', $kelasId);
            })
            ->with([
                'kelas',
                'reads' => function ($query) use ($siswa) {
                    $query->where('siswa_id', $siswa->id);
                },
            ])
            ->latest('diterbitkan_at')
            ->latest()
            ->get()
            ->map(function ($item) {
                $read = $item->reads->first();
                $item->is_read = $read?->read_at !== null;
                return $item;
            });

        return view('siswa-pengumuman-index', compact('pengumuman'));
    }

    public function show(Request $request, $id)
    {
        $siswa = $request->user();
        $kelasId = optional($siswa->siswaProfile)->kelas_id;

        $pengumuman = Pengumuman::findOrFail($id);

        $isAllowed = in_array($pengumuman->target_audience, ['siswa', 'semua'], true)
            && ($pengumuman->kelas_id === null || (int) $pengumuman->kelas_id === (int) $kelasId);
        abort_unless($isAllowed, 403, 'Anda tidak memiliki akses ke pengumuman ini.');

        PengumumanRead::updateOrCreate(
            ['pengumuman_id' => $pengumuman->id, 'siswa_id' => $siswa->id],
            ['read_at' => now()]
        );

        return view('siswa-pengumuman-show', compact('pengumuman'));
    }

    public function markAllAsRead(Request $request)
    {
        $siswa = $request->user();
        $kelasId = optional($siswa->siswaProfile)->kelas_id;

        $pengumumanIds = Pengumuman::query()
            ->whereIn('target_audience', ['siswa', 'semua'])
            ->where(function ($query) use ($kelasId) {
                $query->whereNull('kelas_id')
                    ->orWhere('kelas_id', $kelasId);
            })
            ->pluck('id');

        foreach ($pengumumanIds as $pengumumanId) {
            PengumumanRead::updateOrCreate(
                ['pengumuman_id' => $pengumumanId, 'siswa_id' => $siswa->id],
                ['read_at' => now()]
            );
        }

        return back()->with('success', 'Semua pengumuman telah ditandai sebagai dibaca.');
    }
}