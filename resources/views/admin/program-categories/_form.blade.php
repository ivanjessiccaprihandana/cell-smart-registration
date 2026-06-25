@csrf

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div>
        <label for="name" class="mb-2 block text-sm font-bold text-slate-800">Nama Kelompok</label>
        <input id="name" name="name" type="text" value="{{ old('name', $category->name ?? '') }}" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
            placeholder="Contoh: Bahasa Inggris / English for Kids" />
        @error('name')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
        <label class="flex cursor-pointer items-center gap-3 text-sm font-bold text-slate-800">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                @checked(old('is_active', $category->is_active ?? true))>
            Aktif
        </label>
    </div>
</div>

<div class="mt-6">
    <label for="description" class="mb-2 block text-sm font-bold text-slate-800">Deskripsi</label>
    <textarea id="description" name="description" rows="4"
        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
        placeholder="Tuliskan keterangan singkat kategori.">{{ old('description', $category->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.program-categories.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Batal</a>
    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
        <span class="material-symbols-outlined text-[20px]">save</span>
        Simpan Kelompok
    </button>
</div>
