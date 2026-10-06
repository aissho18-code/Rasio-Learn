@extends(($isAdmin ?? false) ? 'layouts.admin' : 'layouts.app')

@php
    $optionValues = array_pad(array_slice(old('options', $question->options ?? ['', '', '', '']), 0, 4), 4, '');
    $correctAnswers = old('correct_answers', $question->correct_answer ?? []);
    $correctChoiceIndex = old('correct_option', array_search($correctAnswers[0] ?? '', $optionValues, true));
    $correctComplexIndexes = old('correct_options', array_keys(array_filter($optionValues, fn ($option) => in_array($option, $correctAnswers, true))));
@endphp

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <a href="{{ route(($isAdmin ?? false) ? 'admin.exams.edit' : 'guru.ujian.questions.index', $exam->id) }}" class="text-xs font-semibold text-blue-700 hover:underline">← {{ ($isAdmin ?? false) ? 'Kembali ke Kelola Ujian' : 'Kembali ke Kelola Soal' }}</a>
        <h1 class="mt-2 text-xl font-extrabold text-slate-900">{{ $question->exists ? 'Edit Soal' : 'Tambah Soal' }}</h1>
        <p class="mt-1 text-xs text-slate-500">{{ $exam->title }}</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ $question->exists ? route(($isAdmin ?? false) ? 'admin.exams.questions.update' : 'guru.ujian.questions.update', [$exam->id, $question->id]) : route(($isAdmin ?? false) ? 'admin.exams.questions.store' : 'guru.ujian.questions.store', $exam->id) }}" class="space-y-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($question->exists) @method('PUT') @endif

        <div>
            <label for="prompt" class="mb-1 block text-xs font-bold text-slate-700">Pertanyaan</label>
            <textarea id="prompt" name="prompt" required rows="4" class="w-full rounded-lg border border-slate-300 p-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('prompt', $question->prompt) }}</textarea>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <label for="type" class="mb-1 block text-xs font-bold text-slate-700">Tipe Soal</label>
                <select id="type" name="type" required class="w-full rounded-lg border border-slate-300 p-3 text-xs">
                    @foreach ([
                        'multiple_choice' => 'Pilihan Ganda',
                        'multiple_response' => 'Pilihan Ganda Kompleks',
                        'true_false' => 'Benar/Salah',
                        'short_answer' => 'Isian Singkat',
                        'essay' => 'Esai',
                    ] as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $question->type ?? ($exam->exam_model === 'essay' ? 'essay' : 'multiple_choice')) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="points" class="mb-1 block text-xs font-bold text-slate-700">Bobot Nilai</label>
                <input id="points" name="points" type="number" min="0.01" max="1000" step="0.01" required value="{{ old('points', $question->points ?? 1) }}" class="w-full rounded-lg border border-slate-300 p-3 text-xs">
            </div>
        </div>

        <fieldset data-question-type="multiple_choice" class="space-y-3 rounded-lg bg-slate-50 p-4">
            <legend class="px-1 text-xs font-bold text-slate-800">Pilihan jawaban · pilih satu jawaban benar</legend>
            @foreach (['A', 'B', 'C', 'D'] as $index => $letter)
                <label class="flex items-center gap-3">
                    <input type="radio" name="correct_option" value="{{ $index }}" @checked((string) $correctChoiceIndex === (string) $index) class="text-blue-600">
                    <span class="w-5 text-xs font-bold text-slate-500">{{ $letter }}.</span>
                    <input name="options[]" value="{{ $optionValues[$index] }}" maxlength="1000" placeholder="Pilihan {{ $letter }}" class="flex-1 rounded-md border border-slate-300 px-3 py-2 text-xs">
                </label>
            @endforeach
        </fieldset>

        <fieldset data-question-type="multiple_response" class="space-y-3 rounded-lg bg-slate-50 p-4">
            <legend class="px-1 text-xs font-bold text-slate-800">Pilihan jawaban · centang semua jawaban benar</legend>
            @foreach (['A', 'B', 'C', 'D'] as $index => $letter)
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="correct_options[]" value="{{ $index }}" @checked(in_array((string) $index, array_map('strval', $correctComplexIndexes), true)) class="rounded text-blue-600">
                    <span class="w-5 text-xs font-bold text-slate-500">{{ $letter }}.</span>
                    <input name="options[]" value="{{ $optionValues[$index] }}" maxlength="1000" placeholder="Pilihan {{ $letter }}" class="flex-1 rounded-md border border-slate-300 px-3 py-2 text-xs">
                </label>
            @endforeach
        </fieldset>

        <fieldset data-question-type="true_false" class="rounded-lg bg-slate-50 p-4">
            <legend class="px-1 text-xs font-bold text-slate-800">Kunci jawaban</legend>
            <div class="mt-2 flex gap-6 text-xs">
                @foreach (['Benar', 'Salah'] as $choice)
                    <label class="flex items-center gap-2"><input type="radio" name="correct_option" value="{{ $choice }}" @checked(old('correct_option', $correctAnswers[0] ?? '') === $choice) class="text-blue-600">{{ $choice }}</label>
                @endforeach
            </div>
        </fieldset>

        <fieldset data-question-type="short_answer" class="rounded-lg bg-slate-50 p-4">
            <label for="answer_key" class="mb-1 block text-xs font-bold text-slate-800">Jawaban benar</label>
            <input id="answer_key" name="answer_key" value="{{ old('answer_key', $correctAnswers[0] ?? '') }}" maxlength="1000" class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs">
        </fieldset>

        <div>
            <label for="explanation" class="mb-1 block text-xs font-bold text-slate-700">Pembahasan <span class="font-normal text-slate-400">(opsional)</span></label>
            <textarea id="explanation" name="explanation" rows="3" class="w-full rounded-lg border border-slate-300 p-3 text-xs">{{ old('explanation', $question->explanation) }}</textarea>
        </div>
        <input type="hidden" name="position" value="{{ old('position', $question->position ?? $exam->questions()->count()) }}">

        <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
            <a href="{{ route(($isAdmin ?? false) ? 'admin.exams.edit' : 'guru.ujian.questions.index', $exam->id) }}" class="rounded-lg px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</a>
            <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-800">Simpan Soal</button>
        </div>
    </form>
</div>

<script>
    const questionType = document.getElementById('type');
    const questionFields = [...document.querySelectorAll('[data-question-type]')];
    function updateQuestionFields() {
        questionFields.forEach((field) => {
            const active = field.dataset.questionType === questionType.value;
            field.hidden = !active;
            field.disabled = !active;
        });
    }
    questionType.addEventListener('change', updateQuestionFields);
    updateQuestionFields();
</script>
@endsection