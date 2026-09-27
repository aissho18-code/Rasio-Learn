<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $kelas->exists ? 'Edit Kelas' : 'Buat Kelas' }} - Portal Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">
    <main class="flex-1 flex flex-col h-full overflow-y-auto items-center justify-center p-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 max-w-lg w-full space-y-6">
            <h2 class="text-lg font-extrabold text-gray-900 border-b border-gray-100 pb-3">
                {{ $kelas->exists ? '✏️ Edit Kelas' : '➕ Buat Kelas Baru' }}
            </h2>

            <form action="{{ $kelas->exists ? route('admin.kelas.update', $kelas) : route('admin.kelas.store') }}" method="post" class="space-y-4">
                @csrf
                @if($kelas->exists) @method('PUT') @endif

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Kelas</label>
                    <input name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Wali Kelas (Guru)</label>
                    <select name="wali_kelas_id" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Pilih wali kelas --</option>
                        @foreach($gurus as $g) 
                            <option value="{{ $g->id }}" @selected($kelas->wali_kelas_id == $g->id)>{{ $g->name }}</option> 
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Jadwal</label>
                        <input name="jadwal" placeholder="Cth: Senin, 08:00" value="{{ old('jadwal', $kelas->jadwal) }}" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kapasitas</label>
                        <input name="kapasitas" type="number" placeholder="Cth: 30" value="{{ old('kapasitas', $kelas->kapasitas) }}" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.kelas.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs px-4 py-3 rounded-xl transition">Batal</a>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-sm transition">
                        {{ $kelas->exists ? 'Simpan Perubahan' : 'Buat Kelas' }}
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>