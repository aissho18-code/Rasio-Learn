<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manajemen Tugas Pembelajaran - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FLATPICKR CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
        .flatpickr-calendar {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border-radius: 1rem !important;
            border: 1px solid #e5e7eb !important;
        }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    @include('layouts.sidebar-guru')

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <x-dashboard-header />

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manajemen Tugas Pembelajaran</h1>
                <p class="text-xs text-slate-500 mt-0.5">Buat instruksi tugas harian, atur pekan, tenggat waktu, dan unggah berkas acuan.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- FORM TAMBAH TUGAS -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4 md:col-span-1">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-gray-800 text-sm">➕ Buat Tugas Baru</h3>
                        <p class="text-[11px] text-gray-500">Atur rincian tugas untuk siswa.</p>
                    </div>

                    <form action="{{ route('guru.tugas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Tugas:</label>
                            <input type="text" name="judul" required placeholder="Contoh: Tugas Kelompok Pertama" class="w-full text-xs border border-gray-200 rounded-xl p-2.5 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Pekan / Pertemuan:</label>
                            <input type="text" name="pekan" required placeholder="Contoh: Pekan ke-1" class="w-full text-xs border border-gray-200 rounded-xl p-2.5 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Tenggat Waktu (Format 24 Jam WIB):</label>
                            <input type="text" id="tenggatPicker" name="tenggat_waktu" required placeholder="Pilih Tanggal & Jam..." class="w-full text-xs border border-gray-200 rounded-xl p-2.5 bg-gray-50/50 font-bold text-blue-900 focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Instruksi / Keterangan Tugas:</label>
                            <textarea name="deskripsi" rows="4" placeholder="Kumpulkan Tugas Kelompok Pertama di sini ya..." class="w-full text-xs border border-gray-200 rounded-xl p-2.5 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Lampiran Dokumen Acuan (Opsional):</label>
                            <input type="file" name="file_tugas" class="w-full text-[11px] text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 rounded-xl shadow-sm transition">
                            Publikasikan Tugas 🚀
                        </button>
                    </form>
                </div>

                <!-- DAFTAR TUGAS AKTIF -->
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 content-start">
                    @forelse($tugasList ?? [] as $t)
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="bg-indigo-100 text-indigo-700 font-bold px-2.5 py-1 rounded-lg text-[10px]">
                                    📅 {{ $t->pekan ?? 'Pekan Umum' }}
                                </span>
                                <span class="text-[11px] text-gray-500 font-semibold">
                                    ⏰ Tenggat: {{ $t->tenggat_waktu ? \Carbon\Carbon::parse($t->tenggat_waktu)->format('d M Y, H:i') : '-' }}
                                </span>
                            </div>

                            <div class="space-y-1">
                                <h4 class="font-bold text-gray-800 text-sm">{{ $t->judul }}</h4>
                                <p class="text-xs text-gray-500 line-clamp-3">{{ $t->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                                @if($t->file_path)
                                    <a href="{{ asset('storage/' . $t->file_path) }}" target="_blank" class="text-[11px] font-bold text-blue-600 hover:underline pt-1 inline-block">
                                        📄 Lihat Dokumen Acuan
                                    </a>
                                @endif
                            </div>

                            <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-xs">
                                <span class="text-[11px] text-gray-400">Dibuat: {{ $t->created_at->format('d M Y') }}</span>

                                <form action="{{ route('guru.tugas.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-bold text-red-600 hover:underline">Hapus 🗑️</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center text-gray-400 text-xs">
                            Belum ada tugas yang dipublikasikan.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </main>

    <!-- FLATPICKR JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#tenggatPicker", {
                enableTime: true,
                time_24hr: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                defaultHour: 23,
                defaultMinute: 59,
            });
        });
    </script>
</body>
</html>