<aside class="w-64 shrink-0 bg-[#0B132A] text-white flex flex-col justify-between p-4 min-h-screen">
    <div>
        <!-- Logo Portal Admin -->
        <div class="mb-8 flex items-center gap-3 px-3 py-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-xl shadow-md">
                🎓
            </div>
            <div>
                <div class="text-sm font-extrabold leading-tight">Ratio Learn</div>
                <div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-blue-400">
                    Portal Admin
                </div>
            </div>
        </div>

        <!-- Menu Navigasi -->
        <nav class="space-y-1.5">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">🏠</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.users*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">👥</span>
                <span>Kelola Pengguna</span>
            </a>

            <a href="{{ route('admin.kelas.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.kelas*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">🏫</span>
                <span>Kelola Kelas</span>
            </a>

            <a href="{{ route('admin.exams.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.exams*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">📝</span>
                <span>Kelola Ujian</span>
            </a>

            <a href="{{ route('admin.proctoring') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.proctoring*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">🛡️</span>
                <span>Proctoring CBT</span>
            </a>

            <a href="{{ route('diskusi.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('diskusi*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">💬</span>
                <span>Forum Diskusi</span>
            </a>
        </nav>
    </div>

    <!-- Tombol Logout -->
    <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-slate-800/80">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold text-slate-400 hover:text-red-400 hover:bg-slate-800/50 rounded-xl transition-all duration-200 cursor-pointer">
            <span class="text-base">↪️</span>
            <span>Logout</span>
        </button>
    </form>
</aside>