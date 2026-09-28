<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Portal Pembelajaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">
    <main class="flex-1 flex flex-col h-full overflow-y-auto items-center justify-center p-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 max-w-lg w-full space-y-6">
            <h2 class="text-lg font-extrabold text-gray-900 border-b border-gray-100 pb-3">✏️ Edit Profil</h2>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl text-xs">
                    <ul class="list-disc ml-4 space-y-1">
                        @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="post" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Email</label>
                    <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                @if($user->role === 'siswa')
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kelas</label>
                        <input class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-100 text-slate-500 cursor-not-allowed" value="{{ optional(optional($user->siswaProfile)->kelas)->nama_kelas ?? 'Belum tergabung' }}" readonly>
                    </div>
                @endif

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('profile.show') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs px-4 py-3 rounded-xl transition">Batal</a>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-sm transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>