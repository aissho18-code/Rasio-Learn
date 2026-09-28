<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard Admin - Manajemen Pengguna - Ratio Learn</title>

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

    <!-- SIDEBAR ADMIN -->
    <!-- Sidebar Navigation Admin -->
    <aside class="w-64 bg-[#0B132A] text-white flex flex-col justify-between p-4 min-h-screen select-none shrink-0">
        <div>
            <!-- Header / Logo App -->
            <div class="flex items-center gap-3 px-3 py-4 mb-6">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow-md text-sm">
                    🎓
                </div>
                <div>
                    <div class="font-extrabold text-sm text-white leading-tight">Ratio Learn</div>
                    <div class="text-[10px] text-blue-400 font-semibold tracking-wider uppercase mt-0.5">Portal Admin</div>
                </div>
            </div>

            <!-- Navigasi Menu -->
            <nav class="space-y-1.5">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="text-base">🏠</span>
                    <span>Dashboard</span>
                </a>

                <!-- Kelola Pengguna -->
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.users*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="text-base">👥</span>
                    <span>Kelola Pengguna</span>
                </a>

                <!-- Kelola Kelas -->
                <a href="{{ route('admin.kelas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.kelas*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="text-base">🏫</span>
                    <span>Kelola Kelas</span>
                </a>

                <!-- Kelola Ujian / Quiz (BARU) -->
                <a href="{{ route('admin.exams.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.exams*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="text-base">📝</span>
                    <span>Kelola Ujian</span>
                </a>

                <!-- Proctoring CBT (BARU) -->
                <a href="{{ route('admin.proctoring') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.proctoring*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="text-base">🛡️</span>
                    <span>Proctoring CBT</span>
                </a>

                <!-- Forum Diskusi -->
                <a href="{{ route('diskusi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('diskusi*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="text-base">💬</span>
                    <span>Forum Diskusi</span>
                </a>
            </nav>
        </div>

        <!-- Tombol Logout -->
        <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-slate-800/80">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold text-slate-400 hover:text-red-400 hover:bg-slate-800/50 rounded-xl transition-all duration-200">
                <span class="text-base">↪️</span>
                <span>Logout</span>
            </button>
        </form>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            <x-notification-bell />
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
                        @if(Auth::user()->role === 'siswa')
                            Siswa 
                            @if(isset($currentUserKelasName) && $currentUserKelasName)
                                • Kelas {{ $currentUserKelasName }}
                            @else
                                • <span class="text-red-500 font-semibold">Belum masuk kelas</span>
                            @endif
                        @elseif(Auth::user()->role === 'guru')
                            Guru
                        @else
                            Admin
                        @endif
                    </div>
                </div>
            </a>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Dashboard Admin - Manajemen Pengguna</h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola akun pengguna, tambah akun guru atau siswa baru, dan atur akses sistem secara terpusat.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <section class="grid grid-cols-2 lg:grid-cols-5 gap-3" aria-label="Ringkasan pengguna aktif">
                <div class="bg-white rounded-xl border border-gray-200 p-4"><div class="text-[11px] text-gray-500">Total Guru</div><div class="text-xl font-extrabold" id="count-guru">{{ $guruUsers->count() }}</div></div>
                <div class="bg-white rounded-xl border border-gray-200 p-4"><div class="text-[11px] text-gray-500">Guru Aktif</div><div class="text-xl font-extrabold text-green-700" id="count-guru-active">{{ $totalGuruAktif }}</div></div>
                <div class="bg-white rounded-xl border border-gray-200 p-4"><div class="text-[11px] text-gray-500">Total Siswa</div><div class="text-xl font-extrabold" id="count-siswa">{{ $siswaUsers->count() }}</div></div>
                <div class="bg-white rounded-xl border border-gray-200 p-4"><div class="text-[11px] text-gray-500">Siswa Aktif</div><div class="text-xl font-extrabold text-green-700" id="count-siswa-active">{{ $totalSiswaAktif }}</div></div>
                <div class="bg-white rounded-xl border border-gray-200 p-4"><div class="text-[11px] text-gray-500">Total Pengguna Aktif</div><div class="text-xl font-extrabold text-green-700" id="count-total-active">{{ $totalPenggunaAktif }}</div></div>
            </section>

            <!-- Form Tambah Akun Baru -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center space-x-2">
                        <span>➕</span>
                        <span>Tambah Akun Guru / Siswa Baru</span>
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Daftarkan pengguna baru ke dalam sistem pembelajaran Ratio Learn.</p>
                </div>

                <form action="{{ route('admin.users.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Nama lengkap..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required placeholder="Alamat email..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required placeholder="Kata sandi..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Peran (Role)</label>
                        <select name="role" required class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="guru">Guru</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 px-4 rounded-xl shadow-sm transition">
                            Simpan Akun 🚀
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Akun -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-gray-800 text-base flex items-center space-x-2">
                            <span>👥</span>
                            <span>Daftar Pengguna Terdaftar</span>
                        </h4>
                        <p class="text-xs text-gray-400 mt-0.5">Semua akun aktif yang terdaftar di dalam platform.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/50 text-gray-400 font-bold uppercase border-b border-gray-100 text-[10px] tracking-wider">
                                <th class="py-3.5 px-6">Nama</th>
                                <th class="py-3.5 px-6">Email</th>
                                <th class="py-3.5 px-6">Peran</th>
                                <th class="py-3.5 px-6">Kelas</th>
                                <th class="py-3.5 px-6">Status / Terakhir Aktif</th>
                                <th class="py-3.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($users ?? [] as $u)
                                <tr class="hover:bg-gray-50/50 transition" data-user-id="{{ $u->id }}">
                                    <td class="py-4 px-6 font-bold text-gray-800 flex items-center space-x-2">
                                        <span class="text-gray-400">👤</span>
                                        <span>{{ $u->name }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">
                                        {{ $u->email }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @php($userRole = $u->monitoringRole())
                                        <span class="px-3 py-1 text-[10px] font-extrabold uppercase rounded-full {{ $userRole === 'guru' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                            {{ $userRole }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">{{ $userRole === 'siswa' ? ($u->siswaProfile?->kelas?->nama_kelas ?? '-') : '-' }}</td>
                                    <td class="py-4 px-6">
                                        <span data-user-status class="font-bold {{ $u->isOnline() ? 'text-green-700' : 'text-gray-500' }}">{{ $u->isOnline() ? '● Aktif' : '○ Tidak aktif' }}</span>
                                        <span data-user-last-active class="block text-[10px] text-gray-400 mt-1">{{ $u->lastActiveLabel() }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun ini?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold px-3 py-1.5 rounded-xl text-xs transition">
                                                Hapus 🗑️
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400 italic">Belum ada pengguna terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
<script>
    const adminMonitoringUrl = @json(route('admin.monitoring.status'));

    async function refreshAdminMonitoring() {
        try {
            const response = await fetch(adminMonitoringUrl, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const data = await response.json();
            document.getElementById('count-guru').textContent = data.counts.guru;
            document.getElementById('count-guru-active').textContent = data.counts.guru_active;
            document.getElementById('count-siswa').textContent = data.counts.siswa;
            document.getElementById('count-siswa-active').textContent = data.counts.siswa_active;
            document.getElementById('count-total-active').textContent = data.counts.total_active;

            data.users.forEach((user) => {
                const row = document.querySelector(`[data-user-id="${user.id}"]`);
                if (!row) return;
                const status = row.querySelector('[data-user-status]');
                status.textContent = user.is_active ? '● Aktif' : '○ Tidak aktif';
                status.className = `font-bold ${user.is_active ? 'text-green-700' : 'text-gray-500'}`;
                row.querySelector('[data-user-last-active]').textContent = user.last_active;
            });
        } catch (error) {
            console.error('Gagal memperbarui status pengguna.', error);
        }
    }

    refreshAdminMonitoring();
    setInterval(refreshAdminMonitoring, 30000);
</script>
</html>