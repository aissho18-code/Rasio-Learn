@php
    $user = auth()->user();
    $kelasName = optional(optional($user->siswaProfile)->kelas)->nama_kelas;
@endphp

<header class="flex items-center justify-end gap-5 px-8 py-5">
    {{-- Komponen Lonceng Notifikasi --}}
    <x-notification-bell />

    {{-- Profil Siswa --}}
    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-full border border-slate-100 bg-white px-3 py-1.5 shadow-xs transition hover:border-blue-300">
        @if ($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="h-9 w-9 rounded-full object-cover">
        @else
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif

        <div class="pr-1 text-left">
            <div class="text-xs font-bold text-slate-800">
                {{ $user->name }}
            </div>
            <div class="text-[10px] text-slate-400 font-medium">
                Siswa @if ($kelasName) • {{ $kelasName }} @endif
            </div>
        </div>
    </a>
</header>