@extends('layouts.admin')

@php
    $pageTitle = 'Kelompok Program';
@endphp

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Manajemen Program</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Kelompok Program</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Kelola pengelompokan layanan agar program lebih mudah diatur dan ditampilkan.</p>
        </div>
   
    </section>

    <section class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">Kelompok Utama</p>
            <h3 class="mt-2 text-lg font-extrabold text-slate-950">Bahasa Inggris, Test Preparation, BIMBEL</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Digunakan sebagai pembagian layanan utama di CELL.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">Data Program</p>
            <h3 class="mt-2 text-lg font-extrabold text-slate-950">Kids, Teens, TOEFL, BIMBEL SD</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Nama layanan detail tetap dikelola di menu Program.</p>
        </div>
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
                    <h3 class="text-lg font-bold text-slate-900">Daftar Kelompok Program</h3>
                    <p class="text-sm text-slate-500">Total {{ $categories->total() }} kelompok tersimpan.</p>
                </div>
                     <a href="{{ route('admin.program-categories.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Kelompok
        </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Kelompok</th>
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
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $category->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <div>{{ $category->programs_count }} program</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.program-categories.edit', $category) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-600 hover:text-indigo-600" aria-label="Edit {{ $category->name }}">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.program-categories.destroy', $category) }}" onsubmit="return confirm('Hapus kelompok {{ $category->name }}?');">
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
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                    <span class="material-symbols-outlined">category</span>
                                </div>
                                <p class="mt-4 font-bold text-slate-900">Belum ada kelompok program</p>
                                <p class="mt-1 text-sm text-slate-500">Tambahkan kelompok agar program bisa ditampilkan dan dikelola dengan jelas.</p>
                                <a href="{{ route('admin.program-categories.create') }}" class="mt-5 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700">Tambah Kelompok</a>
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
