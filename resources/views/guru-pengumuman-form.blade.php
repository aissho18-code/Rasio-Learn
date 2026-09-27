<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pengumuman->exists ? 'Edit Pengumuman' : 'Buat Pengumuman' }} - Ratio Learn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 p-6 min-h-screen">
    <div class="max-w-2xl mx-auto space-y-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('guru.pengumuman.index') }}" class="inline-block text-xs font-bold text-blue-600 hover:underline">
                ← Kembali ke Daftar Pengumuman
            </a>
            <x-notification-bell />
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-xs space-y-4">
            <div>
                <h1 class="text-lg font-bold text-slate-900">
                    {{ $pengumuman->exists ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $pengumuman->exists ? 'Perubahan akan memperbarui data dan mengirim ulang status pengumuman baru ke siswa.' : 'Pengumuman akan langsung tampil di dashboard siswa target.' }}
                </p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ $pengumuman->exists ? route('guru.pengumuman.update', $pengumuman->id) : route('guru.pengumuman.store') }}" class="space-y-4">
                @csrf
                @if ($pengumuman->exists)
                    @method('PUT')
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Kelas</label>
                    <select name="kelas_id" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasGuru as $k)
                            <option value="{{ $k->id }}" @selected(old('kelas_id', $pengumuman->kelas_id) == $k->id)>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pengumuman</label>
                    <input type="text" name="judul" required value="{{ old('judul', $pengumuman->judul) }}" placeholder="Contoh: Jadwal Ujian Tengah Semester" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pengumuman</label>
                    <textarea name="isi" rows="5" required placeholder="Tuliskan isi instruksi..." class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">{{ old('isi', $pengumuman->isi) }}</textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('guru.pengumuman.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition cursor-pointer">
                        {{ $pengumuman->exists ? 'Simpan & Publikasikan Ulang' : 'Publikasikan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>