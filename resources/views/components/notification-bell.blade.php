@php
    $user = auth()->user();

    $notifications = $user
        ? $user->notifications()->latest()->limit(10)->get()
        : collect();

    $unreadCount = $user
        ? $user->unreadNotifications()->count()
        : 0;
@endphp

<div class="relative">
    {{-- Tombol Lonceng Utama --}}
    <button
        type="button"
        onclick="toggleNotificationDropdown()"
        aria-label="Buka notifikasi"
        class="relative flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-600 shadow-xs border border-slate-100 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-300 cursor-pointer"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        @if ($unreadCount > 0)
            <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white animate-pulse">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Backdrop Transparan untuk Klik di Luar --}}
    <div
        id="notif-backdrop"
        onclick="closeNotificationDropdown()"
        class="hidden fixed inset-0 z-[998]"
    ></div>

    {{-- Panel Dropdown Notifikasi --}}
    <div
        id="notif-dropdown"
        class="hidden absolute right-0 z-[999] mt-3 w-[calc(100vw-2rem)] max-w-[380px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
    >
        {{-- Header Panel --}}
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="text-base">🔔</span>
                <h3 class="text-xs font-bold text-slate-800">
                    Notifikasi
                </h3>

                @if ($unreadCount > 0)
                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-600">
                        {{ $unreadCount }} baru
                    </span>
                @endif
            </div>

            {{-- Tombol Tutup (X) --}}
            <button
                type="button"
                onclick="closeNotificationDropdown()"
                aria-label="Tutup notifikasi"
                class="flex h-7 w-7 items-center justify-center rounded-full text-lg font-bold leading-none text-slate-400 transition hover:bg-slate-200 hover:text-slate-700 focus:outline-none cursor-pointer"
            >
                &times;
            </button>
        </div>

        {{-- Action Tandai Semua Dibaca --}}
        @if ($unreadCount > 0)
            <div class="flex justify-end border-b border-slate-100 px-4 py-2 bg-slate-50/50">
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button
                        type="submit"
                        class="text-[11px] font-semibold text-blue-600 hover:underline cursor-pointer"
                    >
                        Tandai semua dibaca
                    </button>
                </form>
            </div>
        @endif

        {{-- Isi Daftar Notifikasi --}}
        <div class="max-h-80 divide-y divide-slate-100 overflow-y-auto">
            @forelse ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $type = $data['type'] ?? 'default';

                    $icon = match ($type) {
                        'login' => '↪',
                        'assignment' => '✓',
                        'activity' => '▣',
                        'exam' => '✎',
                        'material' => '▤',
                        'discussion' => '☷',
                        'feedback' => '★',
                        'deadline' => '◷',
                        'system' => '⚙',
                        default => '•',
                    };

                    $iconClass = match ($type) {
                        'login' => 'bg-blue-50 text-blue-600',
                        'assignment' => 'bg-green-50 text-green-600',
                        'activity' => 'bg-purple-50 text-purple-600',
                        'exam' => 'bg-red-50 text-red-600',
                        'material' => 'bg-cyan-50 text-cyan-700',
                        'discussion' => 'bg-amber-50 text-amber-700',
                        'feedback' => 'bg-emerald-50 text-emerald-700',
                        'deadline' => 'bg-orange-50 text-orange-700',
                        'system' => 'bg-slate-100 text-slate-700',
                        default => 'bg-slate-50 text-slate-600',
                    };
                @endphp

                <form
                    method="POST"
                    action="{{ route('notifications.read', $notification->id) }}"
                >
                    @csrf
                    <button
                        type="submit"
                        class="{{ $notification->read_at ? '' : 'bg-blue-50/40' }} flex w-full items-start gap-3 p-3 text-left transition hover:bg-slate-50 cursor-pointer"
                    >
                        <span class="{{ $iconClass }} flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-xs font-bold">
                            {{ $icon }}
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="flex items-start justify-between gap-2">
                                <span class="text-xs font-bold text-slate-800">
                                    {{ $data['title'] ?? 'Notifikasi' }}
                                </span>

                                @if (!$notification->read_at)
                                    <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                                @endif
                            </span>

                            <span class="mt-0.5 block line-clamp-2 text-[11px] leading-relaxed text-slate-500">
                                {{ $data['message'] ?? '' }}
                            </span>

                            <span class="mt-1 block text-[10px] text-slate-400">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </span>
                    </button>
                </form>
            @empty
                <div class="px-5 py-8 text-center">
                    <div class="mb-2 text-2xl">🔕</div>
                    <p class="text-xs text-slate-400">
                        Belum ada notifikasi saat ini.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Footer Panel --}}
        <div class="border-t border-slate-100 bg-slate-50 p-2.5 text-center">
            <a
                href="{{ route('notifications.index') }}"
                onclick="closeNotificationDropdown()"
                class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline"
            >
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>
</div>

<script>
    function toggleNotificationDropdown() {
        const dropdown = document.getElementById('notif-dropdown');
        const backdrop = document.getElementById('notif-backdrop');
        if (dropdown && backdrop) {
            dropdown.classList.toggle('hidden');
            backdrop.classList.toggle('hidden');
        }
    }

    function closeNotificationDropdown() {
        const dropdown = document.getElementById('notif-dropdown');
        const backdrop = document.getElementById('notif-backdrop');
        if (dropdown && backdrop) {
            dropdown.classList.add('hidden');
            backdrop.classList.add('hidden');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNotificationDropdown();
        }
    });
</script>