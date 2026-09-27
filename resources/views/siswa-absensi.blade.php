<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Presensi Kehadiran Siswa - Ratio Learn</title>

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
        
    <x-dashboard-header />

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Presensi Kehadiran Siswa</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pantau rekapitulasi kehadiran dan lakukan presensi harian Anda di sini.</p>
            </div>

            <!-- NOTIFIKASI ALERTS -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-semibold flex items-center justify-between">
                    <span>✨ {{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-semibold flex items-center justify-between">
                    <span>⚠️ {{ session('error') }}</span>
                </div>
            @endif

            <!-- BAGIAN ATAS: KARTU AKSI & STATISTIK REAL-TIME -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Kolom Kiri: Form Pilih Status Presensi -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white p-6 rounded-2xl shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="bg-white/20 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Sesi Hari Ini</span>
                        <h3 class="font-bold text-md mt-3">📅 {{ \Carbon\Carbon::now()->format('d F Y') }}</h3>
                        <p class="text-xs text-blue-100 mt-1">
                            {{ $sudahAbsenHariIni ? 'Anda sudah melakukan presensi hari ini.' : 'Pilih status kehadiran Anda hari ini:' }}
                        </p>
                    </div>

                    <div class="mt-4">
                        @if(!$sudahAbsenHariIni)
                            <form action="{{ route('siswa.absensi.store') }}" method="POST" class="space-y-3">
                                @csrf
                                <!-- Pilihan Status Presensi -->
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <label class="flex items-center space-x-2 bg-white/10 hover:bg-white/20 p-2 rounded-lg cursor-pointer">
                                        <input type="radio" name="status" value="Hadir" checked class="text-blue-600 focus:ring-0">
                                        <span class="font-semibold">Hadir</span>
                                    </label>
                                    <label class="flex items-center space-x-2 bg-white/10 hover:bg-white/20 p-2 rounded-lg cursor-pointer">
                                        <input type="radio" name="status" value="Terlambat" class="text-blue-600 focus:ring-0">
                                        <span class="font-semibold">Terlambat</span>
                                    </label>
                                    <label class="flex items-center space-x-2 bg-white/10 hover:bg-white/20 p-2 rounded-lg cursor-pointer">
                                        <input type="radio" name="status" value="Izin" class="text-blue-600 focus:ring-0">
                                        <span class="font-semibold">Izin</span>
                                    </label>
                                    <label class="flex items-center space-x-2 bg-white/10 hover:bg-white/20 p-2 rounded-lg cursor-pointer">
                                        <input type="radio" name="status" value="Sakit" class="text-blue-600 focus:ring-0">
                                        <span class="font-semibold">Sakit</span>
                                    </label>
                                </div>

                                <!-- Input Keterangan Opsional -->
                                <div>
                                    <input type="text" name="keterangan" placeholder="Keterangan (opsional, misal: Surat dokter)" class="w-full text-xs bg-white/10 border border-white/20 text-white placeholder-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white">
                                </div>

                                <button type="submit" class="w-full bg-white text-blue-700 hover:bg-blue-50 font-bold py-2.5 px-4 rounded-xl text-xs shadow transition flex items-center justify-center space-x-2">
                                    <span>✅ Kirim Presensi</span>
                                </button>
                            </form>
                        @else
                            <div class="w-full bg-white/20 text-white font-bold py-3 px-4 rounded-xl text-xs text-center mt-4">
                                ✓ Anda sudah melakukan presensi hari ini.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Kolom Kanan: Statistik Berdasarkan Database -->
                <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-center text-center">
                        <span class="text-xs text-gray-400 font-medium">Persentase</span>
                        <h4 class="text-2xl font-bold text-green-600 mt-1">{{ $persentase }}%</h4>
                        <span class="text-[10px] text-gray-400 mt-1">Kehadiran Real</span>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-center text-center">
                        <span class="text-xs text-gray-400 font-medium">Hadir</span>
                        <h4 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalHadir }}</h4>
                        <span class="text-[10px] text-green-600 mt-1 font-semibold">Tepat Waktu</span>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-center text-center">
                        <span class="text-xs text-gray-400 font-medium">Izin</span>
                        <h4 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalIzin }}</h4>
                        <span class="text-[10px] text-amber-600 mt-1 font-semibold">Resmi</span>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-center text-center">
                        <span class="text-xs text-gray-400 font-medium">Sakit</span>
                        <h4 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalSakit }}</h4>
                        <span class="text-[10px] text-blue-600 mt-1 font-semibold">Dengan Surat</span>
                    </div>
                </div>

            </div>

            <!-- BAGIAN BAWAH: TABEL RIWAYAT DARI DATABASE -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm">Riwayat Presensi Kehadiran</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Catatan kehadiran tersimpan secara real-time dari database.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/75 border-b border-gray-200 text-gray-400 uppercase tracking-wider">
                                <th class="py-3 px-6 font-semibold">Tanggal</th>
                                <th class="py-3 px-6 font-semibold">Keterangan / Alasan</th>
                                <th class="py-3 px-6 font-semibold text-right">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($riwayatAbsen as $absen)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-6 font-semibold text-gray-800">
                                        {{ $absen->tanggal ?? $absen->created_at?->format('d F Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-500">{{ $absen->keterangan }}</td>
                                    <td class="py-4 px-6 text-right">
                                        @if($absen->status === 'Hadir')
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg font-bold">Hadir</span>
                                        @elseif($absen->status === 'Terlambat')
                                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-lg font-bold">Terlambat</span>
                                        @elseif($absen->status === 'Izin')
                                            <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-lg font-bold">Izin</span>
                                        @else
                                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-lg font-bold">Sakit</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-gray-400">Belum ada data riwayat presensi yang tercatat. Silakan pilih status dan kirim presensi di atas.</td>
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