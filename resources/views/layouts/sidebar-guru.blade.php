<aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
    <div class="flex flex-col h-full overflow-y-auto">
        <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
            <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
            <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Portal Guru</span>
        </div>

        <nav class="px-4 py-6 space-y-1.5 flex-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                <span class="text-base">🏠</span><span>Dashboard</span>
            </a>

            @if (Route::has('guru.lkpd.index'))
                <a href="{{ route('guru.lkpd.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.lkpd.*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📋</span><span>Kelola LKPD</span>
                </a>
            @endif

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

            @if (Route::has('guru.refleksi.index'))
                <a href="{{ route('guru.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.refleksi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💭</span><span>Kelola Refleksi</span>
                </a>
            @endif
        </nav>

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
