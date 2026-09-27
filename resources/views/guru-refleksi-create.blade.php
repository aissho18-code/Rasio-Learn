<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Buat Refleksi Baru - Portal Guru</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    <!-- SIDEBAR GURU -->
    <aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
        <div class="flex flex-col h-full overflow-y-auto">
            <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
                <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
                <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Portal Guru</span>
            </div>
            <nav class="px-4 py-6 space-y-1.5 flex-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                <a href="{{ route('guru.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs">
                    <span class="text-base">💭</span><span>Kelola Refleksi</span>
                </a>
            </nav>
            <div class="p-4 border-t border-slate-800/60">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 text-xs font-semibold transition">
                        <span class="text-base">↪</span><span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            <x-notification-bell />
            <div class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Guru Pengajar' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Guru' }}</div>
                </div>
            </div>
        </header>

        <div class="px-8 pb-8 space-y-6 flex-1 max-w-4xl">
            <div>
                <a href="{{ route('guru.refleksi.index') }}" class="text-xs font-bold text-blue-600 hover:underline mb-1 inline-block">&larr; Kembali ke Kelola Refleksi</a>
                <h1 class="text-xl font-extrabold text-slate-900">Buat Instumen Refleksi Baru</h1>
                <p class="text-xs text-slate-500 mt-0.5">Setelah dibuat, item refleksi ini akan langsung muncul di dashboard siswa.</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-6">
                <form action="{{ route('guru.refleksi.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    
                    <div>
                        <label class="block font-bold text-gray-800 mb-1">Judul Refleksi</label>
                        <input type="text" name="judul_refleksi" placeholder="Contoh: Refleksi Konsep Rasio" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-800 mb-1">Pertemuan Ke-</label>
                        <input type="number" name="pertemuan" min="1" value="1" class="w-32 border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-800 mb-1">Deskripsi / Petunjuk Pengisian (Opsional)</label>
                        <textarea name="deskripsi" rows="3" placeholder="Contoh: Jawablah pertanyaan refleksi berikut untuk mengukur sejauh mana pemahamanmu pada materi rasio..." class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm transition">
                            🚀 Publikasikan Refleksi Ke Siswa
                        </button>
                        <a href="{{ route('guru.refleksi.index') }}" class="text-xs font-semibold text-gray-500 hover:underline">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>