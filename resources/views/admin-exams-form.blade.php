<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->exists ? 'Edit Ujian' : 'Buat Ujian Baru' }} - Admin Ratio Learn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 min-h-screen p-6">
    <div class="max-w-2xl mx-auto space-y-4">
        <a href="{{ route('admin.exams.index') }}" class="inline-block text-xs font-bold text-blue-600 hover:underline">
            ← Kembali ke Daftar Ujian
        </a>

        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h1 class="text-base font-bold text-slate-900">{{ $exam->exists ? 'Edit Ujian' : 'Buat Ujian Baru (Admin)' }}</h1>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ $exam->exists ? route('admin.exams.update', $exam->id) : route('admin.exams.store') }}" class="space-y-4">
                @csrf
                @if ($exam->exists)
                    @method('PUT')
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Ujian</label>
                    <input type="text" name="title" required value="{{ old('title', $exam->title) }}" placeholder="Contoh: Ujian Akhir Semester Biologi" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Kelas</label>
                        <select name="kelas_id" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}" @selected(old('kelas_id', $exam->kelas_id) == $k->id)>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Max Pelanggaran</label>
                        <input type="number" name="max_violations" min="1" max="20" required value="{{ old('max_violations', $exam->max_violations ?? 3) }}" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Ujian</label>
                    <textarea name="description" rows="4" placeholder="Tuliskan petunjuk pengerjaan..." class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">{{ old('description', $exam->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Mulai</label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $exam->starts_at?->format('Y-m-d\TH:i')) }}" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Selesai</label>
                        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $exam->ends_at?->format('Y-m-d\TH:i')) }}" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.exams.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">Batal</a>
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition cursor-pointer">
                        {{ $exam->exists ? 'Simpan Perubahan' : 'Simpan Ujian' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>