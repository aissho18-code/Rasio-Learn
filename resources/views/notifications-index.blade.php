<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Semua Notifikasi - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 min-h-screen p-6 md:p-10 flex flex-col items-center select-none">

    <div class="w-full max-w-4xl space-y-6">
        
        <!-- HEADER PAGE -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>🔔</span> Semua Notifikasi
                </h1>
                <p class="text-xs text-slate-500 mt-1">Riwayat pemberitahuan dan aktivitas akun Anda.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2 rounded-xl transition">
                ← Kembali ke Dashboard
            </a>
        </div>

        <!-- LIST NOTIFIKASI -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden divide-y divide-slate-100">
            @forelse ($notifications as $notification)
                @php
                    $data = $notification->data;
                @endphp
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="{{ $notification->read_at ? '' : 'bg-blue-50/40' }}">
                    @csrf
                    <button type="submit" class="w-full text-left p-5 flex items-start gap-4 transition hover:bg-slate-50">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 font-bold flex items-center justify-center shrink-0">
                            🔔
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="text-xs font-bold text-slate-800">{{ $data['title'] ?? 'Notifikasi' }}</h3>
                                <span class="text-[11px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">{{ $data['message'] ?? '' }}</p>
                        </div>
                    </button>
                </form>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    Belum ada notifikasi.
                </div>
            @endforelse
        </div>

        <!-- PAGINASI -->
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>

    </div>

</body>
</html>