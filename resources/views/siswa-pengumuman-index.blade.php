<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Pengumuman - Ratio Learn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 p-6 min-h-screen">
    <div class="max-w-3xl mx-auto space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-bold text-slate-900">📢 Semua Pengumuman Kelas</h1>
            <div class="flex items-center gap-3">
                <x-notification-bell />
                <a href="{{ route('dashboard') }}" class="text-xs font-bold text-blue-600 hover:underline">
                    ← Kembali ke Dashboard
                </a>
            </div>
        </div>

        <div class="space-y-3">
            @forelse ($pengumuman as $p)
                <a href="{{ route('siswa.pengumuman.show', $p->id) }}" class="block bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-blue-300 transition space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800">{{ $p->judul }}</span>
                        @if(!$p->is_read)
                            <span class="bg-blue-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-full">Baru</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 line-clamp-2">{{ $p->isi }}</p>
                    <span class="text-[10px] text-slate-400 block pt-1">
                        {{ optional($p->diterbitkan_at ?? $p->created_at)->format('d M Y, H:i') }}
                    </span>
                </a>
            @empty
                <div class="bg-white p-6 rounded-2xl text-center text-xs text-slate-400 italic">
                    Belum ada pengumuman.
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>