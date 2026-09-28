<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - Portal Pembelajaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">
    <main class="flex-1 flex flex-col h-full overflow-y-auto items-center justify-center p-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 max-w-xl w-full space-y-6">
            <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                <h2 class="text-lg font-extrabold text-gray-900">👤 Detail Profil</h2>
                <a href="{{ route('dashboard') }}" class="text-xs text-blue-600 font-semibold hover:underline">← Kembali ke Dashboard</a>
            </div>

            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex items-center gap-6">
                <div>
                    @if($user->avatar)
                        <img src="{{ asset('storage/'.$user->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover border-2 border-blue-600">
                    @else
                        <div class="w-20 h-20 rounded-full bg-slate-900 text-white flex items-center justify-center text-xl font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="space-y-1">
                    <div class="text-base font-bold text-gray-900">{{ $user->name }}</div>
                    <div class="text-xs text-slate-500 font-medium capitalize">
                        @if($user->role === 'siswa')
                            Siswa 
                            @if(optional(optional($user->siswaProfile)->kelas)->nama_kelas)
                                • Kelas {{ optional($user->siswaProfile->kelas)->nama_kelas }}
                            @else
                                • <span class="text-red-500 font-semibold">Belum masuk kelas</span>
                            @endif
                        @elseif($user->role === 'guru')
                            Guru
                        @else
                            Admin
                        @endif
                    </div>
                    <div class="text-xs text-slate-400">{{ $user->email }}</div>
                </div>
            </div>

            <!-- Upload Avatar Form -->
            <form action="{{ route('profile.avatar') }}" method="post" enctype="multipart/form-data" class="pt-4 border-t border-gray-100 flex items-center gap-3">
                @csrf
                <input type="file" name="avatar" accept="image/*" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition">Unggah Foto</button>
            </form>
            @error('avatar') <span class="text-[10px] text-red-500 block font-semibold">{{ $message }}</span> @enderror

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('profile.edit') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs py-3 rounded-xl transition">Edit Profil</a>
                <a href="{{ route('profile.password.edit') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs py-3 rounded-xl transition">Ubah Password</a>

                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs py-3 rounded-xl transition">Keluar</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>