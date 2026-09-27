@php
    $user = auth()->user();
    $kelasId = optional($user->siswaProfile)->kelas_id;

    $studentAnnouncements = \App\Models\Pengumuman::query()
        ->where(function ($query) use ($kelasId) {
            $query->whereNull('kelas_id')
                ->orWhere('kelas_id', $kelasId);
        })
        ->with([
            'reads' => function ($query) use ($user) {
                $query->where('siswa_id', $user->id);
            },
        ])
        ->latest('diterbitkan_at')
        ->latest()
        ->limit(5)
        ->get()
        ->map(function ($item) {
            $read = $item->reads->first();
            $item->is_read = $read?->read_at !== null;
            return $item;
        });

    $unreadAnnouncements = $studentAnnouncements->where('is_read', false)->count();
@endphp

<section class="rounded-2xl border border-slate-100 bg-white shadow-xs p-5 space-y-4">
    <!-- HEADER -->
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2.5">
            <span class="text-xl">📣</span>
            <div>
                <h2 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                    Pengumuman
                    @if ($unreadAnnouncements > 0)
                        <span class="rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white">
                            {{ $unreadAnnouncements }} Baru
                        </span>
                    @endif
                </h2>
                <p class="text-[11px] text-slate-400">Informasi terbaru dari guru Anda.</p>
            </div>
        </div>

        @if ($unreadAnnouncements > 0)
            <form method="POST" action="{{ route('siswa.pengumuman.read-all') }}">
                @csrf
                <button type="submit" class="text-xs font-bold text-blue-600 hover:underline cursor-pointer">
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    <!-- LIST PENGUMUMAN -->
    <div class="space-y-2.5 max-h-[400px] overflow-y-auto pr-1">
        @forelse ($studentAnnouncements as $announcement)
            <a href="{{ route('siswa.pengumuman.show', $announcement->id) }}"
               class="group relative block rounded-xl border p-3.5 transition-all {{ !$announcement->is_read ? 'border-blue-200 bg-blue-50/40 shadow-2xs hover:border-blue-400' : 'border-slate-100 bg-slate-50/50 hover:bg-slate-100/60' }}">
                
                @if (!$announcement->is_read)
                    <span class="absolute right-3 top-3 h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                @endif

                <div class="space-y-1">
                    <h3 class="text-xs {{ !$announcement->is_read ? 'font-bold text-slate-900' : 'font-semibold text-slate-600' }}">
                        {{ $announcement->judul }}
                    </h3>
                    <p class="text-[11px] line-clamp-2 {{ !$announcement->is_read ? 'text-slate-700' : 'text-slate-400' }} leading-relaxed">
                        {{ $announcement->isi }}
                    </p>
                    <div class="text-[10px] text-slate-400 pt-1">
                        🗓 {{ optional($announcement->diterbitkan_at ?? $announcement->created_at)->format('d M Y, H:i') }}
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-6 text-xs text-slate-400 italic">
                Belum ada pengumuman untuk Anda.
            </div>
        @endforelse
    </div>

    <!-- FOOTER -->
    <div class="border-t border-slate-100 pt-2 text-center">
        <a href="{{ route('siswa.pengumuman.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">
            Lihat Semua Pengumuman →
        </a>
    </div>
</section>