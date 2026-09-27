<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pengumuman->judul }} - Ratio Learn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 p-6 min-h-screen">
    <div class="max-w-2xl mx-auto space-y-4">
        <a href="{{ url()->previous() }}" class="inline-block text-xs font-bold text-blue-600 hover:underline">
            ← Kembali
        </a>

        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-xs space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                    {{ $pengumuman->kelas->nama_kelas ?? 'Semua Kelas' }}
                </span>
                <h1 class="text-lg font-bold text-slate-900 mt-2">{{ $pengumuman->judul }}</h1>
                <p class="text-[10px] text-slate-400 mt-1">
                    Dipublikasikan pada {{ optional($pengumuman->diterbitkan_at ?? $pengumuman->created_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                </p>
            </div>

            <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $pengumuman->isi }}
            </div>
        </div>
    </div>
</body>
</html>