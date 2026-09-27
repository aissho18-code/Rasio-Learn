<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diskusi;
use App\Models\KomentarDiskusi;
use App\Models\DiskusiReaction;
use App\Models\User;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;

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
    public function store(Request $request, LearningNotificationService $notifications)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
        ]);

        $diskusi = Diskusi::create([
            'user_id' => auth()->id(),
            'judul' => $request->judul,
            'pesan' => $request->pesan,
        ]);

        $actor = $request->user();
        if ($actor->role === 'siswa' || $actor->hasRole('siswa')) {
            $kelasId = optional($actor->siswaProfile)->kelas_id;
            if ($kelasId) {
                $notifications->notifyClassTeacher((int) $kelasId, new LearningNotification(
                    type: 'discussion',
                    title: 'Topik Forum Baru',
                    message: $actor->name . ' membuat topik: ' . $diskusi->judul,
                    url: route('diskusi.index'),
                    eventKey: 'discussion.created:' . $diskusi->id . ':' . $actor->id
                ));
            }
        }

        return redirect()->route('diskusi.index')->with('success', 'Topik diskusi berhasil dipublikasikan!');
    }

    // Menyimpan tanggapan/komentar pada suatu topik
    public function storeKomentar(Request $request, $id, LearningNotificationService $notifications)
    {
        $request->validate([
            'pesan' => 'required|string',
        ]);

        $komentar = KomentarDiskusi::create([
            'diskusi_id' => $id,
            'user_id' => auth()->id(),
            'pesan' => $request->pesan,
        ]);

        $diskusi = Diskusi::with('user')->findOrFail($id);
        $actor = $request->user();
        $notification = new LearningNotification(
            type: 'discussion',
            title: 'Respons Forum Baru',
            message: $actor->name . ' merespons topik: ' . $diskusi->judul,
            url: route('diskusi.index'),
            eventKey: 'discussion.comment:' . $komentar->id
        );

        if ($diskusi->user && (int) $diskusi->user_id !== (int) $actor->id) {
            $notifications->sendOnce($diskusi->user, $notification);
        }

        if ($actor->role === 'siswa' || $actor->hasRole('siswa')) {
            $kelasId = optional($actor->siswaProfile)->kelas_id;
            if ($kelasId) {
                $notifications->notifyClassTeacher((int) $kelasId, $notification);
            }
        }

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