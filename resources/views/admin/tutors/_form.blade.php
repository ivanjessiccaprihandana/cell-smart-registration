@csrf

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div>
        <label for="name" class="mb-2 block text-sm font-bold text-slate-800">Nama Tutor</label>
        <input id="name" name="name" type="text" value="{{ old('name', $tutor->name ?? '') }}" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
            placeholder="Contoh: Kak Rina" />
        @error('name')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="program_id" class="mb-2 block text-sm font-bold text-slate-800">Program Tutor</label>
        <select id="program_id" name="program_id"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            <option value="">Semua program</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" @selected((string) old('program_id', $tutor->program_id ?? '') === (string) $program->id)>{{ $program->name }}</option>
            @endforeach
        </select>
        <p class="mt-2 text-xs font-medium text-slate-500">Kosongkan jika tutor bisa mengajar semua program.</p>
        @error('program_id')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="email" class="mb-2 block text-sm font-bold text-slate-800">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $tutor->email ?? '') }}"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
            placeholder="tutor@email.com" />
        @error('email')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="phone" class="mb-2 block text-sm font-bold text-slate-800">No. WhatsApp</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $tutor->phone ?? '') }}"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
            placeholder="08123456789" />
        @error('phone')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="level" class="mb-2 block text-sm font-bold text-slate-800">Level Tutor</label>
        <select id="level" name="level"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            <option value="">Semua level</option>
            @foreach($levels as $value => $label)
                <option value="{{ $value }}" @selected(old('level', $tutor->level ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-2 text-xs font-medium text-slate-500">Kosongkan jika tutor bisa mengajar semua level.</p>
        @error('level')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 lg:self-end">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_active', $tutor->is_active ?? true))>
        Aktif
    </label>
</div>

<div class="mt-6">
    <label for="notes" class="mb-2 block text-sm font-bold text-slate-800">Catatan</label>
    <textarea id="notes" name="notes" rows="4"
        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
        placeholder="Keahlian, jadwal tersedia, atau catatan internal.">{{ old('notes', $tutor->notes ?? '') }}</textarea>
    @error('notes')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
</div>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.tutors.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Batal</a>
    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
        <span class="material-symbols-outlined text-[20px]">save</span>
        Simpan Tutor
    </button>
</div>
