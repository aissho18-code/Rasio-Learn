<aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
    <div class="flex flex-col h-full overflow-y-auto">
        <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
            <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
            <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Belajar Rasio Jadi Seru!</span>
        </div>

        <nav class="px-4 py-6 space-y-1.5 flex-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                <span class="text-base">🏠</span><span>Dashboard</span>
            </a>

            <a href="{{ route('siswa.materi.index', ['type' => 'aktivitas']) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.materi*') && request('type') == 'aktivitas' ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                <span class="text-base">🎮</span><span>Aktivitas</span>
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

            <a href="{{ route('siswa.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.refleksi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                <span class="text-base">💭</span><span>Refleksi</span>
            </a>
        </nav>

        <div class="px-6 py-4 opacity-20 text-[10px] text-blue-200 font-mono space-y-1 pointer-events-none">
            <div>a : b = c : d</div>
            <div class="text-right">2 : 3</div>
            <div class="text-center">4 : 6</div>
        </div>

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
