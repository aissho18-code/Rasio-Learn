<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diskusi;
use App\Models\KomentarDiskusi;
use App\Models\DiskusiReaction;

class DiskusiController extends Controller
{
    // Menampilkan halaman forum diskusi beserta komentar dan jumlah reaction
    public function index()
    {
        $diskusis = Diskusi::with(['user', 'komentars.user', 'reactions'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('diskusi', compact('diskusis'));
    }

    // Menyimpan topik diskusi baru
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
        ]);

        Diskusi::create([
            'user_id' => auth()->id(),
            'judul' => $request->judul,
            'pesan' => $request->pesan,
        ]);

        return redirect()->route('diskusi.index')->with('success', 'Topik diskusi berhasil dipublikasikan!');
    }

    // Menyimpan tanggapan/komentar pada suatu topik
    public function storeKomentar(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string',
        ]);

        KomentarDiskusi::create([
            'diskusi_id' => $id,
            'user_id' => auth()->id(),
            'pesan' => $request->pesan,
        ]);

        return redirect()->route('diskusi.index')->with('success', 'Tanggapan berhasil dikirim!');
    }

    // Memberikan Reaction (Like / Dislike)
    public function reaction(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        $userId = auth()->id();

        // Cek apakah user sudah pernah reaction pada topik ini
        $existing = DiskusiReaction::where('diskusi_id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if ($existing->type === $request->type) {
                // Jika diklik tombol yang sama, hapus reaction (batal)
                $existing->delete();
            } else {
                // Jika berbeda (misal dari like jadi dislike), update
                $existing->update(['type' => $request->type]);
            }
        } else {
            // Buat baru jika belum ada
            DiskusiReaction::create([
                'diskusi_id' => $id,
                'user_id' => $userId,
                'type' => $request->type,
            ]);
        }

        return redirect()->route('diskusi.index');
    }

    // Menghapus topik (Guru/Admin bebas menghapus untuk moderasi)
    public function destroy($id)
    {
        $diskusi = Diskusi::findOrFail($id);
        $user = auth()->user();

        // Guru/Admin atau pemelihara pesan berhak menghapus
        if ($user->role === 'guru' || $user->role === 'admin' || $diskusi->user_id === $user->id) {
            $diskusi->delete();
            return redirect()->route('diskusi.index')->with('success', 'Topik diskusi berhasil dihapus.');
        }

        return redirect()->route('diskusi.index')->with('error', 'Anda tidak memiliki hak akses untuk menghapus topik ini.');
    }
}