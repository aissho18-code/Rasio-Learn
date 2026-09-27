@extends('layouts.app')

@section('content')
    @php
        $dikelolaMateri = $materiList ?? $materis ?? $materi ?? [];
        $totalMateri = is_countable($dikelolaMateri) ? count($dikelolaMateri) : 0;
    @endphp

    <div class="mx-auto max-w-[1200px] px-4 pb-10 pt-6">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Kelola Materi</h1>
            </div>

            <button type="button" onclick="openCreateForm()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
                <span>＋</span>
                <span>Tambah Materi</span>
            </button>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50/90 px-6 py-5">
                <h2 class="text-[15px] font-extrabold text-blue-700">Daftar Materi</h2>
            </div>

            <div id="list-panel" class="overflow-x-auto">
                <table class="w-full min-w-[900px] border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                            <th class="border border-slate-200 px-4 py-4">Judul Pertemuan / Materi</th>
                            <th class="border border-slate-200 px-4 py-4">Status</th>
                            <th class="border border-slate-200 px-4 py-4">Komponen</th>
                            <th class="border border-slate-200 px-4 py-4">Terakhir Update</th>
                            <th class="border border-slate-200 px-4 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($totalMateri === 0)
                            <tr>
                                <td colspan="5" class="border border-slate-200 px-4 py-16 text-center text-sm text-slate-400">
                                    Belum ada materi yang dipublikasikan.
                                </td>
                            </tr>
                        @else
                            @foreach ($dikelolaMateri as $index => $materi)
                                @php
                                    $status = $materi->status ?? 'aktif';
                                    $isPublished = $status === 'aktif';
                                    $countKomponen = max(1, (int) collect(explode("\n", $materi->konten ?? ''))->filter(fn($line) => trim($line) !== '')->count());
                                @endphp
                                <tr class="align-middle text-sm text-slate-700 hover:bg-blue-50/40">
                                    <td class="border border-slate-200 px-4 py-4">
                                        <div class="font-bold text-slate-800">{{ $materi->pekan ?? 'Pertemuan ' . ($index + 1) }}</div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $materi->judul ?? 'Materi tanpa judul' }}</div>
                                    </td>
                                    <td class="border border-slate-200 px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $isPublished ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $isPublished ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td class="border border-slate-200 px-4 py-4">{{ $countKomponen }} Komponen</td>
                                    <td class="border border-slate-200 px-4 py-4">{{ $materi->updated_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="border border-slate-200 px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" onclick="openEditModal('{{ $materi->id }}', '{{ addslashes($materi->judul ?? '') }}', '{{ addslashes($materi->pekan ?? '') }}', '{{ addslashes($materi->konten ?? '') }}', '{{ $status }}')" class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-400 text-white shadow-sm transition hover:bg-amber-500" title="Edit Materi">
                                                ✎
                                            </button>
                                            <form action="{{ route('guru.materi.destroy', $materi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus Materi" class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600">
                                                    🗑
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div id="materi-form-panel" class="hidden border-t border-slate-200 bg-slate-50 p-6">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <h2 id="editor-title" class="text-lg font-semibold text-slate-800">Tambah / Edit Materi — Pertemuan 1</h2>
                    <button type="button" onclick="closeEditor()" class="text-sm font-medium text-slate-500 hover:text-slate-700">Kembali</button>
                </div>

                <div class="grid gap-6 xl:grid-cols-[minmax(0,1.8fr)_280px]">
                    <form id="materi-form" action="{{ route('guru.materi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5">
                        @csrf
                        <input type="hidden" id="form_method" name="_method" value="POST">
                        <input type="hidden" id="status-input" name="status" value="aktif">

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm text-slate-500">Kelas Tujuan</label>
                                <select name="kelas_id" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-[#4E8FF7] focus:outline-none">
                                    <option value="">Pilih kelas</option>
                                    @foreach ($kelasList ?? [] as $kelas)
                                        <option value="{{ $kelas->id }}" @selected(old('kelas_id') == $kelas->id)>{{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm text-slate-500">Pekan / Pertemuan</label>
                                <input type="text" id="input_pekan" name="pekan" value="{{ old('pekan') }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-[#4E8FF7] focus:outline-none" placeholder="Contoh: Pekan ke-1">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm text-slate-500">Judul Materi</label>
                            <input type="text" id="input_judul" name="judul" value="{{ old('judul') }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-[#4E8FF7] focus:outline-none" placeholder="Pengertian Rasio">
                        </div>

                        <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-700">Komponen 1</span>
                                <label class="flex items-center gap-1.5 text-xs text-slate-500">
                                    <input type="checkbox" checked class="accent-[#4E8FF7]">
                                    Wajib untuk selesai
                                </label>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Konten</label>
                                <textarea id="input_konten" name="konten" rows="6" oninput="updatePreview()" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm text-slate-700 focus:border-[#4E8FF7] focus:outline-none" placeholder="Rich text editor…">{{ old('konten') }}</textarea>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Media</label>
                                <input type="file" name="file_materi" class="w-full rounded-xl border border-dashed border-slate-300 bg-white px-3 py-3 text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-blue-700 file:cursor-pointer">
                            </div>
                        </div>

                        <button type="button" class="text-sm font-medium text-[#4E8FF7]">+ Tambah Komponen</button>

                        <div class="flex gap-3 pt-2">
                            <button type="submit" data-status="draft" class="material-submit-btn rounded-xl border border-[#4E8FF7] bg-white px-5 py-2.5 text-sm text-[#4E8FF7]">Simpan Draft</button>
                            <button type="submit" data-status="aktif" id="submit-button" class="material-submit-btn rounded-xl bg-[#4E8FF7] px-5 py-2.5 text-sm font-medium text-white">Publish</button>
                        </div>
                    </form>

                    <aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="mb-3 text-sm font-semibold text-slate-700">Preview Konten</div>
                        <div class="space-y-2" id="preview-komponen-list">
                            <div class="rounded-xl bg-[#E8F1FE] px-3 py-2 text-sm font-medium text-[#4E8FF7]">Komponen 1</div>
                            <div class="rounded-xl px-3 py-2 text-sm text-slate-500">Komponen 2</div>
                            <div class="rounded-xl px-3 py-2 text-sm text-slate-500">Komponen 3</div>
                            <div class="rounded-xl px-3 py-2 text-sm text-slate-500">Komponen 4</div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </div>

    <script>
        function setActiveTab(tab) {
            const listPanel = document.getElementById('list-panel');
            const editorPanel = document.getElementById('materi-form-panel');
            const isList = tab === 'list';

            if (listPanel) {
                listPanel.classList.toggle('hidden', !isList);
                listPanel.style.display = isList ? '' : 'none';
            }

            if (editorPanel) {
                editorPanel.classList.toggle('hidden', isList);
                editorPanel.style.display = isList ? 'none' : 'block';
            }
        }

        function openCreateForm() {
            setActiveTab('editor');
            const form = document.getElementById('materi-form');
            const title = document.getElementById('editor-title');
            const submitBtn = document.getElementById('submit-button');
            const methodInput = document.getElementById('form_method');
            const statusInput = document.getElementById('status-input');

            if (title) title.innerText = 'Tambah / Edit Materi — Pertemuan 1';
            if (submitBtn) submitBtn.innerText = 'Publish';
            if (methodInput) methodInput.value = 'POST';
            if (statusInput) statusInput.value = 'aktif';
            if (form) {
                form.action = '{{ route('guru.materi.store') }}';
                form.reset();
                if (statusInput) statusInput.value = 'aktif';
            }
            updatePreview();
        }

        function openEditModal(id, judul, pekan, konten, status = 'aktif') {
            setActiveTab('editor');
            const form = document.getElementById('materi-form');
            const editorTitle = document.getElementById('editor-title');
            const submitBtn = document.getElementById('submit-button');
            const methodInput = document.getElementById('form_method');
            const statusInput = document.getElementById('status-input');
            const inputPekan = document.getElementById('input_pekan');
            const inputJudul = document.getElementById('input_judul');
            const inputKonten = document.getElementById('input_konten');

            if (editorTitle) editorTitle.innerText = 'Tambah / Edit Materi — ' + (pekan || 'Pertemuan');
            if (submitBtn) submitBtn.innerText = 'Publish';
            if (methodInput) methodInput.value = 'PUT';
            if (statusInput) statusInput.value = status || 'aktif';
            if (form) form.action = '/guru/materi/' + id;
            if (inputPekan) inputPekan.value = pekan || '';
            if (inputJudul) inputJudul.value = judul || '';
            if (inputKonten) inputKonten.value = konten || '';
            updatePreview();
        }

        function closeEditor() {
            setActiveTab('list');
        }

        document.addEventListener('click', function (event) {
            const button = event.target.closest('.material-submit-btn');
            if (!button) {
                return;
            }

            const statusInput = document.getElementById('status-input');
            if (statusInput) {
                statusInput.value = button.dataset.status || 'aktif';
            }
        });

        function updatePreview() {
            const pekan = document.getElementById('input_pekan')?.value || 'Pertemuan 1';
            const judul = document.getElementById('input_judul')?.value || 'Pengertian Rasio';
            const list = document.getElementById('preview-komponen-list');

            if (list) {
                list.innerHTML = `
                    <div class="rounded-xl bg-[#E8F1FE] px-3 py-2 text-sm font-medium text-[#4E8FF7]">${judul}</div>
                    <div class="rounded-xl px-3 py-2 text-sm text-slate-500">Komponen 2</div>
                    <div class="rounded-xl px-3 py-2 text-sm text-slate-500">Komponen 3</div>
                    <div class="rounded-xl px-3 py-2 text-sm text-slate-500">Komponen 4</div>
                `;
            }

            const editorTitle = document.getElementById('editor-title');
            if (editorTitle) {
                editorTitle.innerText = 'Tambah / Edit Materi — ' + pekan;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            setActiveTab('list');
        });
    </script>
@endsection
