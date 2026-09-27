<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard Guru - Ratio Learn</title>

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

    <!-- SIDEBAR GURU -->
    <aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
        <div class="flex flex-col h-full overflow-y-auto">
            
            <!-- LOGO HEADER -->
            <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
                <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
                <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Portal Guru</span>
            </div>

            <!-- MENU SIDEBAR GURU -->
            <nav class="px-4 py-6 space-y-1.5 flex-1">
                <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.dashboard') || request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                
                <a href="{{ route('guru.lkpd.index') }}" 
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('guru.lkpd.*') ? 'bg-blue-100 text-blue-700' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span class="text-lg">📋</span>
                        <span>Kelola LKPD</span>
                </a>

                <a href="{{ route('guru.materi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.materi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📖</span><span>Kelola Materi</span>
                </a>

                <a href="{{ route('guru.tugas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.tugas*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📝</span><span>Kelola Tugas</span>
                </a>

                <a href="{{ route('guru.ujian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.ujian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">✏️</span><span>Kelola Ujian/Kuis</span>
                </a>

                <a href="{{ route('guru.penilaian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.penilaian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📊</span><span>Penilaian & Evaluasi</span>
                </a>

                <a href="{{ route('guru.presensi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.presensi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📅</span><span>Rekap Presensi Siswa</span>
                </a>

                <a href="{{ route('diskusi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('diskusi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💬</span><span>Forum Diskusi</span>
                </a>

                <a href="{{ route('guru.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.refleksi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💭</span><span>Kelola Refleksi</span>
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
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 text-xs font-semibold transition cursor-pointer">
                        <span class="text-base">↪</span><span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            
            <!-- KOMPONEN LONCENG NOTIFIKASI -->
            <x-notification-bell />

            <!-- PROFILE ITEM -->
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">
                        Guru
                    </div>
                </div>
            </a>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- PAGE TITLE & FILTER KELAS -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">Dashboard Guru</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola kelas, buat pengumuman, dan tinjau hasil penilaian siswa secara terpusat.</p>
                </div>

                <!-- FORM FILTER KELAS -->
                <form method="GET" action="{{ route('guru.dashboard') }}" class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-600">Filter Kelas:</span>
                    <select name="kelas_id" onchange="this.form.submit()" class="bg-white border border-slate-200 text-slate-800 text-xs font-bold px-3 py-2 rounded-xl focus:ring-2 focus:ring-blue-300 outline-none cursor-pointer">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}" @selected($selectedKelasId == $k->id)>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- NOTIFIKASI SUKSES -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <!-- STATISTIK RINGKAS -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-1">
                    <span class="text-2xl">🏫</span>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $totalKelas }}</div>
                    <div class="text-xs font-semibold text-slate-400">Total Kelas</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-1">
                    <span class="text-2xl">👨‍🎓</span>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $totalSiswa }}</div>
                    <div class="text-xs font-semibold text-slate-400">Siswa Terdaftar</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-1">
                    <span class="text-2xl">🎮</span>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $totalAktivitas }}</div>
                    <div class="text-xs font-semibold text-slate-400">Aktivitas & LKPD</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-1">
                    <span class="text-2xl">📥</span>
                    <div class="text-2xl font-extrabold text-amber-600">{{ $totalPendingSubmissions }}</div>
                    <div class="text-xs font-semibold text-slate-400">Tugas Perlu Dinilai</div>
                </div>
            </div>

            <!-- BAGIAN PENGUMUMAN KELAS -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span>📢</span> Pengumuman Kelas
                        </h2>
                        <p class="text-xs text-slate-400">Daftar informasi dan instruksi penting yang ditujukan kepada siswa.</p>
                    </div>

                    <button type="button" onclick="openAnnouncementModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition cursor-pointer">
                        + Buat Pengumuman
                    </button>
                </div>

                <!-- LIST PENGUMUMAN -->
                <!-- LIST PENGUMUMAN -->
                <div class="space-y-3">
                    @forelse($pengumuman as $p)
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1 min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-800">{{ $p->judul }}</span>
                                    <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $p->kelas->nama_kelas ?? 'Semua Kelas' }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $p->isi }}</p>
                                <span class="text-[10px] text-slate-400 block pt-1">
                                    {{ optional($p->diterbitkan_at ?? $p->created_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                                </span>
                            </div>

                            <!-- TOMBOL OPSI (EDIT & HAPUS) -->
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('guru.pengumuman.edit', $p->id) }}" class="text-xs font-bold text-blue-600 hover:bg-blue-100/70 bg-white px-3 py-1.5 rounded-lg border border-blue-200 transition">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('guru.pengumuman.destroy', $p->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-red-600 hover:bg-red-50 bg-white px-3 py-1.5 rounded-lg border border-red-200 transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400 italic">
                            Belum ada pengumuman yang dipublikasikan.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL BUAT PENGUMUMAN -->
    <div id="announcement-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800">Buat Pengumuman Baru</h3>
                <button type="button" onclick="closeAnnouncementModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('guru.pengumuman.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Kelas</label>
                    <select name="kelas_id" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pengumuman</label>
                    <input type="text" name="judul" required placeholder="Contoh: Jadwal Ujian Tengah Semester" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pengumuman</label>
                    <textarea name="isi" rows="4" required placeholder="Tuliskan isi informasi untuk siswa..." class="w-full text-xs font-medium border border-slate-200 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-blue-300"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeAnnouncementModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl">Publikasikan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAnnouncementModal() {
            document.getElementById('announcement-modal').classList.remove('hidden');
        }
        function closeAnnouncementModal() {
            document.getElementById('announcement-modal').classList.add('hidden');
        }
    </script>
</body>
</html>