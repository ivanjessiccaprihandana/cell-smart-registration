@extends('layouts.admin')

@php
    $pageTitle = 'Kategori Tampilan';
@endphp

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Manajemen Program</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Kategori Tampilan</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Kelola kategori dan sub-kategori yang muncul di pilihan Kategori Tampilan saat membuat program.</p>
        </div>
        <a href="{{ route('admin.program-categories.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Kategori
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('category'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">
            {{ $errors->first('category') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Daftar Kategori</h3>
                    <p class="text-sm text-slate-500">Total {{ $categories->total() }} kategori tersimpan.</p>
                </div>
                <a href="{{ route('admin.programs.create') }}" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 hover:text-indigo-700">
                    <span class="material-symbols-outlined text-[18px]">school</span>
                    Tambah Program
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Parent</th>
                        <th class="px-6 py-4">Urutan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Dipakai</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $category->name }}</div>
                                <div class="mt-1 max-w-sm truncate text-xs text-slate-500">{{ $category->description ?: 'Tidak ada deskripsi.' }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $category->parent?->name ?: 'Kategori utama' }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $category->sort_order }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $category->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <div>{{ $category->programs_count }} program</div>
                                <div class="text-xs text-slate-400">{{ $category->children_count }} sub-kategori</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.program-categories.edit', $category) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-600 hover:text-indigo-600" aria-label="Edit {{ $category->name }}">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.program-categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori {{ $category->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-red-500 hover:text-red-600" aria-label="Hapus {{ $category->name }}">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                    <span class="material-symbols-outlined">category</span>
                                </div>
                                <p class="mt-4 font-bold text-slate-900">Belum ada kategori tampilan</p>
                                <p class="mt-1 text-sm text-slate-500">Tambahkan kategori agar dropdown Kategori Tampilan bisa dipilih.</p>
                                <a href="{{ route('admin.program-categories.create') }}" class="mt-5 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700">Tambah Kategori</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $categories->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
