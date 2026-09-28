@php
    $user = auth()->user();
    $roleName = $user->role === 'guru'
        ? 'Guru'
        : ($user->role === 'siswa' ? 'Siswa' : 'Admin');

    $kelasName = null;
    if ($roleName === 'Siswa') {
        $kelasName = optional($user->siswaProfile)->kelas->nama_kelas ?? null;
    }
@endphp

<!-- HEADER TOP BAR REUSABLE -->
<header class="px-8 py-4 flex items-center justify-end gap-5">
    <!-- KOMPONEN LONCENG NOTIFIKASI -->
    <x-notification-bell />

    <!-- PROFILE ITEM -->
    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
        @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover">
        @else
            <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
        <div class="text-left leading-tight pr-1">
            <div class="text-xs font-bold text-slate-800">{{ $user->name }}</div>
            <div class="text-[10px] text-slate-400 font-medium capitalize">
                @if($roleName === 'Siswa')
                    Siswa 
                    @if($kelasName)
                        • Kelas {{ $kelasName }}
                    @else
                        • <span class="text-red-500 font-semibold">Belum masuk kelas</span>
                    @endif
                @else
                    {{ $roleName }}
                @endif
            </div>
        </div>
    </a>
</header>