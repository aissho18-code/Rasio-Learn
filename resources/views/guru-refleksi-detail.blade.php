<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rekap Refleksi Siswa - Portal Guru</title>
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
            <div class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Guru Pengajar' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Guru' }}</div>
                </div>
            </div>
        </header>

        <div class="px-8 pb-8 space-y-6 flex-1 max-w-6xl">
            <div>
                <a href="{{ route('guru.refleksi.index') }}" class="text-xs font-bold text-blue-600 hover:underline mb-1 inline-block">&larr; Kembali ke Kelola Refleksi</a>
                <h1 class="text-xl font-extrabold text-slate-900">Rekap Jawaban Siswa: {{ $refleksi->judul_refleksi }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pertemuan Ke-{{ $refleksi->pertemuan }} &bull; Total Mengisi: {{ $refleksi->submissions->count() }} Siswa</p>
            </div>

            <div class="space-y-4">
                @forelse($refleksi->submissions as $sub)
                    @php $a = $sub->answers ?? [] @endphp
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-base">👤</span>
                                <span class="font-bold text-slate-800 text-xs">{{ $sub->siswa->name ?? 'Siswa' }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium">
                                {{ $sub->updated_at->format('d M Y, H:i') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-1">
                                <span class="font-bold text-slate-700 block">1. Hal Baru Dipahami:</span>
                                <span class="text-slate-600">{{ $a['q1'] ?? '-' }}</span>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-1">
                                <span class="font-bold text-slate-700 block">2. Membingungkan:</span>
                                <span class="text-slate-600">{{ $a['q2'] ?? '-' }}</span>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-1">
                                <span class="font-bold text-slate-700 block">3. Cara Mengatasi Kesulitan:</span>
                                <span class="text-slate-600">{{ $a['q3'] ?? '-' }}</span>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-1">
                                <span class="font-bold text-slate-700 block">4. Ingin Dipelajari Kembali:</span>
                                <span class="text-slate-600">{{ $a['q4'] ?? '-' }}</span>
                            </div>
                        </div>

                        @if($sub->catatan)
                            <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100 text-xs">
                                <span class="font-bold text-blue-900 block">Catatan Siswa:</span>
                                <span class="text-blue-800">{{ $sub->catatan }}</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center text-gray-400 text-xs italic">
                        Belum ada siswa yang mengisi refleksi untuk pertemuan ini.
                    </div>
                @endforelse
            </div>
        </div>
    </main>
</body>
</html>