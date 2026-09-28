<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Materi Pembelajaran - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    @include('layouts.sidebar-siswa-compact')

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <x-dashboard-header />

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">📚 Materi Pembelajaran</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pilih dan pelajari modul materi pembelajaran sesuai jadwal Anda.</p>
            </div>

            <!-- Alert Peringatan jika Materi Terkunci -->
            @if(session('error'))
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold shadow-sm">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            @forelse($groupedMateris as $namaMapel => $items)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="text-lg font-extrabold text-gray-900 uppercase tracking-wide">
                            {{ $namaMapel }}
                        </h3>
                        <span class="text-xs font-semibold px-3 py-1 bg-blue-50 text-blue-600 rounded-full">
                            {{ $items->count() }} Materi
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($items as $index => $materi)
                            @php
                                // Cek status lock real-time dari tabel Materi & MateriProgress
                                $prog = $progresses[$materi->id] ?? null;
                                $isLocked = (isset($materi->is_locked) && $materi->is_locked) || 
                                           in_array($materi->status, ['terkunci', 'locked'], true) ||
                                           ($prog && $prog->status === 'locked');
                            @endphp

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-blue-100 transition bg-gray-50/50 gap-4">
                                <div class="flex items-start space-x-4">
                                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm shrink-0 mt-1 sm:mt-0">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                            Format: Modul Pembelajaran
                                        </span>
                                        <h4 class="text-base font-bold text-gray-800">{{ $materi->judul }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($materi->deskripsi ?? 'Silahkan pelajari materi berikut.', 80) }}</p>
                                    </div>
                                </div>

                                <!-- BADGE STATUS & TOMBOL LIHAT MATERI -->
                                <div class="flex items-center space-x-3 self-end sm:self-center">
                                    @if($isLocked)
                                        <!-- MUNCUL STATUS TERKUNCI -->
                                        <span class="px-3 py-1 text-xs font-bold text-red-600 bg-red-50 border border-red-200 rounded-full flex items-center gap-1">
                                            🔒 Terkunci
                                        </span>
                                        <button disabled class="px-4 py-2 bg-gray-200 text-gray-400 font-bold text-xs rounded-xl cursor-not-allowed shadow-none">
                                            Akses Ditutup
                                        </button>
                                    @else
                                        <!-- MUNCUL STATUS TERSEDIA -->
                                        <span class="px-3 py-1 text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-full flex items-center gap-1">
                                            🔓 Tersedia
                                        </span>
                                        <a href="{{ route('siswa.materi.show', $materi->id) }}" 
                                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1">
                                            Lihat Materi →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center text-gray-400 text-xs">
                    Belum ada materi pembelajaran yang diunggah.
                </div>
            @endforelse

        </div>
    </main>

</body>
</html>