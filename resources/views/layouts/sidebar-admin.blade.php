<aside class="w-64 shrink-0 bg-[#0B132A] text-white flex flex-col justify-between p-4 min-h-screen">
    <div>
        <!-- Logo Portal Admin -->
        <div class="mb-8 flex items-center gap-3 px-3 py-4">
            <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-10 w-auto object-contain">
            <div>
                <div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-blue-400">
                    Portal Admin
                </div>
            </div>
        </div>

        <nav class="space-y-1.5">
            @php
                $adminLearningActive = request()->routeIs('admin.materi.*', 'admin.aktivitas.*', 'admin.tugas.*', 'admin.quiz.*', 'admin.exams.*');
                $adminUsersActive = request()->routeIs('admin.users*');
            @endphp

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">🏠</span><span>Dashboard</span>
            </a>

            <details class="group" @if ($adminUsersActive) open @endif>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 rounded-xl px-4 py-3 text-xs font-semibold transition-all duration-200 {{ $adminUsersActive ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="flex items-center gap-3"><span class="text-base">👥</span><span>Kelola Pengguna</span></span>
                    <span aria-hidden="true">⌄</span>
                </summary>
                <div class="ml-5 mt-1 space-y-1 border-l border-slate-700 pl-3">
                    <a href="{{ route('admin.users.index', ['role' => 'guru']) }}" class="block rounded-lg px-3 py-2 text-xs {{ request('role') === 'guru' ? 'bg-white text-slate-900 font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">Guru</a>
                    <a href="{{ route('admin.users.index', ['role' => 'siswa']) }}" class="block rounded-lg px-3 py-2 text-xs {{ request('role') === 'siswa' ? 'bg-white text-slate-900 font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">Siswa</a>
                </div>
            </details>

            <a href="{{ route('admin.kelas.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.kelas*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">🏫</span><span>Kelola Kelas</span>
            </a>

            <details class="group" @if ($adminLearningActive) open @endif>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 rounded-xl px-4 py-3 text-xs font-semibold transition-all duration-200 {{ $adminLearningActive ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="flex items-center gap-3"><span class="text-base">📚</span><span>Kelola Pembelajaran</span></span>
                    <span aria-hidden="true">⌄</span>
                </summary>
                <div class="ml-5 mt-1 space-y-1 border-l border-slate-700 pl-3">
                    <a href="{{ route('admin.materi.index') }}" class="block rounded-lg px-3 py-2 text-xs {{ request()->routeIs('admin.materi.*') ? 'bg-white text-slate-900 font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">Materi</a>
                  <a href="{{ route('admin.lkpd.index') }}" class="block rounded-lg px-3 py-2 text-xs {{ request()->routeIs('admin.lkpd.*') ? 'bg-white text-slate-900 font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">LKPD</a>
                    <a href="{{ route('admin.tugas.index') }}" class="block rounded-lg px-3 py-2 text-xs {{ request()->routeIs('admin.tugas.*') ? 'bg-white text-slate-900 font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">Tugas</a>
                    <a href="{{ route('admin.quiz.index', ['model' => 'quiz_interactive']) }}" class="block rounded-lg px-3 py-2 text-xs {{ request()->routeIs('admin.quiz.*') || request()->routeIs('admin.exams.*') ? 'bg-white text-slate-900 font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">Quiz/Ujian</a>
                </div>
            </details>

            <a href="{{ route('diskusi.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-semibold transition-all duration-200 {{ request()->routeIs('diskusi*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">💬</span><span>Forum Diskusi</span>
            </a>
            <a href="{{ route('admin.proctoring') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.proctoring*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">🛡️</span><span>Proctoring CBT</span>
            </a>
            <a href="{{ route('admin.monitoring.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.monitoring.*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">📊</span><span>Monitoring &amp; Laporan</span>
            </a>
            <a href="{{ route('admin.pengumuman.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.pengumuman.*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                <span class="text-base">📢</span><span>Pengumuman</span>
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