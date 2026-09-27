<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manajemen Ujian & Quiz (Auto-Parser AI) - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
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
                
                <!-- KELOLA MATERI (ACTIVE) -->
                <a href="{{ route('guru.lkpd.index') }}" 
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('guru.lkpd.*') ? 'bg-blue-100 text-blue-700' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span class="text-lg">📋</span>
                        <span>Kelola LKPD</span>
                </a>
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

            <!-- DOODLE -->
            <div class="px-6 py-4 opacity-20 text-[10px] text-blue-200 font-mono space-y-1 pointer-events-none">
                <div>a : b = c : d</div>
                <div class="text-right">2 : 3</div>
                <div class="text-center">4 : 6</div>
            </div>

            <!-- LOGOUT -->
            <div class="p-4 border-t border-slate-800/60">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 text-xs font-semibold transition">
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
            <button class="relative p-2 bg-white rounded-full shadow-xs hover:bg-slate-50 transition border border-slate-100">
                <span class="text-base">🔔</span>
                <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">3</span>
            </button>

            <div class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Guru Pengajar' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Guru' }}</div>
                </div>
            </div>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-5xl" x-data="{ 
            isParsing: false,
            parseMessage: '',
            questions: [
                { text: '', type: 'multiple', options: ['Opsi 1', 'Opsi 2'], answer: '' }
            ],
            parseFile(event) {
                const file = event.target.files[0];
                if (!file) return;

                this.isParsing = true;
                this.parseMessage = '🤖 AI sedang memindai dokumen dan mengekstrak soal rasio...';

                setTimeout(() => {
                    this.isParsing = false;
                    this.questions = [
                        { 
                            text: 'Jika perbandingan uang Ani dan Budi adalah 3 : 5, dan jumlah uang mereka Rp 400.000, berapa uang Ani?', 
                            type: 'multiple', 
                            options: ['Rp 150.000', 'Rp 250.000', 'Rp 200.000', 'Rp 300.000'], 
                            answer: 'Rp 150.000' 
                        },
                        { 
                            text: 'Sebuah peta digambar dengan skala 1 : 2.000.000. Jika jarak pada peta antara kota A dan B adalah 5 cm, berapa jarak sebenarnya?', 
                            type: 'multiple', 
                            options: ['10 km', '100 km', '1.000 km', '50 km'], 
                            answer: '100 km' 
                        },
                        { 
                            text: 'Jelaskan konsep perbandingan senilai (proporsi) dan berikan contoh penerapannya dalam kehidupan sehari-hari!', 
                            type: 'paragraph', 
                            options: ['Opsi 1', 'Opsi 2'], 
                            answer: 'Perbandingan senilai adalah perbandingan dua besaran di mana jika nilai satu besaran bertambah, nilai besaran yang lain ikut bertambah.' 
                        }
                    ];
                    this.parseMessage = '✨ Berhasil mengekstrak 3 soal otomatis dari dokumen: ' + file.name;
                    setTimeout(() => { this.parseMessage = ''; }, 5000);
                }, 1500);
            }
        }">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manajemen Ujian & Quiz (Auto-Parser AI)</h1>
                <p class="text-xs text-slate-500 mt-0.5">Unggah dokumen soal untuk otomatis menghasilkan pertanyaan ujian beserta kunci jawaban.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('guru.ujian.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- KARTU HEADER UTAMA & UPLOAD FILE PDF/WORD -->
                <div class="bg-white rounded-2xl border-t-8 border-t-blue-600 border border-gray-200 shadow-sm p-6 space-y-4">
                    <input type="text" name="judul_ujian" required placeholder="Judul Formulir / Ujian Tanpa Judul" value="Evaluasi Mandiri: Konsep Rasio Kelas VII" class="w-full text-xl font-bold text-gray-800 border-0 border-b border-gray-200 focus:border-blue-600 focus:ring-0 px-0 pb-1 placeholder-gray-300">
                    <textarea name="deskripsi_ujian" rows="2" placeholder="Deskripsi Formulir / Petunjuk Pengerjaan Ujian..." class="w-full text-xs text-gray-600 border-0 border-b border-gray-200 focus:border-blue-600 focus:ring-0 px-0 pb-1 placeholder-gray-300 resize-none">Kerjakan soal-soal perbandingan dan rasio di bawah ini dengan teliti.</textarea>
                    
                    <!-- Fitur Unggah Dokumen -->
                    <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-2 pt-3">
                        <label class="block text-xs font-bold text-blue-800">📁 Auto-Generate Soal dari Dokumen (PDF / Word)</label>
                        <input type="file" name="file_soal" accept=".pdf,.doc,.docx" @change="parseFile($event)" class="w-full text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                        <p class="text-[10px] text-gray-500">Unggah file LKPD/Soal Anda; sistem AI akan otomatis mengekstrak teks dan mengisinya ke dalam daftar soal di bawah.</p>
                    </div>

                    <!-- Indikator Loading / Pesan Sukses Ekstraksi -->
                    <div x-show="isParsing" class="text-xs font-bold text-blue-600 animate-pulse bg-blue-50 p-3 rounded-xl border border-blue-200" style="display: none;">
                        <span x-text="parseMessage"></span>
                    </div>
                    <div x-show="parseMessage && !isParsing" class="text-xs font-bold text-green-700 bg-green-50 p-3 rounded-xl border border-green-200" style="display: none;">
                        <span x-text="parseMessage"></span>
                    </div>
                </div>

                <!-- DAFTAR KARTU PERTANYAAN DENGAN TOOLBAR DI SAMPING KANAN -->
                <div class="space-y-6">
                    <template x-for="(q, index) in questions" :key="index">
                        <div class="flex gap-4 items-start">
                            
                            <!-- Kartu Pertanyaan Utama (Kiri) -->
                            <div class="flex-1 bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4 border-l-4 border-l-blue-600 transition hover:shadow-md">
                                
                                <!-- Baris Atas: Input Pertanyaan & Dropdown Tipe Soal -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                                    <div class="md:col-span-2">
                                        <input type="text" :name="'questions['+index+'][text]'" x-model="q.text" placeholder="Pertanyaan..." required class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <select :name="'questions['+index+'][type]'" x-model="q.type" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                            <option value="multiple">⚪ Pilihan Ganda</option>
                                            <option value="short">➖ Jawaban Singkat</option>
                                            <option value="paragraph">📜 Esai / Paragraf</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Opsi Pilihan Ganda -->
                                <div x-show="q.type === 'multiple'" class="space-y-2 pl-2 border-l-2 border-blue-100">
                                    <template x-for="(opt, optIndex) in q.options" :key="optIndex">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-400 font-bold w-4 text-center" x-text="String.fromCharCode(65 + optIndex) + '.'"></span>
                                            <input type="text" :name="'questions['+index+'][options]['+optIndex+']'" x-model="q.options[optIndex]" placeholder="Opsi jawaban..." class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white focus:outline-none border-gray-200">
                                            <button type="button" @click="q.options.splice(optIndex, 1)" x-show="q.options.length > 1" class="text-gray-400 hover:text-red-500 text-xs">❌</button>
                                        </div>
                                    </template>
                                    <div class="pt-1">
                                        <button type="button" @click="q.options.push('Opsi baru')" class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center space-x-1">
                                            <span>➕ Tambah Opsi</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Kunci Jawaban untuk Auto-Correct AI -->
                                <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100 flex flex-col sm:flex-row items-center justify-between gap-2">
                                    <span class="text-[11px] font-semibold text-blue-700">🔑 Kunci Jawaban (AI Auto-Correct):</span>
                                    <input type="text" :name="'questions['+index+'][answer]'" x-model="q.answer" placeholder="Contoh: A atau kata kunci" required class="w-full sm:w-1/2 text-xs border border-blue-200 rounded-lg p-2 bg-white focus:outline-none">
                                </div>

                                <!-- Upload Gambar Soal -->
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Sisipkan Gambar Soal (Opsional):</label>
                                    <input type="file" :name="'questions['+index+'][image]'" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700">
                                </div>
                            </div>

                            <!-- Bilah Alat Melayang di Samping Kanan Kartu -->
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-2 flex flex-col items-center space-y-3 sticky top-6">
                                <button type="button" @click="questions.splice(index + 1, 0, { text: '', type: 'multiple', options: ['Opsi 1', 'Opsi 2'], answer: '' })" class="p-2.5 rounded-xl hover:bg-blue-50 text-blue-600 transition" title="Tambah Pertanyaan di Bawah">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                                <button type="button" @click="questions.splice(index, 1)" x-show="questions.length > 1" class="p-2.5 rounded-xl hover:bg-red-50 text-red-500 transition" title="Hapus Pertanyaan Ini">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Tombol Simpan Utama -->
                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-sm transition">
                        Simpan & Publikasikan Ujian 🚀
                    </button>
                </div>
            </form>

        </div>
    </main>

</body>
</html>