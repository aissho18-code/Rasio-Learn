<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Aktivitas Baru - Portal Guru</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">
    <main class="flex-1 flex flex-col h-full overflow-y-auto items-center justify-center p-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 max-w-2xl w-full space-y-6">
            <h2 class="text-lg font-extrabold text-slate-900 border-b border-gray-100 pb-3">➕ Buat Aktivitas / LKPD Baru</h2>

            <form action="{{ route('guru.aktivitas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Judul Aktivitas</label>
                    <input name="judul" required placeholder="Contoh: Praktikum Struktur Sel Plant & Animal" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Ditujukan untuk Kelas</label>
                        <select name="kelas_id" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tipe Penyerahan Jawaban</label>
                        <select name="respons_type" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="file">Upload File (PDF/Word/Gambar)</option>
                            <option value="text">Teks Langsung</option>
                            <option value="both">Teks & Upload File</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tujuan Pembelajaran (Opsional)</label>
                    <input name="tujuan" placeholder="Contoh: Siswa dapat mengidentifikasi organel sel" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Petunjuk Pengerjaan</label>
                    <textarea name="petunjuk" rows="2" placeholder="Tuliskan petunjuk pengerjaan..." class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Unggah Lampiran LKPD (PDF, DOC, DOCX, PNG, JPG)</label>
                    <input type="file" name="lkpd" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Publikasi</label>
                    <select name="status" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50">
                        <option value="published">Langsung Terbitkan (Published)</option>
                        <option value="draft">Simpan sebagai Draft</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('guru.aktivitas.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-4 py-3 rounded-xl transition">Batal</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl transition shadow-sm">Simpan & Publikasikan</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>