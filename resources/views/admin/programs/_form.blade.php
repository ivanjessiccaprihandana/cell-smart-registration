@csrf

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div>
        <label for="name" class="mb-2 block text-sm font-bold text-slate-800">Nama Program</label>
        <input id="name" name="name" type="text" value="{{ old('name', $program->name ?? '') }}" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
            placeholder="Contoh: English for Kids" />
        @error('name')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="program_category_id" class="mb-2 block text-sm font-bold text-slate-800">Kelompok Program</label>
        <select id="program_category_id" name="program_category_id"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            <option value="">Pilih kelompok program...</option>
            @foreach(($categories ?? []) as $value => $label)
                <option value="{{ $value }}" @selected((string) old('program_category_id', $program->program_category_id ?? '') === (string) $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-2 text-xs font-medium text-slate-500">
            Dipakai untuk mengelompokkan program/sub-program di data admin.
            <a href="{{ route('admin.program-categories.create') }}" class="font-bold text-indigo-600 hover:text-indigo-700">Tambah kelompok</a>
        </p>
        @error('program_category_id')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="category" class="mb-2 block text-sm font-bold text-slate-800">Label Kategori</label>
        <select id="category" name="category"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            <option value="">Pilih label...</option>
            @foreach(($categoryLabels ?? []) as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $program->category ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-2 text-xs font-medium text-slate-500">Dipakai untuk filter, badge, dan ringkasan program.</p>
        @error('category')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="quota" class="mb-2 block text-sm font-bold text-slate-800">Kuota</label>
        <input id="quota" name="quota" type="number" min="1" value="{{ old('quota', $program->quota ?? '') }}"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
            placeholder="Contoh: 20" />
        <p class="mt-2 text-xs font-medium text-slate-500">Kosongkan jika kuota tidak dibatasi.</p>
        @error('quota')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="price" class="mb-2 block text-sm font-bold text-slate-800">Harga Reguler</label>
        <div class="flex">
            <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-100 px-4 text-sm font-bold text-slate-600">Rp</span>
            <input id="price" name="price" type="number" min="0" value="{{ old('price', $program->price ?? '') }}"
                class="w-full rounded-r-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                placeholder="Contoh: 750000" />
        </div>
        <p class="mt-2 text-xs font-medium text-slate-500">Masukkan angka tanpa titik, contoh 750000.</p>
        @error('price')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="private_price" class="mb-2 block text-sm font-bold text-slate-800">Harga Private</label>
        <div class="flex">
            <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-100 px-4 text-sm font-bold text-slate-600">Rp</span>
            <input id="private_price" name="private_price" type="number" min="0" value="{{ old('private_price', $program->private_price ?? '') }}"
                class="w-full rounded-r-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                placeholder="Kosongkan jika sama dengan reguler" />
        </div>
        @error('private_price')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="conversation_price" class="mb-2 block text-sm font-bold text-slate-800">Harga Conversation</label>
        <div class="flex">
            <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-100 px-4 text-sm font-bold text-slate-600">Rp</span>
            <input id="conversation_price" name="conversation_price" type="number" min="0" value="{{ old('conversation_price', $program->conversation_price ?? '') }}"
                class="w-full rounded-r-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                placeholder="Kosongkan jika sama dengan reguler" />
        </div>
        @error('conversation_price')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="start_date" class="mb-2 block text-sm font-bold text-slate-800">Tanggal Mulai</label>
        <input id="start_date" name="start_date" type="datetime-local"
            value="{{ old('start_date', isset($program) && $program->start_date ? $program->start_date->format('Y-m-d\TH:i') : '') }}"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100" />
        @error('start_date')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="end_date" class="mb-2 block text-sm font-bold text-slate-800">Tanggal Selesai</label>
        <input id="end_date" name="end_date" type="datetime-local"
            value="{{ old('end_date', isset($program) && $program->end_date ? $program->end_date->format('Y-m-d\TH:i') : '') }}"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100" />
        @error('end_date')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="mb-2 block text-sm font-bold text-slate-800">Status</label>
        <select id="status" name="status" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $program->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6">
    <label for="description" class="mb-2 block text-sm font-bold text-slate-800">Deskripsi</label>
    <textarea id="description" name="description" rows="5"
        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
        placeholder="Tuliskan ringkasan program, target siswa, dan manfaat kelas.">{{ old('description', $program->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.programs.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Batal</a>
    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
        <span class="material-symbols-outlined text-[20px]">save</span>
        Simpan Program
    </button>
</div>
