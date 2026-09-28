<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Pengguna - Portal Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    <!-- SIDEBAR ADMIN -->
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
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
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

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
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
        

        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">Manajemen Pengguna</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola akun pengguna (Admin, Guru, dan Siswa) dalam sistem.</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm">
                    + Buat Pengguna Baru
                </a>
            </div>

            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <!-- TABEL DAFTAR PENGGUNA -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-200/80">
                    <h3 class="font-bold text-gray-800 text-sm">Semua Pengguna Terdaftar</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-100/70 text-gray-600 font-semibold uppercase text-[10px] tracking-wider border-b border-gray-200">
                                <th class="p-4">Nama Lengkap</th>
                                <th class="p-4">Email</th>
                                <th class="p-4">Password</th>
                                <th class="p-4">Role / Hak Akses</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $u)
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="p-4 font-bold text-gray-800">{{ $u->name }}</td>
                                    <td class="p-4 text-gray-600">{{ $u->email }}</td>
                                    <td class="p-4">
                                        @if(!empty($u->plain_password))
                                            <div class="flex items-center gap-2">
                                                <span id="pwd-mask-{{ $u->id }}" class="text-gray-400 font-mono tracking-widest text-xs">••••••••</span>
                                                <span id="pwd-plain-{{ $u->id }}" class="hidden text-blue-600 font-mono text-xs font-semibold">{{ $u->plain_password }}</span>
                                                <button type="button" onclick="togglePassword('{{ $u->id }}')" class="text-gray-400 hover:text-blue-600 focus:outline-none transition" title="Lihat/Sembunyikan Password">
                                                    <svg id="eye-icon-{{ $u->id }}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-amber-700 bg-amber-50 px-2 py-1 rounded-md font-medium border border-amber-200" title="Akun lama/bawaan seeder. Edit pengguna untuk menyetel password baru.">
                                                🔒 Terenkripsi (Reset via Edit)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                            {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($u->role === 'guru' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                            {{ $u->role }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center space-x-2">
                                        <a href="{{ route('admin.users.edit', $u) }}" class="text-blue-600 font-semibold hover:underline">Edit</a>
                                        @if($u->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $u) }}" method="post" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                                @csrf @method('DELETE')
                                                <button class="text-red-600 font-semibold hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-gray-400 italic">Belum ada pengguna terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </main>

    <script>
        function togglePassword(id) {
            const maskSpan = document.getElementById('pwd-mask-' + id);
            const plainSpan = document.getElementById('pwd-plain-' + id);
            
            if (maskSpan.classList.contains('hidden')) {
                maskSpan.classList.remove('hidden');
                plainSpan.classList.add('hidden');
            } else {
                maskSpan.classList.add('hidden');
                plainSpan.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>