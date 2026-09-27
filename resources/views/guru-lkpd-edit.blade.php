@extends('layouts.guru')

@php
    $title = 'Edit LKPD';$subtitle = 'Edit lembar kerja peserta didik.';
@endphp

@section('content')
<div x-data="lkpdBuilder(@js($lkpd->questions->map(fn($q) => ['key' =>$q->id, 'pertanyaan' => $q->pertanyaan, 'rubrik_jawaban' =>$q->rubrik_jawaban])->values()))" class="mx-auto max-w-7xl">
    <div class="mb-5 flex items-center justify-between">
        <h1 class="text-2xl font-extrabold text-slate-900">Edit LKPD</h1>
        <a href="{{ route('guru.lkpd.index') }}" class="rounded-xl bg-slate-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-700">
            ← Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('guru.lkpd.update', $lkpd) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <section class="rounded-2xl border border-slate-100 bg-white shadow-sm">
                <div class="rounded-t-2xl bg-blue-600 px-5 py-4 text-white">
                    <h2 class="font-extrabold">ℹ️ Informasi LKPD</h2>
                </div>
                <div class="space-y-5 p-5">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Kelas <span class="text-red-500">*</span></label>
                        <select name="kelas_id" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                            @foreach ($kelasGuru as$kelas)
                                <option value="{{ $kelas->id }}" {{ $lkpd->kelas_id ==$kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Judul LKPD <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul', $lkpd->judul) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Batas Waktu <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="deadline" value="{{ old('deadline', $lkpd->deadline?->format('Y-m-d\TH:i')) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">{{ old('deskripsi', $lkpd->deskripsi) }}</textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Instruksi Pengerjaan</label>
                        <textarea name="instruksi" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">{{ old('instruksi', $lkpd->instruksi) }}</textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Dokumen Modul Utuh</label>
                        <input type="file" name="modul" accept=".pdf,.doc,.docx,.ppt,.pptx" class="w-full text-sm">
                    </div>
                </div>
            </section>

            <section class="xl:col-span-2">
                <div class="rounded-2xl border border-slate-100 bg-white shadow-sm">
                    <div class="flex items-center justify-between rounded-t-2xl bg-blue-600 px-5 py-4 text-white">
                        <h2 class="font-extrabold">☷ Daftar Item Soal LKPD</h2>
                    </div>

                    <div class="space-y-5 p-5">
                        <template x-for="(question, index) in questions" :key="question.key">
                            <div class="rounded-2xl border-l-4 border-blue-600 bg-slate-50 p-5 shadow-sm">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="font-bold text-blue-700">Soal No. <span x-text="index + 1"></span></h3>
                                    <button type="button" @click="removeQuestion(index)" x-show="questions.length > 1" class="rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-600">
                                        Hapus
                                    </button>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-700">Pertanyaan Soal <span class="text-red-500">*</span></label>
                                    <textarea :name="`questions[${index}][pertanyaan]`" x-model="question.pertanyaan" rows="3" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm"></textarea>
                                </div>
                                <div class="mt-3">
                                    <label class="mb-2 block text-sm font-bold text-slate-700">Gambar Pendukung Soal</label>
                                    <input type="file" :name="`questions[${index}][gambar]`" accept="image/*" class="w-full text-sm">
                                </div>
                                <div class="mt-3">
                                    <label class="mb-2 block text-sm font-bold text-emerald-700">🔑 Kunci Jawaban / Rubrik</label>
                                    <textarea :name="`questions[${index}][rubrik_jawaban]`" x-model="question.rubrik_jawaban" rows="2" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm"></textarea>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-100 p-5">
                        <button type="button" @click="addQuestion()" class="rounded-xl bg-emerald-500 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-600">
                            + Tambah Soal
                        </button>
                        <button type="submit" class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">
                            Perbarui LKPD
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </form>
</div>

<script>
function lkpdBuilder(initialQuestions = null) {
    return {
        questions: initialQuestions && initialQuestions.length ? initialQuestions : [{ key: Date.now(), pertanyaan: '', rubrik_jawaban: '' }],
        addQuestion() {
            this.questions.push({ key: Date.now() + Math.random(), pertanyaan: '', rubrik_jawaban: '' });
        },
        removeQuestion(index) {
            this.questions.splice(index, 1);
        }
    };
}
</script>
@endsection