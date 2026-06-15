@extends('layouts.admin')

@php
    $pageTitle = 'Program';
    $statusStyles = [
        'draft' => 'bg-slate-100 text-slate-700',
        'active' => 'bg-emerald-50 text-emerald-700',
        'inactive' => 'bg-amber-50 text-amber-700',
        'completed' => 'bg-indigo-50 text-indigo-700',
    ];
    $statusLabels = [
        'draft' => 'Draft',
        'active' => 'Aktif',
        'inactive' => 'Nonaktif',
        'completed' => 'Selesai',
    ];
@endphp

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Manajemen Program</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">CRUD Program</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Kelola program kursus dan bimbel yang tersedia di CELL English Course.</p>
        </div>
        <a href="{{ route('admin.programs.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Program
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Daftar Program</h3>
                    <p class="text-sm text-slate-500">Total {{ $programs->total() }} program tersimpan.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Program</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Jadwal</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Harga</th>
                        <th class="px-6 py-4">Kuota</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($programs as $program)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $program->name }}</div>
                                <div class="mt-1 max-w-sm truncate text-xs text-slate-500">{{ $program->description ?: 'Tidak ada deskripsi.' }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $program->category ?: '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                <div>{{ $program->start_date?->format('d M Y') ?: '-' }}</div>
                                <div class="text-xs text-slate-400">{{ $program->end_date?->format('d M Y') ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusStyles[$program->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $statusLabels[$program->status] ?? $program->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <div>{{ $program->formattedPriceForClassType(null) }}</div>
                                @if($program->private_price !== null || $program->conversation_price !== null)
                                    <div class="mt-1 text-xs font-semibold text-slate-500">
                                        Private: {{ $program->formattedPriceForClassType('Private') }}
                                    </div>
                                    <div class="text-xs font-semibold text-slate-500">
                                        Conversation: {{ $program->formattedPriceForClassType('Conversation') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">
                                    {{ $program->registered_users_count }}
                                    @if($program->quota)
                                        / {{ $program->quota }}
                                    @else
                                        pendaftar
                                    @endif
                                </div>
                                <div class="text-xs font-medium {{ $program->quota && $program->remaining_quota <= 0 ? 'text-red-600' : 'text-slate-500' }}">
                                    @if($program->quota)
                                        {{ $program->remaining_quota }} kuota tersisa
                                    @else
                                        Tidak dibatasi
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.programs.edit', $program) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-600 hover:text-indigo-600" aria-label="Edit {{ $program->name }}">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.programs.destroy', $program) }}" onsubmit="return confirm('Hapus program {{ $program->name }}? Data enrollment terkait juga bisa ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-red-500 hover:text-red-600" aria-label="Hapus {{ $program->name }}">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                    <span class="material-symbols-outlined">school</span>
                                </div>
                                <p class="mt-4 font-bold text-slate-900">Belum ada program</p>
                                <p class="mt-1 text-sm text-slate-500">Mulai dengan menambahkan program pertama.</p>
                                <a href="{{ route('admin.programs.create') }}" class="mt-5 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700">Tambah Program</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($programs->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $programs->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
