<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kelola Refleksi - Portal Guru</title>

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

    @include('layouts.sidebar-guru')

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            <x-notification-bell />

            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Guru Pengajar' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Guru' }}</div>
                </div>
            </a>
        </header>

        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">Kelola Refleksi Pembelajaran</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Buat instrumen refleksi agar dapat diisi oleh siswa pada tiap pertemuan.</p>
                </div>
                <a href="{{ route('guru.refleksi.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm transition">
                    ➕ Buat Refleksi Baru
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($refleksis as $r)
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4 flex flex-col justify-between hover:shadow-md transition">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-lg text-[10px]">
                                    📌 Pertemuan Ke-{{ $r->pertemuan }}
                                </span>
                                <span class="text-[11px] text-gray-500 font-semibold">
                                    📥 {{ $r->submissions_count }} Siswa Mengisi
                                </span>
                            </div>

                            <h3 class="font-bold text-gray-800 text-sm">{{ $r->judul_refleksi }}</h3>
                            <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                {{ $r->deskripsi ?? 'Refleksi pembelajaran untuk siswa.' }}
                            </p>
                        </div>

                        <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                            <a href="{{ route('guru.refleksi.detail', $r->id) }}" class="bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs px-3.5 py-2 rounded-xl transition">
                                📊 Rekap Siswa
                            </a>
                            <form action="{{ route('guru.refleksi.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus refleksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold px-2 py-1">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center text-gray-400 text-xs italic">
                        Belum ada refleksi yang dibuat. Klik "Buat Refleksi Baru" untuk menambahkan refleksi pertemuan siswa.
                    </div>
                @endforelse
            </div>
        </div>
    </main>
</body>
</html>