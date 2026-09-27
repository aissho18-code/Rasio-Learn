<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - E-Learning Matematika</title>
    
    <!-- TAILWIND CSS CDN AGAR TAMPILAN LANGSUNG MUNCUL INDAH TANPA VITE -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen flex flex-col justify-between">

    <!-- HEADER / NAVBAR -->
    <header class="w-full bg-white border-b border-gray-100 py-4 px-6 md:px-12 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
                📐
            </div>
            <div>
                <h1 class="font-bold text-base text-gray-900 leading-tight">E-Learning Matematika</h1>
                <p class="text-[11px] text-gray-400">Materi: Konsep Rasio Kelas VII</p>
            </div>
        </div>
        
        <!-- TOMBOL PORTAL AKSES ADMIN DI POJOK KANAN ATAS -->
        <a href="{{ route('login', ['role' => 'admin']) }}" 
           class="text-xs font-semibold text-gray-600 hover:text-purple-700 transition flex items-center space-x-1.5 bg-gray-100/80 hover:bg-purple-50 px-3.5 py-2 rounded-xl border border-gray-200/80 hover:border-purple-200 shadow-sm">
            <span>⚙️ Portal Admin</span>
        </a>
    </header>

    <!-- KONTEN UTAMA: PILIH ROLE (GURU & SISWA SAJA) -->
    <main class="flex-1 flex flex-col items-center justify-center px-4 py-12 max-w-4xl mx-auto w-full">
        
        <div class="text-center space-y-2 mb-10">
            <span class="bg-blue-50 text-blue-600 font-bold text-xs px-3 py-1 rounded-full uppercase tracking-wider">
                Selamat Datang
            </span>
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">Pilih Peran Anda untuk Masuk</h2>
            <p class="text-xs md:text-sm text-gray-500 max-w-md mx-auto">
                Silakan pilih jenis akun untuk mengakses platform pembelajaran matematika.
            </p>
        </div>

        <!-- GRID 2 KARTU PERAN: GURU & SISWA -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
            
            <!-- KARTU GURU -->
            <a href="{{ route('login', ['role' => 'guru']) }}" 
               class="bg-white rounded-2xl border border-gray-200/80 p-8 flex flex-col justify-between hover:shadow-xl hover:border-blue-400 transition group cursor-pointer space-y-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -z-0 group-hover:scale-110 transition"></div>
                
                <div class="space-y-4 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow-md group-hover:scale-105 transition">
                        👨‍🏫
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 group-hover:text-blue-600 transition">Guru / Pengajar</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Kelola modul materi, tugas harian, paket ujian, serta evaluasi & penilaian siswa.
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs font-bold text-blue-600 group-hover:translate-x-1 transition relative z-10">
                    <span>Masuk Guru</span>
                    <span>→</span>
                </div>
            </a>

            <!-- KARTU SISWA -->
            <a href="{{ route('login', ['role' => 'siswa']) }}" 
               class="bg-white rounded-2xl border border-gray-200/80 p-8 flex flex-col justify-between hover:shadow-xl hover:border-indigo-400 transition group cursor-pointer space-y-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-bl-full -z-0 group-hover:scale-110 transition"></div>
                
                <div class="space-y-4 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-2xl shadow-md group-hover:scale-105 transition">
                        👨‍🎓
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 group-hover:text-indigo-600 transition">Siswa / Pelajar</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Pelajari materi pembelajaran, kumpulkan tugas, ikuti ujian, dan cek rekap nilai.
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs font-bold text-indigo-600 group-hover:translate-x-1 transition relative z-10">
                    <span>Masuk Siswa</span>
                    <span>→</span>
                </div>
            </a>

        </div>

    </main>

    <!-- FOOTER -->
    <footer class="w-full py-4 text-center text-xs text-gray-400 border-t border-gray-100 bg-white">
        &copy; {{ date('Y') }} E-Learning Matematika. All rights reserved.
    </footer>

</body>
</html>