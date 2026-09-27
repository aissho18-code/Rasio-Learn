<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengumuman - Ratio Learn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 p-6 min-h-screen">
    <div class="max-w-5xl mx-auto space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900">📢 Kelola Pengumuman Kelas</h1>
                <p class="text-xs text-slate-500 mt-0.5">Daftar informasi yang Anda publikasikan ke siswa.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('guru.dashboard') }}" class="text-xs font-bold text-slate-600 hover:underline">
                    ← Kembali ke Dashboard
                </a>
                <a href="{{ route('guru.pengumuman.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition">
                    + Buat Pengumuman
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-xs space-y-3">
            @forelse ($pengumuman as $item)
                <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1 min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-800">{{ $item->judul }}</span>
                            <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $item->kelas->nama_kelas ?? 'Semua Kelas' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $item->isi }}</p>
                        <span class="text-[10px] text-slate-400 block pt-1">
                            {{ optional($item->diterbitkan_at ?? $item->created_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                        </span>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('guru.pengumuman.edit', $item->id) }}" class="text-xs font-bold text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-200 transition">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('guru.pengumuman.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg border border-red-200 transition cursor-pointer">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-xs text-slate-400 italic">
                    Belum ada pengumuman yang dipublikasikan.
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>