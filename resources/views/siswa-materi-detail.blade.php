<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ruang Belajar - {{ $materi->judul }} - Ratio Learn</title>

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

    <!-- SIDEBAR SISWA -->
    @include('layouts.sidebar-siswa-compact')

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <x-dashboard-header />

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-5xl">
            
            <!-- PAGE TITLE HEADER & TOMBOL KEMBALI -->
            <div>
                <a href="{{ route('siswa.materi.index') }}" class="text-xs font-bold text-blue-600 hover:underline mb-1 inline-block">&larr; Kembali ke Daftar Materi</a>
                <h1 class="text-xl font-extrabold text-slate-900">Ruang Belajar - {{ $materi->judul }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pelajari materi rasio dan kerjakan tugas latihan di bawah ini.</p>
            </div>

            <!-- KONTAINER UTAMA -->
            <div class="space-y-6">
                
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                        ✨ {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                        ⚠️ {{ session('error') }}
                    </div>
                @endif

                <!-- KONTEN MATERI -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 space-y-4">
                    <div class="flex items-center space-x-2">
                        <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                            Matematika: Rasio
                        </span>
                        <span class="text-xs text-gray-400">| Kelas: {{ $materi->kelas->nama_kelas ?? 'Umum' }}</span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-800">
    {{ $materi->judul }}
</h3>

<div class="border-t border-gray-100 pt-5">
    <div class="text-xs leading-7 text-gray-700 whitespace-pre-line">
        {{ $materi->konten }}
    </div>
</div>

                    <!-- TOMBOL UNDUH DOKUMEN MATERI (PDF/Word/PPT) -->
                    @if($materi->file_path)
                        <div class="pt-4 border-t border-gray-100">
                            <a href="{{ asset('storage/' . $materi->file_path) }}" target="_blank" class="inline-flex items-center space-x-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs px-4 py-2.5 rounded-xl transition border border-blue-200">
                                <span>📥 Unduh / Lihat Dokumen Materi (PDF/Word/PPT)</span>
                            </a>
                        </div>
                    @endif
                </div>

               <div class="pt-2">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline text-xs font-semibold">&larr; Kembali ke Dashboard</a>
                </div>

            </div>

        </div>
    </main>

</body>
</html>