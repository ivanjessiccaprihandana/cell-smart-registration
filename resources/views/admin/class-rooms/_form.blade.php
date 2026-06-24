@csrf

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Nama Ruang</label>
        <input id="name" name="name" type="text" value="{{ old('name', $room->name ?? '') }}" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
            placeholder="Contoh: English Room 1">
        @error('name')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="category" class="mb-2 block text-sm font-bold text-slate-700">Kategori Ruang</label>
        <select id="category" name="category" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            @foreach($roomCategories as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $room->category ?? 'English') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('category')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="capacity" class="mb-2 block text-sm font-bold text-slate-700">Kapasitas Ruang</label>
        <input id="capacity" name="capacity" type="number" min="1" max="8" value="{{ old('capacity', $room->capacity ?? 8) }}" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
        <p class="mt-2 text-xs font-medium text-slate-500">Data CELL: maksimal 8 siswa per kelas. Private tetap 1 siswa saat dibuat di pilihan jadwal.</p>
        @error('capacity')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm font-bold text-slate-800">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_active', $room->is_active ?? true))>
        Aktif
    </label>
</div>

<div class="mt-6">
    <label for="notes" class="mb-2 block text-sm font-bold text-slate-700">Catatan</label>
    <textarea id="notes" name="notes" rows="3"
        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
        placeholder="Catatan opsional untuk ruang ini.">{{ old('notes', $room->notes ?? '') }}</textarea>
    @error('notes')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
</div>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.class-rooms.index') }}" class="inline-flex h-12 items-center justify-center rounded-lg border border-slate-300 px-5 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Batal</a>
    <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-6 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
        <span class="material-symbols-outlined text-[18px]">save</span>
        Simpan Ruang
    </button>
</div>
