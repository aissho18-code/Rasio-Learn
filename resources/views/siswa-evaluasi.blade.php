<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Evaluasi & Penilaian - Ratio Learn</title>

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
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                
                <a href="{{ route('siswa.aktivitas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('siswa.aktivitas*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="text-base">📚</span>
                    <span>Aktivitas & LKPD</span>
                </a>

                <a href="{{ route('siswa.absensi') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.absensi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📅</span><span>Presensi</span>
                </a>

                <a href="{{ route('siswa.materi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.materi*') && request('type') != 'aktivitas' ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📖</span><span>Materi</span>
                </a>

                <a href="{{ route('diskusi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('diskusi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💬</span><span>Forum Diskusi</span>
                </a>

                <a href="{{ route('siswa.tugas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.tugas*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📋</span><span>Tugas</span>
                </a>

                <a href="{{ route('siswa.ujian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.ujian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📝</span><span>Ujian/Quiz</span>
                </a>

                <a href="{{ route('siswa.evaluasi') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.evaluasi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📊</span><span>Evaluasi</span>
                </a>

                <!-- TAB REFLEKSI SISWA -->
                <a href="{{ route('siswa.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.refleksi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💭</span><span>Refleksi</span>
                </a>
            </nav>

            <!-- DOODLE -->
            <div class="px-6 py-4 opacity-20 text-[10px] text-blue-200 font-mono space-y-1 pointer-events-none">
                <div>a : b = c : d</div>
                <div class="text-right">2 : 3</div>
                <div class="text-center">4 : 6</div>
            </div>

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
        
        <!-- HEADER TOP BAR -->
        <x-dashboard-header />

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-6xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Evaluasi & Penilaian Hasil Belajar</h1>
                <p class="text-xs text-slate-500 mt-0.5">Tinjau nilai tugas, kuis, serta catatan umpan balik dari guru Anda.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    ✨ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            <!-- STATISTIK RINGKASAN EVALUASI -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">📊</div>
                    <div>
                        <span class="text-[11px] text-gray-400 font-medium">Rata-rata Nilai</span>
                        <h3 class="text-xl font-extrabold text-slate-900">{{ $rataRataNilai ?? '85.5' }}</h3>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl font-bold">✅</div>
                    <div>
                        <span class="text-[11px] text-gray-400 font-medium">Tugas Dinilai</span>
                        <h3 class="text-xl font-extrabold text-slate-900">{{ $totalDinilai ?? '12' }}</h3>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">📝</div>
                    <div>
                        <span class="text-[11px] text-gray-400 font-medium">Kuis Selesai</span>
                        <h3 class="text-xl font-extrabold text-slate-900">{{ $totalKuis ?? '4' }}</h3>
                    </div>
                </div>
            </div>

            <!-- TABEL DAFTAR EVALUASI DAN NILAI -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm">Riwayat Nilai & Catatan Guru</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Daftar lengkap hasil evaluasi tugas dan ujian harian Anda.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/75 border-b border-gray-200 text-gray-400 uppercase tracking-wider">
                                <th class="py-3 px-6 font-semibold">Judul Tugas / Evaluasi</th>
                                <th class="py-3 px-6 font-semibold">Tanggal Kumpul</th>
                                <th class="py-3 px-6 font-semibold">Catatan / Umpan Balik Guru</th>
                                <th class="py-3 px-6 font-semibold text-right">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($evaluasiList ?? [] as $eval)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-800">
                                        {{ $eval->tugas->judul ?? $eval->judul ?? 'Evaluasi Pembelajaran' }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-500">
                                        {{ $eval->updated_at ? \Carbon\Carbon::parse($eval->updated_at)->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-600 italic">
                                        "{{ $eval->catatan_guru ?? $eval->feedback ?? 'Belum ada catatan dari guru.' }}"
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if(isset($eval->nilai) && $eval->nilai !== null)
                                            <span class="bg-green-100 text-green-700 font-bold px-3 py-1 rounded-lg">
                                                {{ $eval->nilai }}
                                            </span>
                                        @else
                                            <span class="bg-amber-100 text-amber-700 font-semibold px-3 py-1 rounded-lg">
                                                Menunggu Penilaian
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400">
                                        Belum ada data evaluasi atau nilai yang tercatat saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>