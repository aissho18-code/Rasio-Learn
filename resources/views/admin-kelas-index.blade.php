@extends('layouts.admin')

@section('title', 'Manajemen Kelas - Portal Admin')

@section('content')
        <div class="max-w-7xl space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">Manajemen Kelas</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola data kelas, jadwal, kapasitas, serta penugasan peserta didik.</p>
                </div>
                <a href="{{ route('admin.kelas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm">
                    + Buat Kelas Baru
                </a>
            </div>

            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <!-- TABEL RINGKASAN KELAS -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-200/80">
                    <h3 class="font-bold text-gray-800 text-sm">Daftar Semua Kelas</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-100/70 text-gray-600 font-semibold uppercase text-[10px] tracking-wider border-b border-gray-200">
                                <th class="p-4">Nama Kelas</th>
                                <th class="p-4 text-center">Jumlah Murid</th>
                                <th class="p-4">Wali Kelas</th>
                                <th class="p-4">Jadwal</th>
                                <th class="p-4 text-center">Kapasitas</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($kelas as $k)
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="p-4 font-bold text-gray-800">{{ $k->nama_kelas }}</td>
                                    <td class="p-4 text-center font-semibold text-blue-600">{{ $k->siswa_count ?? $k->siswa()->count() }}</td>
                                    <td class="p-4 text-gray-600">{{ optional($k->wali)->name ?? '-' }}</td>
                                    <td class="p-4 text-gray-600">{{ $k->jadwal ?? '-' }}</td>
                                    <td class="p-4 text-center text-gray-600">{{ $k->kapasitas ?? '-' }}</td>
                                    <td class="p-4 text-center space-x-2">
                                        <a href="{{ route('admin.kelas.edit', $k) }}" class="text-blue-600 font-semibold hover:underline">Edit</a>
                                        <form action="{{ route('admin.kelas.destroy', $k) }}" method="post" class="inline-block" onsubmit="return confirm('Hapus kelas ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 font-semibold hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DAFTAR SISWA PER KELAS -->
            @foreach($kelas as $k)
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm">Daftar Siswa — {{ $k->nama_kelas }}</h3>
                            <p class="text-[11px] text-gray-500">Total Siswa: {{ $k->siswa()->count() }}</p>
                        </div>
                        <form action="{{ route('admin.kelas.add_participant', $k) }}" method="post" class="flex gap-2 items-center">
                            @csrf
                            <select name="user_id" class="text-xs border border-gray-200 rounded-xl p-2 bg-gray-50">
                                <option value="">Pilih Siswa untuk Ditambahkan</option>
                                @foreach(\App\Models\User::where('role', 'siswa')->whereNotIn('id', \App\Models\SiswaProfile::where('kelas_id', $k->id)->pluck('user_id'))->get() as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="role" value="siswa">
                            <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-xs transition">Tambah</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-100/70 text-gray-600 font-semibold uppercase text-[10px] tracking-wider border-b border-gray-200">
                                    <th class="p-3">Nama Siswa</th>
                                    <th class="p-3">Email</th>
                                    <th class="p-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($k->siswa as $s)
                                    <tr class="hover:bg-blue-50/30 transition">
                                        <td class="p-3 font-medium text-gray-800">{{ $s->name }}</td>
                                        <td class="p-3 text-gray-500">{{ $s->email }}</td>
                                        <td class="p-3 text-right space-x-2">
                                            <form action="{{ route('admin.kelas.remove_participant', $k) }}" method="post" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $s->id }}">
                                                <input type="hidden" name="role" value="siswa">
                                                <button class="text-red-600 font-semibold hover:underline" onclick="return confirm('Keluarkan siswa dari kelas?')">Keluarkan</button>
                                            </form>

                                            <form action="{{ route('admin.kelas.move_participant', $k) }}" method="post" class="inline-block ml-3">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $s->id }}">
                                                <input type="hidden" name="role" value="siswa">
                                                <select name="target_kelas_id" class="text-xs border rounded-lg p-1 bg-gray-50">
                                                    <option value="">Pindah ke...</option>
                                                    @foreach(\App\Models\Kelas::where('id', '!=', $k->id)->get() as $kk)
                                                        <option value="{{ $kk->id }}">{{ $kk->nama_kelas }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-lg font-semibold ml-1">Pindah</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-6 text-center text-gray-400 italic">Belum ada siswa di kelas ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endsection