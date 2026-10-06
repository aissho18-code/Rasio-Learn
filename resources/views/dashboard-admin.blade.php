@extends('layouts.admin')

@section('title', 'Dashboard Admin - Ratio Learn')

@section('content')
        <div class="max-w-7xl space-y-6">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Dashboard Admin</h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola pengguna, kelas, pembelajaran, dan aktivitas sistem Ratio Learn secara terpusat.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <section class="grid grid-cols-2 lg:grid-cols-5 gap-3" aria-label="Ringkasan pengguna aktif">
                <div class="bg-white rounded-xl border border-gray-200 p-4"><div class="text-[11px] text-gray-500">Total Guru</div><div class="text-xl font-extrabold" id="count-guru">{{ $guruUsers->count() }}</div></div>
                <div class="bg-white rounded-xl border border-gray-200 p-4"><div class="text-[11px] text-gray-500">Guru Aktif</div><div class="text-xl font-extrabold text-green-700" id="count-guru-active">{{ $totalGuruAktif }}</div></div>
                <div class="bg-white rounded-xl border border-gray-200 p-4"><div class="text-[11px] text-gray-500">Total Siswa</div><div class="text-xl font-extrabold" id="count-siswa">{{ $siswaUsers->count() }}</div></div>
                <div class="bg-white rounded-xl border border-gray-200 p-4"><div class="text-[11px] text-gray-500">Siswa Aktif</div><div class="text-xl font-extrabold text-green-700" id="count-siswa-active">{{ $totalSiswaAktif }}</div></div>
                <div class="bg-white rounded-xl border border-gray-200 p-4"><div class="text-[11px] text-gray-500">Total Pengguna Aktif</div><div class="text-xl font-extrabold text-green-700" id="count-total-active">{{ $totalPenggunaAktif }}</div></div>
            </section>

            <!-- Form Tambah Akun Baru -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center space-x-2">
                        <span>➕</span>
                        <span>Tambah Akun Guru / Siswa Baru</span>
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Daftarkan pengguna baru ke dalam sistem pembelajaran Ratio Learn.</p>
                </div>

                <form action="{{ route('admin.users.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Nama lengkap..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required placeholder="Alamat email..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required placeholder="Kata sandi..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Peran (Role)</label>
                        <select name="role" required class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="guru">Guru</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 px-4 rounded-xl shadow-sm transition">
                            Simpan Akun 🚀
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Akun -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-gray-800 text-base flex items-center space-x-2">
                            <span>👥</span>
                            <span>Daftar Pengguna Terdaftar</span>
                        </h4>
                        <p class="text-xs text-gray-400 mt-0.5">Semua akun aktif yang terdaftar di dalam platform.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/50 text-gray-400 font-bold uppercase border-b border-gray-100 text-[10px] tracking-wider">
                                <th class="py-3.5 px-6">Nama</th>
                                <th class="py-3.5 px-6">Email</th>
                                <th class="py-3.5 px-6">Peran</th>
                                <th class="py-3.5 px-6">Kelas</th>
                                <th class="py-3.5 px-6">Status / Terakhir Aktif</th>
                                <th class="py-3.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @if(($users ?? collect())->isNotEmpty())
                                @foreach($users as $u)
                                <tr class="hover:bg-gray-50/50 transition" data-user-id="{{ $u->id }}">
                                    <td class="py-4 px-6 font-bold text-gray-800 flex items-center space-x-2">
                                        <span class="text-gray-400">👤</span>
                                        <span>{{ $u->name }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">
                                        {{ $u->email }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @php($userRole = $u->monitoringRole())
                                        <span class="px-3 py-1 text-[10px] font-extrabold uppercase rounded-full {{ $userRole === 'guru' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                            {{ $userRole }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">{{ $userRole === 'siswa' ? ($u->siswaProfile?->kelas?->nama_kelas ?? '-') : '-' }}</td>
                                    <td class="py-4 px-6">
                                        <span data-user-status class="font-bold {{ $u->isOnline() ? 'text-green-700' : 'text-gray-500' }}">{{ $u->isOnline() ? '● Aktif' : '○ Tidak aktif' }}</span>
                                        <span data-user-last-active class="block text-[10px] text-gray-400 mt-1">{{ $u->lastActiveLabel() }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun ini?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold px-3 py-1.5 rounded-xl text-xs transition">
                                                Hapus 🗑️
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400 italic">Belum ada pengguna terdaftar.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
<script>
    const adminMonitoringUrl = @json(route('admin.monitoring.status'));

    async function refreshAdminMonitoring() {
        try {
            const response = await fetch(adminMonitoringUrl, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const data = await response.json();
            document.getElementById('count-guru').textContent = data.counts.guru;
            document.getElementById('count-guru-active').textContent = data.counts.guru_active;
            document.getElementById('count-siswa').textContent = data.counts.siswa;
            document.getElementById('count-siswa-active').textContent = data.counts.siswa_active;
            document.getElementById('count-total-active').textContent = data.counts.total_active;

            data.users.forEach((user) => {
                const row = document.querySelector(`[data-user-id="${user.id}"]`);
                if (!row) return;
                const status = row.querySelector('[data-user-status]');
                status.textContent = user.is_active ? '● Aktif' : '○ Tidak aktif';
                status.className = `font-bold ${user.is_active ? 'text-green-700' : 'text-gray-500'}`;
                row.querySelector('[data-user-last-active]').textContent = user.last_active;
            });
        } catch (error) {
            console.error('Gagal memperbarui status pengguna.', error);
        }
    }

    refreshAdminMonitoring();
    setInterval(refreshAdminMonitoring, 30000);
</script>
@endsection