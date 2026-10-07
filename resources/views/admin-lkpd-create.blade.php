@extends('layouts.app')

@section('title', 'Tambah LKPD')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Tambah LKPD</h1>
        <p class="text-sm text-slate-500 mt-1">
            Buat LKPD baru untuk siswa.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3">
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.lkpd.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Informasi utama --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-slate-800 mb-5">
                Informasi LKPD
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Judul LKPD
                    </label>
                    <input
                        type="text"
                        name="judul"
                        value="{{ old('judul') }}"
                        required
                        placeholder="Contoh: LKPD Rasio"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Kelas
                    </label>
                    <select
                        name="kelas_id"
                        required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                        <option value="">Pilih Kelas</option>

                        @foreach($kelasList as $kelas)
                            <option
                                value="{{ $kelas->id }}"
                                {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Guru
                    </label>
                    <select
                        name="guru_id"
                        required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                        <option value="">Pilih Guru</option>

                        @foreach($guruList as $guru)
                            <option
                                value="{{ $guru->id }}"
                                {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                {{ $guru->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Deadline
                    </label>
                    <input
                        type="datetime-local"
                        name="deadline"
                        value="{{ old('deadline') }}"
                        required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                </div>

            </div>

            <div class="mt-5">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Deskripsi
                </label>
                <textarea
                    name="deskripsi"
                    rows="3"
                    placeholder="Deskripsi singkat LKPD..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none resize-none">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="mt-5">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Instruksi
                </label>
                <textarea
                    name="instruksi"
                    rows="4"
                    placeholder="Tuliskan instruksi pengerjaan untuk siswa..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none resize-none">{{ old('instruksi') }}</textarea>
            </div>

            <div class="mt-5">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Modul / Materi Pendukung
                </label>
                <input
                    type="file"
                    name="modul"
                    accept=".pdf,.doc,.docx,.ppt,.pptx"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-slate-50">
                <p class="text-xs text-slate-400 mt-2">
                    Format: PDF, DOC, DOCX, PPT, PPTX. Maksimal 10 MB.
                </p>
            </div>
        </div>

        {{-- Pertanyaan --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-slate-800">
                        Pertanyaan LKPD
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Tambahkan minimal satu pertanyaan.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="addQuestion()"
                    class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                    + Tambah Pertanyaan
                </button>
            </div>

            <div id="questions-container" class="space-y-4">

                <div class="question-item rounded-xl border border-slate-200 p-5 bg-slate-50">

                    <div class="flex items-center justify-between mb-4">
                        <span class="question-number text-sm font-bold text-slate-700">
                            Pertanyaan 1
                        </span>

                        <button
                            type="button"
                            onclick="removeQuestion(this)"
                            class="text-xs font-semibold text-red-600 hover:text-red-700">
                            Hapus
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">
                            Pertanyaan
                        </label>

                        <textarea
                            name="questions[0][pertanyaan]"
                            rows="4"
                            required
                            placeholder="Tuliskan pertanyaan..."
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none resize-none"></textarea>
                    </div>

                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-2">
                            Gambar Soal (opsional)
                        </label>

                        <input
                            type="file"
                            name="questions[0][gambar]"
                            accept="image/*"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-white">
                    </div>
<div class="mt-4">
    <label class="block text-xs font-semibold text-slate-700 mb-2">
        Rubrik Jawaban
    </label>
    <textarea
        name="questions[0][rubrik_jawaban]"
        rows="3"
        required
        placeholder="Tuliskan kriteria jawaban yang dianggap benar..."
        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none resize-none"></textarea>
    <p class="text-xs text-slate-400 mt-1">
        Digunakan sebagai acuan penilaian jawaban siswa.
    </p>
</div>
                </div>

            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center justify-end gap-3">
            <a
                href="{{ route('admin.lkpd.index') }}"
                class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Batal
            </a>

            <button
                type="submit"
                class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                Simpan LKPD
            </button>
        </div>

    </form>
</div>

<script>
let questionIndex = 1;

function addQuestion() {
    const container = document.getElementById('questions-container');

    const item = document.createElement('div');

    item.className = 'question-item rounded-xl border border-slate-200 p-5 bg-slate-50';

    item.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <span class="question-number text-sm font-bold text-slate-700">
                Pertanyaan ${questionIndex + 1}
            </span>

            <button
                type="button"
                onclick="removeQuestion(this)"
                class="text-xs font-semibold text-red-600 hover:text-red-700">
                Hapus
            </button>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-2">
                Pertanyaan
            </label>

            <textarea
                name="questions[${questionIndex}][pertanyaan]"
                rows="4"
                required
                placeholder="Tuliskan pertanyaan..."
                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none resize-none"></textarea>
        </div>

        <div class="mt-4">
            <label class="block text-xs font-semibold text-slate-700 mb-2">
                Gambar Soal (opsional)
            </label>

            <input
                type="file"
                name="questions[${questionIndex}][gambar]"
                accept="image/*"
                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-white">
        </div>
        <div class="mt-4">
    <label class="block text-xs font-semibold text-slate-700 mb-2">
        Rubrik Jawaban
    </label>
    <textarea
        name="questions[${questionIndex}][rubrik_jawaban]"
        rows="3"
        required
        placeholder="Tuliskan kriteria jawaban yang dianggap benar..."
        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none resize-none"></textarea>
    <p class="text-xs text-slate-400 mt-1">
        Digunakan sebagai acuan penilaian jawaban siswa.
    </p>
</div>
    `;

    container.appendChild(item);

    questionIndex++;
    updateQuestionNumbers();
}

function removeQuestion(button) {
    const items = document.querySelectorAll('.question-item');

    if (items.length <= 1) {
        alert('Minimal harus ada satu pertanyaan.');
        return;
    }

    button.closest('.question-item').remove();

    updateQuestionNumbers();
}

function updateQuestionNumbers() {
    document.querySelectorAll('.question-item').forEach((item, index) => {
        const number = item.querySelector('.question-number');

        if (number) {
            number.textContent = `Pertanyaan ${index + 1}`;
        }
    });
}
</script>
@endsection