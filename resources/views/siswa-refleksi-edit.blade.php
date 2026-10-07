<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Edit Refleksi - Ratio Learn</title>

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
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            <x-notification-bell />
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Siswa' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Siswa' }}</div>
                </div>
            </a>
        </header>

        <div class="px-8 pb-8 space-y-6 flex-1 max-w-4xl">
            <div>
                <a href="{{ route('siswa.refleksi.index') }}" class="text-xs font-bold text-blue-600 hover:underline mb-1 inline-block">&larr; Kembali ke Daftar Refleksi</a>
                <h1 class="text-xl font-extrabold text-slate-900">Edit Refleksi — Pertemuan {{ $refleksi->pertemuan }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui jawaban refleksi Anda jika ada perubahan.</p>
            </div>

            @php $a = $refleksi->answers ?? [] @endphp

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-6">
                <form action="{{ route('siswa.refleksi.update', $refleksi->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-1">1. Apa hal baru yang paling kamu pahami?</label>
                            <textarea name="q1" rows="3" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ old('q1', $a['q1'] ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-1">2. Bagian mana yang masih membingungkan?</label>
                            <textarea name="q2" rows="3" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ old('q2', $a['q2'] ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-1">3. Apa yang kamu lakukan ketika mengalami kesulitan?</label>
                            <textarea name="q3" rows="3" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ old('q3', $a['q3'] ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-1">4. Bagian mana yang ingin kamu pelajari kembali?</label>
                            <textarea name="q4" rows="3" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ old('q4', $a['q4'] ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="catatan" rows="3" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ old('catatan', $refleksi->catatan ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm transition">
                            💾 Perbarui Refleksi
                        </button>
                        <a href="{{ route('siswa.refleksi.index') }}" class="text-xs font-semibold text-gray-500 hover:underline">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>