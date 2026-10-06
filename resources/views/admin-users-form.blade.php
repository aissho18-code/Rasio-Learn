@extends('layouts.admin')

@section('title', ($user->exists ? 'Edit Pengguna' : 'Buat Pengguna') . ' - Portal Admin')

@section('content')
    <div class="flex min-h-[calc(100vh-9rem)] items-center justify-center p-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 max-w-lg w-full space-y-6">
            <h2 class="text-lg font-extrabold text-gray-900 border-b border-gray-100 pb-3">
                {{ $user->exists ? '✏️ Edit Pengguna' : '➕ Buat Pengguna Baru' }}
            </h2>

            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="post" class="space-y-4">
                @csrf
                @if($user->exists) @method('PUT') @endif

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @error('name') <span class="text-[10px] text-red-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Email</label>
                    <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @error('email') <span class="text-[10px] text-red-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Role</label>
                    <select id="roleSelect" name="role" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $r) 
                            <option value="{{ $r }}" @selected(old('role', $user->role ?? '') == $r)>{{ ucfirst($r) }}</option> 
                        @endforeach
                    </select>
                    @error('role') <span class="text-[10px] text-red-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                <div id="classSelectWrapper" style="display: {{ (old('role', $user->role ?? '') === 'siswa' || old('role', $user->role ?? '') === 'guru') ? 'block' : 'none' }};">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Kelas (Opsional)</label>
                    <select name="kelas_id" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Tidak memilih kelas --</option>
                        @foreach($kelas as $k) 
                            <option value="{{ $k->id }}" @selected(isset($kelasId) && $kelasId == $k->id)>{{ $k->nama_kelas }}</option> 
                        @endforeach
                    </select>
                    @error('kelas_id') <span class="text-[10px] text-red-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Password @if($user->exists) <span class="text-[10px] text-gray-400 font-normal">(kosongkan jika tidak diubah)</span> @endif</label>
                    <input name="password" type="password" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @error('password') <span class="text-[10px] text-red-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Konfirmasi Password</label>
                    <input name="password_confirmation" type="password" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs px-4 py-3 rounded-xl transition">Batal</a>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-sm transition">
                        {{ $user->exists ? 'Simpan Perubahan' : 'Buat Pengguna' }}
                    </button>
                </div>
            </form>
        </div>
    <script>
        document.getElementById('roleSelect')?.addEventListener('change', function(){
            const val = this.value;
            const wrapper = document.getElementById('classSelectWrapper');
            if (val === 'siswa' || val === 'guru') wrapper.style.display = 'block'; else wrapper.style.display = 'none';
        });
    </script>
    </div>
@endsection