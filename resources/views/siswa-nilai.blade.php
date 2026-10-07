<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rekapitulasi Nilai & Performa - Ratio Learn</title>

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
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Rekapitulasi Nilai & Performa</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pantau hasil evaluasi belajar dan nilai akademik Anda.</p>
            </div>

            <!-- KARTU RINGKASAN STATISTIK NILAI -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Rata-Rata Nilai</p>
                        <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ $rataRata }}</h3>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">📈</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Tugas Dinilai</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalTugasDinilai }}</h3>
                    </div>
                    <div class="p-3 bg-green-50 text-green-600 rounded-xl">📝</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Predikat Akademik</p>
                        <h3 class="text-2xl font-bold text-indigo-600 mt-1">Sangat Baik</h3>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">⭐</div>
                </div>
            </div>

            <!-- TABEL DAFTAR NILAI -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm">Daftar Nilai Tugas & Quiz</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Rincian nilai dari setiap aktivitas pembelajaran yang telah dikumpulkan.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/75 border-b border-gray-200 text-gray-400 uppercase tracking-wider">
                                <th class="py-3 px-6 font-semibold">Mata Pelajaran</th>
                                <th class="py-3 px-6 font-semibold">Aktivitas / Tugas</th>
                                <th class="py-3 px-6 font-semibold">Tanggal</th>
                                <th class="py-3 px-6 font-semibold text-center">Predikat</th>
                                <th class="py-3 px-6 font-semibold text-right">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($nilaiList as $item)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-800">{{ $item['mapel'] }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $item['kategori'] }}</td>
                                    <td class="py-4 px-6 text-gray-400">{{ $item['tanggal'] }}</td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="bg-indigo-50 text-indigo-700 font-bold px-2.5 py-1 rounded-lg">
                                            {{ $item['predikat'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right font-bold text-blue-600 text-sm">{{ $item['nilai'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-400">Belum ada nilai yang dipublikasikan.</td>
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