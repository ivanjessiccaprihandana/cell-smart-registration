I be sad she won't do bath the one on the trail like the one that just do it when we had to come straight wheel in the task when I hold to the tables the secret is the moment heel and the s and the song carry on and feel the human trusting all I don't trust might give her chances given a chance is giving up I don't know sans is giving up with dance is giving up be giving up the ceiling like us when it rains you try to take smells every so dangerous I've been inside this life it's the slide it's the side you think yeah to say yeah long rolling do engine drug show ing@extends('layouts.admin')

@php($pageTitle = 'Ruang Kelas')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Manajemen Kelas</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Ruang Kelas</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Kelola ruang English dan BIMBEL. Kapasitas CELL maksimal 8 siswa per kelas, sedangkan Private tetap 1 siswa.</p>
        </div>
        <a href="{{ route('admin.class-rooms.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Ruang
        </a>
    </section>

    @if(session('success'))
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">{{ session('success') }}</section>
    @endif

    @if($errors->any())
        <section class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{{ $errors->first() }}</section>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Ruang</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Kapasitas</th>
                        <th class="px-6 py-4">Pemakaian</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rooms as $room)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-950">{{ $room->name }}</p>
                                @if($room->notes)
                                    <p class="mt-1 text-xs text-slate-500">{{ $room->notes }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">{{ $roomCategories[$room->category] ?? $room->category }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $room->capacity }} siswa</td>
                            <td class="px-6 py-4 text-slate-600">
                                <p>{{ $room->schedule_templates_count }} pilihan jadwal</p>
                                <p class="mt-1 text-xs">{{ $room->class_schedules_count }} jadwal siswa</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $room->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $room->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.class-rooms.show', $room) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-600 hover:text-indigo-600" aria-label="Lihat isi ruang">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a href="{{ route('admin.class-rooms.edit', $room) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-600 hover:text-indigo-600" aria-label="Edit ruang">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.class-rooms.destroy', $room) }}" onsubmit="return confirm('Hapus ruang kelas ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-rose-500 hover:text-rose-600" aria-label="Hapus ruang">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm font-semibold text-slate-500">Belum ada ruang kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rooms->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">{{ $rooms->links() }}</div>
        @endif
    </section>
</div>
@endsection
