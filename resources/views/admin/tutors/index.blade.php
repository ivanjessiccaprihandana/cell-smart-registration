@extends('layouts.admin')

@php($pageTitle = 'Tutor')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Manajemen Tutor</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Tutor</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Kelola tutor berdasarkan program dan level agar mudah dipilih saat membuat jadwal siswa.</p>
        </div>
        <a href="{{ route('admin.tutors.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Tutor
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('tutor'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">
            {{ $errors->first('tutor') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Tutor</th>
                        <th class="px-6 py-4">Program</th>
                        <th class="px-6 py-4">Level</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Jadwal</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tutors as $tutor)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-950">{{ $tutor->name }}</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">{{ $tutor->email ?: '-' }}{{ $tutor->phone ? ' / ' . $tutor->phone : '' }}</p>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $tutor->program?->name ?: 'Semua program' }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $tutor->level ?: 'Semua level' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $tutor->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $tutor->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $tutor->class_schedules_count }} jadwal</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.tutors.edit', $tutor) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-600 hover:text-indigo-600" aria-label="Edit tutor">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.tutors.destroy', $tutor) }}" onsubmit="return confirm('Hapus tutor {{ $tutor->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-rose-500 hover:text-rose-600" aria-label="Hapus tutor">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm font-semibold text-slate-500">Belum ada tutor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tutors->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $tutors->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
