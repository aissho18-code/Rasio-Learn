<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manajemen Materi Pembelajaran - Portal Guru</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
        [x-cloak] { display: none !important; }

        /* Style Khusus Rendering Markdown */
        .materi-content { color: #334155; font-size: 0.8rem; line-height: 1.6; }
        .materi-content h2 { font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-top: 0.75rem; margin-bottom: 0.35rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.2rem; }
        .materi-content h3 { font-size: 0.875rem; font-weight: 700; color: #1e293b; margin-top: 0.5rem; margin-bottom: 0.25rem; }
        .materi-content p { margin-bottom: 0.5rem; }
        .materi-content ul { list-style-type: disc; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .materi-content ol { list-style-type: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .materi-content blockquote { border-left: 3px solid #3b82f6; background-color: #eff6ff; padding: 0.5rem 0.75rem; border-radius: 0.5rem; margin: 0.5rem 0; color: #1e40af; font-size: 0.75rem; }
        .materi-content table { width: 100%; border-collapse: collapse; margin: 0.5rem 0; font-size: 0.75rem; }
        .materi-content th, .materi-content td { border: 1px solid #cbd5e1; padding: 0.35rem 0.5rem; text-align: left; }
        .materi-content th { background-color: #f1f5f9; font-weight: 700; }
        .materi-content img { max-width: 100%; border-radius: 0.5rem; margin: 0.5rem 0; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    <!-- SIDEBAR GURU -->
    <aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
        <div class="flex flex-col h-full overflow-y-auto">
            
            <!-- LOGO HEADER -->
            <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
                <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
                <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Portal Guru</span>
            </div>

            <!-- MENU SIDEBAR GURU -->
            <nav class="px-4 py-6 space-y-1.5 flex-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                
                @if (Route::has('guru.aktivitas.index'))
                <a href="{{ route('guru.lkpd.index') }}" 
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('guru.lkpd.*') ? 'bg-blue-100 text-blue-700' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span class="text-lg">📋</span>
                        <span>Kelola LKPD</span>
                </a>
                @endif

                <a href="{{ route('guru.materi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.materi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📖</span><span>Kelola Materi</span>
                </a>

                <a href="{{ route('guru.tugas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.tugas*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📋</span><span>Kelola Tugas</span>
                </a>

                <a href="{{ route('guru.ujian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.ujian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📝</span><span>Kelola Ujian/Kuis</span>
                </a>

                <a href="{{ route('guru.penilaian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.penilaian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📊</span><span>Penilaian & Evaluasi</span>
                </a>

                <a href="{{ route('guru.presensi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.presensi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📅</span><span>Rekap Presensi Siswa</span>
                </a>

                <a href="{{ route('diskusi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('diskusi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💬</span><span>Forum Diskusi</span>
                </a>

                <a href="{{ route('guru.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.refleksi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💭</span><span>Kelola Refleksi</span>
                </a>
            </nav>

            <!-- LOGOUT -->
            <div class="p-4 border-t border-slate-800/60">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 text-xs font-semibold transition cursor-pointer">
                        <span class="text-base">↪</span><span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            <x-notification-bell />

            <div class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👨‍🏫</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Ibu Guru Matematika' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Guru' }}</div>
                </div>
            </div>
        </header>

        <!-- CONTAINER KONTEN UTAMA -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- HEADER HALAMAN -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">Manajemen Materi Pembelajaran</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola modul materi, atur jadwal pertemuan/pekan, serta kontrol akses materi siswa.</p>
                </div>

                <button type="button" onclick="focusFormTambah()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition cursor-pointer">
                    <span>➕</span> Tambah Materi Baru
                </button>
            </div>

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                @php 
                    $errorList = $errors->all(); 
                @endphp
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errorList as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- LAYOUT UTAMA: EDITOR (KIRI) & DAFTAR MATERI (KANAN) -->
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.3fr_0.9fr]">

                <!-- EDITOR MATERI (KIRI) -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- HEADER EDITOR DENGAN TAB WRITE / PREVIEW -->
                        <div class="flex items-center justify-between border-b border-gray-100 bg-slate-50/80 px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-base">📖</span>
                                <h3 class="font-bold text-gray-800 text-sm">Editor Modul Materi</h3>
                            </div>

                            <div class="flex rounded-lg bg-slate-200 p-1 text-xs font-semibold">
                                <button type="button" id="tab-write-btn" onclick="switchTab('write')" class="rounded-md px-3 py-1 bg-white text-slate-800 shadow-xs transition cursor-pointer">
                                    Ketikan
                                </button>
                                <button type="button" id="tab-preview-btn" onclick="switchTab('preview')" class="rounded-md px-3 py-1 text-slate-600 hover:text-slate-900 transition cursor-pointer">
                                    Preview
                                </button>
                            </div>
                        </div>

                        <!-- TAB WRITE (FORM TAMBAH MATERI) -->
                        <div id="tab-write-content" class="p-6">
                            <form action="{{ route('guru.materi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Pekan / Pertemuan Ke:</label>
                                        <input type="text" id="input_pekan" name="pekan" required placeholder="Contoh: Pekan ke-1" class="w-full text-xs border border-gray-200 rounded-xl p-2.5 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Materi:</label>
                                        <input type="text" id="input_judul" name="judul" required placeholder="Contoh: Konsep Dasar Rasio" class="w-full text-xs border border-gray-200 rounded-xl p-2.5 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-1.5 flex items-center justify-between">
                                        <label class="block text-[11px] font-bold text-gray-700">Keterangan / Deskripsi / Konten:</label>
                                        <span class="text-[10px] font-semibold text-blue-600 uppercase">Format Otomatis</span>
                                    </div>

                                    <!-- SHORTCUT TOOLBAR -->
                                    <div class="mb-2 flex flex-wrap gap-1.5 rounded-t-xl border border-b-0 border-gray-200 bg-slate-100 p-2">
                                        <button type="button" onclick="insertFormatting('h2')" class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50 cursor-pointer">
                                            📌 Sub Judul
                                        </button>
                                        <button type="button" onclick="insertFormatting('table')" class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50 cursor-pointer">
                                            📊 Tabel
                                        </button>
                                        <button type="button" onclick="insertFormatting('callout')" class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50 cursor-pointer">
                                            💡 Catatan Info
                                        </button>
                                        <button type="button" onclick="insertFormatting('image')" class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50 cursor-pointer">
                                            🖼️ Gambar
                                        </button>
                                        <button type="button" onclick="insertFormatting('list')" class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50 cursor-pointer">
                                            • Poin List
                                        </button>
                                    </div>

                                    <textarea id="input_konten" name="konten" rows="8" oninput="updatePreview()" placeholder="Tuliskan instruksi, materi, atau ringkasan..." class="w-full text-xs font-mono border border-gray-200 rounded-b-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none leading-relaxed"></textarea>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Dokumen Materi (PDF / Word / PPT):</label>
                                    <input type="file" name="file_materi" class="w-full text-[11px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                </div>

                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 rounded-xl shadow-sm transition cursor-pointer">
                                    Publikasikan Materi 🚀
                                </button>
                            </form>
                        </div>

                        <!-- TAB PREVIEW -->
                        <div id="tab-preview-content" class="hidden p-6 space-y-4">
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <span id="preview-pekan" class="inline-flex rounded-full bg-purple-100 px-2.5 py-0.5 text-[10px] font-bold text-purple-700">
                                    Pekan ke-1
                                </span>
                                <h3 id="preview-judul" class="mt-2 text-base font-extrabold text-slate-900">
                                    Judul Pratinjau Materi
                                </h3>
                            </div>

                            <div id="preview-body" class="text-xs leading-relaxed text-slate-700 space-y-3 pt-2 font-sans">
                                <!-- Hasil Preview JS -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DAFTAR MATERI MODUL (KANAN) -->
                @php
                    $dikelolaMateri = $materiList ?? $materis ?? $materi ?? [];
                @endphp

                <div class="space-y-4">
                    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="font-bold text-gray-800 text-sm">
                                Daftar Modul Materi ({{ count($dikelolaMateri) }})
                            </h3>
                            <span class="bg-blue-50 text-blue-700 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                Tersedia
                            </span>
                        </div>

                        <div class="space-y-3 max-h-[620px] overflow-y-auto pr-1">
                            @forelse ($dikelolaMateri as $materi)
                                <div class="bg-white rounded-2xl border border-gray-200/80 p-4 shadow-2xs hover:shadow-md transition space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="bg-purple-100 text-purple-700 font-bold px-2.5 py-0.5 rounded-lg text-[10px]">
                                            📅 {{ $materi->pekan ?? 'Pekan Umum' }}
                                        </span>

                                        @if (($materi->status ?? 'aktif') === 'aktif')
                                            <span class="bg-green-100 text-green-700 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                                🔓 Unlocked
                                            </span>
                                        @else
                                            <span class="bg-amber-100 text-amber-700 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                                🔒 Locked
                                            </span>
                                        @endif
                                    </div>

                                    <div>

                                    <div>
                                        <h4 class="font-bold text-gray-800 text-xs line-clamp-1">{{ $materi->judul }}</h4>
    
                                        <!-- GANTI {{ $materi->konten }} DENGAN TAG DILENGKAPI CLASS .materi-content -->
                                        <div class="materi-content line-clamp-3 mt-1">
                                            {!! $materi->rendered_konten ?? $materi->konten !!}
                                        </div>
    
                                        @if($materi->file_path)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $materi->file_path) }}" target="_blank" class="text-[10px] font-bold text-blue-600 hover:underline inline-flex items-center gap-1">
                                                    <span>📄 Dokumen Pendukung</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    

                                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-xs">
                                        @if (Route::has('guru.materi.toggle-lock'))
                                        <form action="{{ route('guru.materi.toggle-lock', $materi->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="font-semibold text-gray-600 hover:text-blue-600 transition flex items-center gap-1 cursor-pointer">
                                                @if (($materi->status ?? 'aktif') === 'aktif')
                                                    <span>🔒 Kunci</span>
                                                @else
                                                    <span>🔓 Buka</span>
                                                @endif
                                            </button>
                                        </form>
                                        @else
                                        <span></span>
                                        @endif

                                        <div class="flex items-center space-x-3">
                                            <button type="button" onclick="openEditModal('{{ $materi->id }}', '{{ addslashes($materi->judul ?? '') }}', '{{ addslashes($materi->pekan ?? '') }}', '{{ addslashes($materi->konten ?? '') }}')" class="font-semibold text-blue-600 hover:underline cursor-pointer">
                                                Edit ✏️
                                            </button>

                                            <form action="{{ route('guru.materi.destroy', $materi->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-semibold text-red-600 hover:underline cursor-pointer">
                                                    Hapus 🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-8 text-center text-gray-400 text-xs italic">
                                    Belum ada modul materi pembelajaran yang dipublikasikan.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <!-- MODAL EDIT MATERI -->
    <div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="font-bold text-gray-800 text-sm">✏️ Edit Modul Materi Pembelajaran</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 font-bold text-sm cursor-pointer">✕</button>
            </div>

            <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Pekan / Pertemuan Ke:</label>
                    <input type="text" id="editPekan" name="pekan" required class="w-full text-xs border border-gray-200 rounded-xl p-2.5 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Materi:</label>
                    <input type="text" id="editJudul" name="judul" required class="w-full text-xs border border-gray-200 rounded-xl p-2.5 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Keterangan / Deskripsi Singkat:</label>
                    <textarea id="editKonten" name="konten" rows="4" class="w-full text-xs border border-gray-200 rounded-xl p-2.5 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none leading-relaxed"></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Ganti Dokumen Materi (Opsional):</label>
                    <input type="file" name="file_materi" class="w-full text-[11px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs px-4 py-2.5 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                        Simpan Perubahan 💾
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT JS INTERAKTIF -->
    <script>
        function switchTab(tab) {
            const writeContent = document.getElementById('tab-write-content');
            const previewContent = document.getElementById('tab-preview-content');
            const writeBtn = document.getElementById('tab-write-btn');
            const previewBtn = document.getElementById('tab-preview-btn');

            if (tab === 'write') {
                writeContent.classList.remove('hidden');
                previewContent.classList.add('hidden');

                writeBtn.className = "rounded-md px-3 py-1 bg-white text-slate-800 shadow-xs transition cursor-pointer";
                previewBtn.className = "rounded-md px-3 py-1 text-slate-600 hover:text-slate-900 transition cursor-pointer";
            } else {
                writeContent.classList.add('hidden');
                previewContent.classList.remove('hidden');

                previewBtn.className = "rounded-md px-3 py-1 bg-white text-blue-600 shadow-xs transition cursor-pointer";
                writeBtn.className = "rounded-md px-3 py-1 text-slate-600 hover:text-slate-900 transition cursor-pointer";

                updatePreview();
            }
        }

        function insertFormatting(type) {
            const textarea = document.getElementById('input_konten');
            let template = '';

            switch (type) {
                case 'h2':
                    template = '\n\n## Sub-Judul Bab Baru\nTuliskan uraian penjelasan di sini...\n';
                    break;
                case 'table':
                    template = '\n\n| Kolom A | Kolom B | Kolom C |\n| :--- | :---: | :--- |\n| Data 1 | Nilai X | Penjelasan |\n';
                    break;
                case 'callout':
                    template = '\n\n> [!INFO] Catatan Penting\nTuliskan poin penting yang wajib diingat siswa di sini.\n';
                    break;
                case 'image':
                    template = '\n\n![Deskripsi Gambar](https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=800&q=80)\n*Gambar: Keterangan singkat ilustrasi materi.*\n';
                    break;
                case 'list':
                    template = '\n\n- Poin materi kesatu\n- Poin materi kedua\n- Poin materi ketiga\n';
                    break;
            }

            textarea.value += template;
            textarea.focus();
        }

        function updatePreview() {
            const pekan = document.getElementById('input_pekan').value || 'Pekan ke-1';
            const judul = document.getElementById('input_judul').value || 'Judul Pratinjau Materi';
            const raw = document.getElementById('input_konten').value || 'Belum ada konten materi yang diketik.';

            document.getElementById('preview-pekan').innerText = pekan;
            document.getElementById('preview-judul').innerText = judul;

            const formatted = raw.split('\n\n').map(p => `<p class="mb-2">${p.replace(/\n/g, '<br>')}</p>`).join('');
            document.getElementById('preview-body').innerHTML = formatted;
        }

        function focusFormTambah() {
            switchTab('write');
            document.getElementById('input_pekan').focus();
        }

        function openEditModal(id, judul, pekan, konten) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            
            document.getElementById('editJudul').value = judul;
            document.getElementById('editPekan').value = pekan;
            document.getElementById('editKonten').value = konten;
            
            form.action = '/guru/materi/' + id;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>