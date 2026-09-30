<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Detail Refleksi - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    <!-- SIDEBAR SISWA -->
    <aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
        <div class="flex flex-col h-full overflow-y-auto">
            
            <!-- LOGO HEADER -->
            <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
                <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
                <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Belajar Rasio Jadi Seru!</span>
            </div>

            <!-- MENU SIDEBAR SISWA -->
            <nav class="px-4 py-6 space-y-1.5 flex-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                
                <a href="{{ route('siswa.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs">
                    <span class="text-base">💭</span><span>Refleksi</span>
                </a>
            </nav>

            <!-- LOGOUT -->
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
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Siswa' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Siswa' }}</div>
                </div>
            </a>
        </header>

        <div class="px-8 pb-8 space-y-6 flex-1 max-w-4xl">
            <div>
                <a href="{{ route('siswa.refleksi.index') }}" class="text-xs font-bold text-blue-600 hover:underline mb-1 inline-block">&larr; Kembali ke Daftar Refleksi</a>
                <h1 class="text-xl font-extrabold text-slate-900">Hasil Refleksi — Pertemuan {{ $refleksi->pertemuan }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Dibuat pada: {{ $refleksi->created_at->format('l, d F Y, H:i') }}</p>
            </div>

            @php $a = $refleksi->answers ?? [] @endphp

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-5">
                <div class="space-y-4 text-xs">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-1">
                        <div class="font-bold text-gray-800">1. Apa hal baru yang paling kamu pahami?</div>
                        <div class="text-gray-600 leading-relaxed">{{ $a['q1'] ?? '-' }}</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-1">
                        <div class="font-bold text-gray-800">2. Bagian mana yang masih membingungkan?</div>
                        <div class="text-gray-600 leading-relaxed">{{ $a['q2'] ?? '-' }}</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-1">
                        <div class="font-bold text-gray-800">3. Apa yang kamu lakukan ketika mengalami kesulitan?</div>
                        <div class="text-gray-600 leading-relaxed">{{ $a['q3'] ?? '-' }}</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-1">
                        <div class="font-bold text-gray-800">4. Bagian mana yang ingin kamu pelajari kembali?</div>
                        <div class="text-gray-600 leading-relaxed">{{ $a['q4'] ?? '-' }}</div>
                    </div>
                    @if($refleksi->catatan)
                        <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-1">
                            <div class="font-bold text-blue-900">Catatan Tambahan</div>
                            <div class="text-blue-800 leading-relaxed">{{ $refleksi->catatan }}</div>
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
                    <a href="{{ route('siswa.refleksi.edit', $refleksi) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm transition">
                        ✏️ Edit Refleksi
                    </a>
                    <a href="{{ route('siswa.refleksi.index') }}" class="text-xs font-semibold text-gray-500 hover:underline">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>