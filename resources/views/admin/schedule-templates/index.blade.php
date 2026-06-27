@extends('layouts.admin')

@php
    $pageTitle = 'Batch & Pilihan Jadwal';
    $templates = $templates ?? collect();
    $dayLabels = $dayLabels ?? [];
    $selectedStatus = $selectedStatus ?? 'active';
    $templateStats = $templateStats ?? ['active' => 0, 'inactive' => 0, 'all' => 0];
@endphp

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Manajemen Kelas</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Batch & Pilihan Jadwal</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Kelola batch pendaftaran, periode belajar, dan pilihan jadwal yang bisa dipilih calon siswa.</p>
        </div>
        <a href="{{ route('admin.schedule-templates.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Batch Jadwal
        </a>
    </section>

    @if(session('success'))
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">{{ session('success') }}</section>
    @endif

    @if($errors->any())
        <section class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{{ $errors->first() }}</section>
    @endif

    <section class="grid gap-4 md:grid-cols-3">
        <a href="{{ route('admin.schedule-templates.index', ['status' => 'active']) }}" class="rounded-2xl border p-5 shadow-sm transition {{ $selectedStatus === 'active' ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white hover:border-emerald-200' }}">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Aktif</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $templateStats['active'] }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Tampil dan bisa dipilih siswa</p>
        </a>
        <a href="{{ route('admin.schedule-templates.index', ['status' => 'inactive']) }}" class="rounded-2xl border p-5 shadow-sm transition {{ $selectedStatus === 'inactive' ? 'border-slate-300 bg-slate-100' : 'border-slate-200 bg-white hover:border-slate-300' }}">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Nonaktif</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $templateStats['inactive'] }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Arsip jadwal lama</p>
        </a>
        <a href="{{ route('admin.schedule-templates.index', ['status' => 'all']) }}" class="rounded-2xl border p-5 shadow-sm transition {{ $selectedStatus === 'all' ? 'border-indigo-200 bg-indigo-50' : 'border-slate-200 bg-white hover:border-indigo-200' }}">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Semua</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $templateStats['all'] }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Aktif dan arsip</p>
        </a>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-slate-950">
                    {{ $selectedStatus === 'inactive' ? 'Arsip Batch Jadwal' : ($selectedStatus === 'all' ? 'Semua Batch Jadwal' : 'Batch Jadwal Aktif') }}
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $selectedStatus === 'inactive' ? 'Batch nonaktif tidak tampil ke siswa.' : 'Pilihan batch, hari, dan jam belajar yang bisa dipilih siswa sesuai program.' }}
                </p>
            </div>
            @if($selectedStatus !== 'active')
                <a href="{{ route('admin.schedule-templates.index') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 px-4 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Lihat Aktif Saja</a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Program</th>
                        <th class="px-6 py-4">Batch</th>
                        <th class="px-6 py-4">Hari & Jam</th>
                        <th class="px-6 py-4">Tutor</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($templates as $template)
                        @php($days = collect($template->days ?? [])->map(fn ($day) => $dayLabels[$day] ?? $day)->join(' & '))
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-950">{{ $template->program?->name }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @if($template->class_type)
                                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $template->class_type }}</span>
                                    @endif
                                    @if($template->private_package)
                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">{{ $template->private_package }}</span>
                                    @endif
                                    @if($template->level)
                                        <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-bold text-sky-700">{{ $template->level }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $template->batch_name ?: 'Batch berjalan' }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">
                                    Daftar:
                                    {{ $template->registration_start_date?->format('d M Y') ?: '-' }}
                                    -
                                    {{ $template->registration_end_date?->format('d M Y') ?: '-' }}
                                </p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">
                                    Belajar:
                                    {{ $template->learning_start_date?->format('d M Y') ?: '-' }}
                                    -
                                    {{ $template->learning_end_date?->format('d M Y') ?: '-' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $days }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-500">{{ $template->start_time->format('H:i') }} - {{ $template->end_time->format('H:i') }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $template->classRoom?->name ?? $template->room ?? 'Ruang belum diisi' }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-700">{{ $template->tutor?->name ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $template->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <p class="mt-2 text-xs text-slate-500">{{ $template->activeStudentCount() }} / {{ $template->max_students }} siswa</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $template->class_schedules_count }} jadwal siswa dibuat</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.schedule-templates.edit', $template) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-600 hover:text-indigo-600" aria-label="Edit pilihan jadwal">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.schedule-templates.destroy', $template) }}" onsubmit="return confirm('Hapus pilihan jadwal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-rose-500 hover:text-rose-600" aria-label="Hapus pilihan jadwal">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm font-semibold text-slate-500">
                                {{ $selectedStatus === 'active' ? 'Belum ada batch jadwal aktif.' : 'Belum ada batch jadwal pada filter ini.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">{{ $templates->links() }}</div>
        @endif
    </section>
</div>
@endsection
