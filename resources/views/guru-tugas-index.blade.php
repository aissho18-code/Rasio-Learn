<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manajemen Tugas Pembelajaran - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js untuk Dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    @if ($isAdmin ?? false)
        @include('layouts.sidebar-admin')
    @else
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
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.dashboard') || request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                
                <!-- KELOLA MATERI (ACTIVE) -->
                <a href="{{ route('guru.lkpd.index') }}" 
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('guru.lkpd.*') ? 'bg-blue-100 text-blue-700' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span class="text-lg">📋</span>
                        <span>Kelola LKPD</span>
                </a>
                <a href="{{ route('guru.materi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.materi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📖</span><span>Kelola Materi</span>
                </a>

                <a href="{{ route('guru.tugas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.tugas*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📋</span><span>Kelola Tugas</span>
                </a>

                <a href="{{ route('guru.ujian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.ujian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📝</span><span>Kelola Ujian/Kuis</span>
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
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 text-xs font-semibold transition">
                        <span class="text-base">↪</span><span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>
    @endif

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            <x-notification-bell />

            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Guru Pengajar' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Guru' }}</div>
                </div>
            </a>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manajemen Tugas Pembelajaran</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pilih paket tugas yang ingin dikelola atau buat paket tugas baru.</p>
            </div>

            @if ($isAdmin ?? false)
                <form method="GET" action="{{ route('admin.tugas.index') }}" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-4">
                    <select name="kelas_id" class="rounded-lg border border-slate-200 px-3 py-2 text-xs">
                        <option value="0">Semua Kelas</option>
                        @foreach ($kelasList as $kelas)<option value="{{ $kelas->id }}" @selected($selectedClassId == $kelas->id)>{{ $kelas->nama_kelas }}</option>@endforeach
                    </select>
                    <select name="guru_id" class="rounded-lg border border-slate-200 px-3 py-2 text-xs">
                        <option value="0">Semua Guru</option>
                        @foreach ($teacherList as $teacher)<option value="{{ $teacher->id }}" @selected($selectedTeacherId == $teacher->id)>{{ $teacher->name }}</option>@endforeach
                    </select>
                    <select name="status" class="rounded-lg border border-slate-200 px-3 py-2 text-xs">
                        <option value="">Semua Status</option>
                        <option value="aktif" @selected($selectedStatus === 'aktif')>Aktif</option>
                        <option value="terkunci" @selected($selectedStatus === 'terkunci')>Terkunci</option>
                    </select>
                    <button class="rounded-lg bg-blue-700 px-4 py-2 text-xs font-bold text-white">Filter Tugas</button>
                </form>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- KARTU DASHED: BUAT TUGAS BARU -->
                <a href="{{ route($routePrefix . '.tugas.create') }}" class="border-2 border-dashed border-blue-200 hover:border-blue-400 bg-white rounded-2xl p-8 flex flex-col items-center justify-center text-center cursor-pointer transition group min-h-[200px]">
                    <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-bold shadow-md group-hover:scale-110 transition mb-3">
                        +
                    </div>
                    <h3 class="font-bold text-blue-700 text-sm group-hover:text-blue-800">Buat Tugas Baru</h3>
                    <p class="text-[11px] text-gray-400 mt-1">Tambahkan paket tugas harian atau instruksi baru</p>
                </a>

                <!-- DAFTAR KARTU TUGAS -->
                @forelse($tugasList ?? $tugases ?? [] as $t)
                    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition space-y-4 min-h-[200px] relative">
                        
                        <div>
                            <!-- HEADER KARTU: BADGE AKTIF & DROPDOWN TIGA TITIK (⋮) -->
                            <div class="flex items-center justify-between pb-3">
                                <span class="{{ $t->status === 'terkunci' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-600' }} font-extrabold text-[10px] px-2.5 py-1 rounded-md uppercase tracking-wider">
                                    {{ $t->status === 'terkunci' ? 'TERKUNCI' : 'AKTIF' }}
                                </span>

                                <!-- DROPDOWN MENU TIGA TITIK (⋮) -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition focus:outline-none">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>

                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-20"
                                         style="display: none;">
                                        <a href="{{ route($routePrefix . '.tugas.edit', $t->id) }}" class="flex items-center px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                                            ✏️ Edit Tugas
                                        </a>
                                        <form action="{{ route($routePrefix . '.tugas.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Yakin hapus tugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- KONTEN TUGAS -->
                            <div class="space-y-1.5">
                                <h4 class="font-bold text-gray-800 text-sm leading-snug">{{ $t->judul }}</h4>
                                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $t->deskripsi ?? 'Tidak ada petunjuk tambahan.' }}</p>
                                
                                @if($t->file_path)
                                    <div class="pt-1">
                                        <a href="{{ asset('storage/' . $t->file_path) }}" target="_blank" class="text-[11px] text-blue-600 font-semibold hover:underline inline-flex items-center space-x-1">
                                            <span>📄 Lihat Dokumen Acuan</span>
                                        </a>
                                    </div>
                                @endif
                                @if ($isAdmin ?? false)
                                    <p class="mt-2 text-[10px] text-slate-500">Guru: {{ $t->materi?->kelas?->wali?->name ?? '—' }} · Kelas: {{ $t->materi?->kelas?->nama_kelas ?? '—' }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- FOOTER KARTU: PEKAN & TENGGAT WAKTU -->
                        <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-[11px] text-gray-400 font-medium">
                            <span>{{ $t->pekan ?? 'Pekan 1' }}</span>
                            <span>⏰ {{ $t->tenggat_waktu ? \Carbon\Carbon::parse($t->tenggat_waktu)->format('d M Y, H:i') : '-' }}</span>
                        </div>

                    </div>
                @empty
                @endforelse

            </div>

        </div>
    </main>

</body>
</html>