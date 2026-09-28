<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Evaluasi & Penilaian - Ratio Learn</title>

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
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-6xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Evaluasi & Penilaian Hasil Belajar</h1>
                <p class="text-xs text-slate-500 mt-0.5">Tinjau nilai tugas, kuis, serta catatan umpan balik dari guru Anda.</p>
            </div>

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

            <!-- STATISTIK RINGKASAN EVALUASI -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">📊</div>
                    <div>
                        <span class="text-[11px] text-gray-400 font-medium">Rata-rata Nilai</span>
                        <h3 class="text-xl font-extrabold text-slate-900">{{ $rataRataNilai }}</h3>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl font-bold">✅</div>
                    <div>
                        <span class="text-[11px] text-gray-400 font-medium">Tugas Dinilai</span>
                        <h3 class="text-xl font-extrabold text-slate-900">{{ $totalDinilai }}</h3>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">📝</div>
                    <div>
                        <span class="text-[11px] text-gray-400 font-medium">Kuis Selesai</span>
                        <h3 class="text-xl font-extrabold text-slate-900">{{ $totalKuis }}</h3>
                    </div>
                </div>
            </div>

            <!-- TABEL DAFTAR EVALUASI DAN NILAI -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm">Riwayat Nilai & Catatan Guru</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Daftar lengkap hasil evaluasi tugas dan ujian harian Anda.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/75 border-b border-gray-200 text-gray-400 uppercase tracking-wider">
                                <th class="py-3 px-6 font-semibold">Judul Tugas / Evaluasi</th>
                                <th class="py-3 px-6 font-semibold">Tanggal Kumpul</th>
                                <th class="py-3 px-6 font-semibold">Catatan / Umpan Balik Guru</th>
                                <th class="py-3 px-6 font-semibold text-right">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($evaluasiList ?? [] as $eval)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-800">
                                        {{ $eval->tugas->judul ?? $eval->judul ?? 'Evaluasi Pembelajaran' }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-500">
                                        {{ $eval->updated_at ? \Carbon\Carbon::parse($eval->updated_at)->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-600 italic">
                                        "{{ $eval->catatan_guru ?? $eval->feedback ?? 'Belum ada catatan dari guru.' }}"
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if(isset($eval->nilai) && $eval->nilai !== null)
                                            <span class="bg-green-100 text-green-700 font-bold px-3 py-1 rounded-lg">
                                                {{ $eval->nilai }}
                                            </span>
                                        @else
                                            <span class="bg-amber-100 text-amber-700 font-semibold px-3 py-1 rounded-lg">
                                                Menunggu Penilaian
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400">
                                        Belum ada data evaluasi atau nilai yang tercatat saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>