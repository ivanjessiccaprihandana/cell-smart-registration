@csrf

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div>
        <label for="section" class="mb-2 block text-sm font-bold text-slate-800">Section</label>
        <input id="section" name="section" type="text" value="{{ old('section', $question->section ?? '') }}" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
            placeholder="Contoh: Grammar, Vocabulary, Reading" />
        @error('section')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="level" class="mb-2 block text-sm font-bold text-slate-800">Level</label>
        <select id="level" name="level" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            @foreach($levels as $value => $label)
                <option value="{{ $value }}" @selected(old('level', $question->level ?? 'Beginner') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('level')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="correct_option" class="mb-2 block text-sm font-bold text-slate-800">Jawaban Benar</label>
        <select id="correct_option" name="correct_option" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            @foreach(['A', 'B', 'C', 'D'] as $index => $label)
                <option value="{{ $index }}" @selected((string) old('correct_option', $question->correct_option ?? 0) === (string) $index)>Pilihan {{ $label }}</option>
            @endforeach
        </select>
        @error('correct_option')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="sort_order" class="mb-2 block text-sm font-bold text-slate-800">Urutan</label>
        <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $question->sort_order ?? 0) }}"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100" />
        @error('sort_order')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6">
    <label for="question_text" class="mb-2 block text-sm font-bold text-slate-800">Teks Soal</label>
    <textarea id="question_text" name="question_text" rows="4" required
        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
        placeholder="Tulis pertanyaan placement test...">{{ old('question_text', $question->question_text ?? '') }}</textarea>
    @error('question_text')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
</div>

<div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
    @foreach(['A', 'B', 'C', 'D'] as $index => $label)
        <div>
            <label for="option_{{ $index }}" class="mb-2 block text-sm font-bold text-slate-800">Pilihan {{ $label }}</label>
            <input id="option_{{ $index }}" name="options[{{ $index }}]" type="text" value="{{ old("options.$index", $question->options[$index] ?? '') }}" required
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100" />
            @error("options.$index")<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
        </div>
    @endforeach
</div>

<div class="mt-6">
    <label for="explanation" class="mb-2 block text-sm font-bold text-slate-800">Penjelasan Opsional</label>
    <textarea id="explanation" name="explanation" rows="3"
        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
        placeholder="Penjelasan jawaban jika diperlukan.">{{ old('explanation', $question->explanation ?? '') }}</textarea>
    @error('explanation')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
</div>

<label class="mt-6 flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">
    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_active', $question->is_active ?? true))>
    Aktifkan soal ini
</label>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.placement.questions.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Batal</a>
    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
        <span class="material-symbols-outlined text-[20px]">save</span>
        Simpan Soal
    </button>
</div>
