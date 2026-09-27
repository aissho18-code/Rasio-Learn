<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($tugas) ? 'Edit Paket Tugas' : 'Buat Paket Tugas Baru' }} - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FLATPICKR CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">

    <!-- KUSTOM CSS FLATPICKR 24 JAM -->
    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
        .flatpickr-calendar {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border-radius: 1rem !important;
            border: 1px solid #e5e7eb !important;
            padding-bottom: 12px !important;
            overflow: visible !important;
        }
        .flatpickr-time {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
            height: auto !important;
            max-height: none !important;
            margin-top: 28px !important;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
            border-top: 1px solid #f3f4f6 !important;
            overflow: visible !important;
        }
        .flatpickr-time .numInputWrapper {
            height: 38px !important;
            position: relative !important;
            overflow: visible !important;
        }
        .flatpickr-time .numInputWrapper input {
            font-weight: 700 !important;
            color: #1e3a8a !important;
            font-size: 15px !important;
        }
        .flatpickr-time .numInputWrapper:nth-of-type(1)::before {
            content: "JAM (00-23)";
            position: absolute !important;
            top: -20px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            white-space: nowrap !important;
            font-size: 9px !important;
            font-weight: 800 !important;
            color: #2563eb !important;
            letter-spacing: 0.5px !important;
            z-index: 10 !important;
        }
        .flatpickr-time .numInputWrapper:nth-of-type(2)::before {
            content: "MENIT (00-59)";
            position: absolute !important;
            top: -20px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            white-space: nowrap !important;
            font-size: 9px !important;
            font-weight: 800 !important;
            color: #2563eb !important;
            letter-spacing: 0.5px !important;
            z-index: 10 !important;
        }
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
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-4xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ isset($tugas) ? 'Edit Paket Tugas' : 'Buat Paket Tugas Baru' }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola judul, petunjuk instruksi, berkas acuan, dan atur tenggat pengerjaan tugas (Format 24 Jam WIB).</p>
            </div>

            <form action="{{ isset($tugas) ? route('guru.tugas.update', $tugas->id) : route('guru.tugas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if(isset($tugas))
                    @method('PUT')
                @endif

                <!-- KARTU UTAMA TUGAS -->
                <div class="bg-white rounded-2xl border-t-8 border-t-blue-600 border border-gray-200 shadow-sm p-6 space-y-4">
                    <!-- Judul Tugas -->
                    <div>
                        <input type="text" name="judul" value="{{ old('judul', $tugas->judul ?? '') }}" required placeholder="Judul Tugas..." class="w-full text-xl font-bold border-0 border-b border-gray-200 focus:border-blue-600 focus:ring-0 p-2 placeholder-gray-300">
                    </div>

                    <!-- Petunjuk Pengerjaan Tugas -->
                    <div>
                        <input type="text" name="deskripsi" value="{{ old('deskripsi', $tugas->deskripsi ?? '') }}" placeholder="Petunjuk pengerjaan tugas..." class="w-full text-xs border-0 border-b border-gray-100 focus:border-blue-500 focus:ring-0 p-2 text-gray-600 placeholder-gray-300">
                    </div>

                    <!-- Pekan Default untuk Controller Validation -->
                    <input type="hidden" name="pekan" value="{{ old('pekan', $tugas->pekan ?? 'Pekan 1') }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        
                        <!-- Tenggat Pengumpulan -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700">📅 Tenggat Pengumpulan (Format 24 Jam WIB):</label>
                            <div class="relative">
                                <input type="text" id="tenggatPicker" name="tenggat_waktu" value="{{ old('tenggat_waktu', isset($tugas) && $tugas->tenggat_waktu ? $tugas->tenggat_waktu->format('Y-m-d H:i') : '') }}" required placeholder="Pilih Tanggal & Jam (misal: 23:59)..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 font-bold text-blue-900 focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                            </div>

                            <!-- Opsi Cepat -->
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                <span class="text-[10px] text-gray-400 font-semibold self-center mr-1">Opsi Cepat:</span>
                                <button type="button" onclick="setQuickDeadline(0, '23:59')" class="text-[10px] bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold px-2 py-1 rounded-lg border border-blue-200 transition">
                                    Malam Ini 23:59
                                </button>
                                <button type="button" onclick="setQuickDeadline(1, '23:59')" class="text-[10px] bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold px-2 py-1 rounded-lg border border-blue-200 transition">
                                    Besok 23:59
                                </button>
                                <button type="button" onclick="setQuickDeadline(7, '23:59')" class="text-[10px] bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold px-2 py-1 rounded-lg border border-blue-200 transition">
                                    1 Minggu Lagi
                                </button>
                            </div>
                        </div>

                        <!-- Lampiran Dokumen Acuan -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700">📁 Lampiran Dokumen Acuan (PDF/Word/Gambar):</label>
                            <input type="file" name="file_tugas" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @if(isset($tugas) && $tugas->file_path)
                                <p class="text-[10px] text-blue-600 font-medium pt-1">Dokumen terpasang: <a href="{{ asset('storage/' . $tugas->file_path) }}" target="_blank" class="underline font-bold">Lihat Berkas</a></p>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- TOMBOL AKSI SIMPAN PERUBAHAN TUGAS -->
                <div class="flex justify-end items-center pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-8 py-3 rounded-xl shadow-md transition">
                        Simpan Perubahan Tugas 🚀
                    </button>
                </div>
            </form>

        </div>
    </main>

    <!-- FLATPICKR JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        let fpInstance;

        document.addEventListener('DOMContentLoaded', function() {
            fpInstance = flatpickr("#tenggatPicker", {
                enableTime: true,
                time_24hr: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                minuteIncrement: 5,
                defaultHour: 23,
                defaultMinute: 59,
            });
        });

        function setQuickDeadline(addDays, timeString) {
            const date = new Date();
            date.setDate(date.getDate() + addDays);

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            const formattedDate = `${year}-${month}-${day} ${timeString}`;
            fpInstance.setDate(formattedDate, true);
        }
    </script>
</body>
</html>