<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard Siswa - Ratio Learn</title>

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
                
                <a href="{{ route('siswa.aktivitas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.aktivitas*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📚</span>
                    <span>Aktivitas & LKPD</span>
                </a>

                <a href="{{ route('siswa.absensi') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.absensi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📅</span><span>Presensi</span>
                </a>

                <a href="{{ route('siswa.materi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.materi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
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

                <a href="{{ route('siswa.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.refleksi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💭</span><span>Refleksi</span>
                </a>
            </nav>

            <!-- DOODLE MATHEMATICS -->
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
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- GREETING BANNER -->
            <div class="bg-gradient-to-r from-blue-50/80 to-indigo-50/50 rounded-2xl p-6 border border-blue-100/60 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                        👋 Halo, {{ Auth::user()->name ?? 'Siswa' }}!
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Selamat datang di <strong class="text-blue-600">Ratio Learn</strong>. Yuk lanjutkan pembelajaranmu dan raih hasil terbaik!
                    </p>
                </div>
                <div class="w-12 h-12 bg-white rounded-2xl shadow-xs border border-blue-100 flex items-center justify-center text-2xl shrink-0">
                    📚
                </div>
            </div>

            <!-- MAIN GRID CONTENT (PEMBELAJARAN & PENGUMUMAN) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- KIRI: KARTU GRID PEMBELAJARAN -->
                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-4 bg-blue-600 rounded-full inline-block"></span>
                            Pembelajaran
                        </h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Akses materi dan selesaikan setiap tahap pembelajaran sesuai urutan.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        
                        <!-- 1. Aktivitas & LKPD -->
                        <a href="{{ route('siswa.aktivitas.index') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition space-y-3 group relative">
                            <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold flex items-center justify-center">1</span>
                            <div class="text-2xl">🎮</div>
                            <div>
                                <h3 class="font-bold text-xs text-slate-800 group-hover:text-blue-600 transition">Aktivitas & LKPD</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5 line-clamp-2">Kerjakan aktivitas interaktif dan LKPD untuk memahami konsep rasio.</p>
                            </div>
                            <span class="text-xs text-blue-600 font-bold absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition">&rarr;</span>
                        </a>

                        <!-- 2. Materi -->
                        <a href="{{ route('siswa.materi.index') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition space-y-3 group relative">
                            <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold flex items-center justify-center">2</span>
                            <div class="text-2xl">📖</div>
                            <div>
                                <h3 class="font-bold text-xs text-slate-800 group-hover:text-blue-600 transition">Materi</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5 line-clamp-2">Pelajari konsep rasio secara bertahap.</p>
                            </div>
                            <span class="text-xs text-blue-600 font-bold absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition">&rarr;</span>
                        </a>

                        <!-- 3. Forum Diskusi -->
                        <a href="{{ route('diskusi.index') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition space-y-3 group relative">
                            <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold flex items-center justify-center">3</span>
                            <div class="text-2xl">💬</div>
                            <div>
                                <h3 class="font-bold text-xs text-slate-800 group-hover:text-blue-600 transition">Forum Diskusi</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5 line-clamp-2">Diskusikan materi bersama teman dan guru.</p>
                            </div>
                            <span class="text-xs text-blue-600 font-bold absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition">&rarr;</span>
                        </a>

                        <!-- 4. Tugas -->
                        <a href="{{ route('siswa.tugas.index') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition space-y-3 group relative">
                            <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold flex items-center justify-center">4</span>
                            <div class="text-2xl">📋</div>
                            <div>
                                <h3 class="font-bold text-xs text-slate-800 group-hover:text-blue-600 transition">Tugas</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5 line-clamp-2">Kerjakan tugas sesuai tenggat waktu.</p>
                            </div>
                            <span class="text-xs text-blue-600 font-bold absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition">&rarr;</span>
                        </a>

                        <!-- 5. Ujian/Quiz -->
                        <a href="{{ route('siswa.ujian.index') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition space-y-3 group relative">
                            <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold flex items-center justify-center">5</span>
                            <div class="text-2xl">📝</div>
                            <div>
                                <h3 class="font-bold text-xs text-slate-800 group-hover:text-blue-600 transition">Ujian/Quiz</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5 line-clamp-2">Uji pemahamanmu melalui kuis dan ujian.</p>
                            </div>
                            <span class="text-xs text-blue-600 font-bold absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition">&rarr;</span>
                        </a>

                        <!-- 6. Evaluasi -->
                        <a href="{{ route('siswa.evaluasi') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition space-y-3 group relative">
                            <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold flex items-center justify-center">6</span>
                            <div class="text-2xl">📊</div>
                            <div>
                                <h3 class="font-bold text-xs text-slate-800 group-hover:text-blue-600 transition">Evaluasi</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5 line-clamp-2">Lihat hasil penilaian pembelajaran.</p>
                            </div>
                            <span class="text-xs text-blue-600 font-bold absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition">&rarr;</span>
                        </a>

                        <!-- 7. Refleksi -->
                        <a href="{{ route('siswa.refleksi.index') }}" class="bg-white p-4 rounded-2xl border border-blue-100 shadow-xs hover:shadow-md transition space-y-3 group relative border-l-4 border-l-blue-600">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-[11px] font-bold flex items-center justify-center">7</span>
                            <div class="text-2xl">💭</div>
                            <div>
                                <h3 class="font-bold text-xs text-blue-700 group-hover:text-blue-800 transition">Refleksi</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5 line-clamp-2">Tuliskan pemahaman & refleksi belajarmu.</p>
                            </div>
                            <span class="text-xs text-blue-600 font-bold absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition">&rarr;</span>
                        </a>

                    </div>
                </div>

                <!-- KANAN: PENGUMUMAN & TP PEMBELAJARAN -->
                <div class="space-y-4">
                    
                    <!-- PENGUMUMAN DINAMIK (DARI BLADE COMPONENT) -->
                    <x-student-announcements />

                    <!-- BAGIAN TP PEMBELAJARAN (TUJUAN PEMBELAJARAN) -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-xs space-y-3">
                        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-lg shrink-0">
                                🎯
                            </div>
                            <div>
                                <h2 class="text-xs font-extrabold text-slate-800">
                                    Tujuan Pembelajaran
                                </h2>
                                <p class="text-[10px] text-slate-400">
                                    Kompetensi yang diharapkan setelah pembelajaran.
                                </p>
                            </div>
                        </div>

                        <p class="text-xs leading-relaxed text-slate-600 font-medium">
                            Setelah mengikuti pembelajaran Rasio, siswa diharapkan mampu:
                        </p>

                        <ol class="space-y-2.5">
                            <li class="flex items-start gap-2.5">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold text-blue-600 mt-0.5">
                                    1
                                </span>
                                <span class="text-xs leading-relaxed text-slate-600">
                                    Memahami konsep dan pengertian rasio.
                                </span>
                            </li>

                            <li class="flex items-start gap-2.5">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold text-blue-600 mt-0.5">
                                    2
                                </span>
                                <span class="text-xs leading-relaxed text-slate-600">
                                    Menyatakan dan menyederhanakan rasio dalam berbagai bentuk.
                                </span>
                            </li>

                            <li class="flex items-start gap-2.5">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold text-blue-600 mt-0.5">
                                    3
                                </span>
                                <span class="text-xs leading-relaxed text-slate-600">
                                    Menentukan rasio senilai.
                                </span>
                            </li>

                            <li class="flex items-start gap-2.5">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold text-blue-600 mt-0.5">
                                    4
                                </span>
                                <span class="text-xs leading-relaxed text-slate-600">
                                    Menerapkan konsep rasio dalam menyelesaikan masalah sehari-hari.
                                </span>
                            </li>
                        </ol>
                    </div>
                </div>

            </div>

        </div>
    </main>

</body>
</html>