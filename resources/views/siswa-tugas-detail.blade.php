<!DOCTYPE html>












<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Detail Tugas Pembelajaran - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    <!-- SIDEBAR SISWA PRESISI -->
    @include('layouts.sidebar-siswa-compact')

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR VERSI TERBARU -->
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            {{-- KOMPONEN LONCENG NOTIFIKASI DINAMIS --}}
            <x-notification-bell />

            {{-- PROFIL SISWA DENGAN INFORMASI KELAS --}}
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
                @if (auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                    </div>
                @endif

                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ auth()->user()->name ?? 'Siswa' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium">
                        Siswa @if (optional(optional(auth()->user()->siswaProfile)->kelas)->nama_kelas) • Kelas {{ optional(optional(auth()->user()->siswaProfile)->kelas)->nama_kelas }} @endif
                    </div>
                </div>
            </a>
        </header>

        <div class="px-8 pb-8 space-y-6 flex-1 max-w-4xl">
            <div>
                <a href="{{ route('siswa.tugas.index') }}" class="text-xs font-bold text-blue-600 hover:underline mb-1 inline-block">&larr; Kembali ke Daftar Tugas</a>
               <h1 class="text-xl font-extrabold text-slate-900">
    {{ $tugas->judul ?? 'Detail Tugas' }}
</h1>

<div class="mt-4 rounded-2xl border border-blue-100 bg-blue-50 p-5">
    <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">
        📖 Instruksi / Soal dari Guru
    </p>

    <div class="mt-2 text-sm leading-6 text-slate-700 whitespace-pre-line">
        {{ $tugas->deskripsi ?? 'Tidak ada instruksi tugas.' }}
    </div>
</div>

@if($tugas->file_path)
    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
            📎 Lampiran Tugas
        </p>

        <a href="{{ asset('storage/' . $tugas->file_path) }}"
           target="_blank"
           class="mt-3 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-700 transition">
            📥 Lihat / Unduh File Tugas
        </a>
    </div>
@endif
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-6">
                <form action="{{ route('siswa.tugas.submit', $tugas->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-1">Catatan / Jawaban Teks</label>
                            <textarea name="jawaban" rows="4" placeholder="Tuliskan jawaban atau catatan tugas di sini..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ old('jawaban', $submission->jawaban ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-1">Unggah Lampiran / File (Opsional)</label>
                            <input type="file" name="file_submission" class="w-full text-xs border border-gray-200 rounded-xl p-2.5 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <p class="text-[10px] text-gray-400 mt-1">Format yang didukung: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, ZIP, RAR (Maks. 20MB)</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-sm transition cursor-pointer">
                            🚀 Kirim Tugas
                        </button>
                        <a href="{{ route('siswa.tugas.index') }}" class="text-xs font-semibold text-gray-500 hover:underline">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>