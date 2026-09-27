<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Aktivitas & LKPD - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN untuk Real-time Polling Sync -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none" 
      x-data="aktivitasRealtime()" 
      x-init="initPolling()">

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
                
                <a href="{{ route('siswa.aktivitas.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.aktivitas*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <div class="flex items-center gap-3">
                        <span class="text-base">📚</span><span>Aktivitas & LKPD</span>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-ping" x-show="isSyncing"></span>
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

        <!-- CONTAINER CONTENT -->
        <div class="px-8 py-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- HEADER PAGE TITLE -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span>📚</span> Aktivitas Pembelajaran & LKPD
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Unduh lembar kerja peserta didik (LKPD) dan serahkan jawaban tugas Anda di sini.</p>
                </div>
                <button @click="fetchAktivitas()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold px-3 py-2 rounded-xl transition flex items-center gap-1.5">
                    <span :class="{'animate-spin': isSyncing}">🔄</span> Sync
                </button>
            </div>

            <!-- NOTIFIKASI AKTIVITAS BARU -->
            <div x-show="hasNewActivity" x-transition class="bg-blue-600 text-white p-4 rounded-2xl shadow-md flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">🔔</span>
                    <span class="text-xs font-bold">Guru baru saja menerbitkan Aktivitas/LKPD baru!</span>
                </div>
                <button @click="hasNewActivity = false" class="text-xs bg-white text-blue-700 font-bold px-3 py-1.5 rounded-xl">Lihat</button>
            </div>

            <!-- DAFTAR AKTIVITAS REAL-TIME -->
            <div class="space-y-4">
                <template x-for="a in listAktivitas" :key="a.id">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 transition hover:border-blue-200">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm" x-text="a.judul"></h3>
                            <p class="text-xs text-slate-500 mt-1" x-text="a.tujuan || 'Tidak ada deskripsi tujuan'"></p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-[10px] bg-blue-50 text-blue-600 font-bold px-2.5 py-0.5 rounded-full" x-text="'Guru: ' + a.guru_name"></span>
                                <span class="text-[10px] bg-slate-100 text-slate-600 font-bold px-2.5 py-0.5 rounded-full uppercase" x-text="'Tipe: ' + a.respons_type"></span>
                                <span class="text-[10px] text-slate-400" x-text="a.created_at_formatted"></span>
                            </div>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <template x-if="a.has_lkpd">
                                <a :href="a.download_url" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                    <span>📄</span> Download LKPD
                                </a>
                            </template>
                            <a :href="a.show_url" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-1.5">
                                <span>✏️</span> Kerjakan Task
                            </a>
                        </div>
                    </div>
                </template>

                <!-- PEMBERITAHUAN JIKA KOSONG -->
                <div x-show="listAktivitas.length === 0" class="bg-white rounded-2xl p-8 text-center text-slate-400 text-xs border border-gray-100">
                    Belum ada aktivitas yang diterbitkan untuk kelas Anda.
                </div>
            </div>

        </div>
    </main>

    <script>
        function aktivitasRealtime() {
            return {
                listAktivitas: [],
                isSyncing: false,
                hasNewActivity: false,
                previousCount: 0,
                
                initPolling() {
                    this.fetchAktivitas();
                    // Polling data otomatis setiap 4 detik untuk update real-time dari Guru
                    setInterval(() => {
                        this.fetchAktivitas();
                    }, 4000);
                },

                async fetchAktivitas() {
                    this.isSyncing = true;
                    try {
                        const res = await fetch('{{ route("siswa.aktivitas.api") }}');
                        const json = await res.json();
                        if (json.success) {
                            if (this.previousCount > 0 && json.data.length > this.previousCount) {
                                this.hasNewActivity = true;
                            }
                            this.listAktivitas = json.data;
                            this.previousCount = json.data.length;
                        }
                    } catch (e) {
                        console.error('Realtime sync error:', e);
                    } finally {
                        setTimeout(() => { this.isSyncing = false; }, 500);
                    }
                }
            }
        }
    </script>
</body>
</html>