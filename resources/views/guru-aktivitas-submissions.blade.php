<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumpulan Siswa - {{ $aktivitas->judul }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">
    <main class="flex-1 flex flex-col h-full overflow-y-auto p-8">
        <div class="max-w-5xl mx-auto w-full space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">📥 Jawaban Siswa: {{ $aktivitas->judul }}</h1>
                    <p class="text-xs text-slate-500">Kelas: {{ optional($aktivitas->kelas)->nama_kelas ?? 'Semua Kelas' }}</p>
                </div>
                <a href="{{ route('guru.aktivitas.index') }}" class="text-xs text-blue-600 font-semibold hover:underline">← Kembali ke Daftar</a>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-left uppercase tracking-wider">
                            <th class="p-3 rounded-l-xl">Nama Siswa</th>
                            <th class="p-3">Waktu Pengumpulan</th>
                            <th class="p-3">Jawaban Teks</th>
                            <th class="p-3 text-center">File Jawaban</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($subs as $sub)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-3 font-semibold text-slate-800">{{ optional($sub->siswa)->name }}</td>
                                <td class="p-3 text-slate-500">{{ $sub->updated_at->format('d M Y H:i') }}</td>
                                <td class="p-3 text-slate-700 max-w-xs truncate">{{ $sub->text_answer ?? '-' }}</td>
                                <td class="p-3 text-center">
                                    @if($sub->file_path)
                                        <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg font-semibold hover:bg-blue-100 transition inline-block">📄 Unduh File</a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-6 text-center text-slate-400">Belum ada siswa yang mengumpulkan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>