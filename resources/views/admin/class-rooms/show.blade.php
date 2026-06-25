@extends('layouts.admin')

@php
    $pageTitle = 'Isi Ruang Kelas';
    $templates = $room->scheduleTemplates ?? collect();
    $upcomingSchedules = $room->classSchedules ?? collect();
    $totalStudents = $templates
        ->flatMap(fn ($template) => $template->preferences->whereIn('status', ['pending', 'assigned'])->pluck('user_id'))
        ->unique()
        ->count();
    $statusLabels = [
        'pending' => ['Menunggu Pembayaran', 'bg-amber-50 text-amber-700'],
        'assigned' => ['Terjadwal', 'bg-emerald-50 text-emerald-700'],
        'rejected' => ['Tidak Aktif', 'bg-slate-100 text-slate-600'],
    ];
@endphp

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-start gap-3">
                <a href="{{ route('admin.class-rooms.index') }}" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 hover:text-indigo-600" aria-label="Kembali">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <p class="text-sm font-semibold text-indigo-600">Ruang Kelas</p>
                    <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">{{ $room->name }}</h2>
                    <p class="mt-1 text-sm font-semibold text-slate-500">{{ $room->category }} / kapasitas ruang {{ $room->capacity }} siswa</p>
                </div>
            </div>
            <a href="{{ route('admin.class-rooms.edit', $room) }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Edit Ruang
            </a>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Batch Jadwal</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $templates->count() }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Siswa Terhubung</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $totalStudents }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Jadwal Mendatang</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $upcomingSchedules->count() }}</p>
        </div>
    </section>

    <section class="space-y-4">
        <div>
            <h3 class="text-xl font-extrabold text-slate-950">Isi Kelas Per Batch Jadwal</h3>
            <p class="mt-1 text-sm font-medium text-slate-500">Daftar ini membantu admin melihat siapa saja yang masuk ke setiap pilihan jadwal di ruang ini.</p>
        </div>

        @forelse($templates as $template)
            @php
                $days = collect($template->days ?? [])->map(fn ($day) => $dayLabels[$day] ?? $day)->join(' & ');
                $activePreferences = $template->preferences->whereIn('status', ['pending', 'assigned'])->values();
                $usedSeats = $activePreferences->pluck('user_id')->unique()->count();
            @endphp
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50 px-6 py-5">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">{{ $template->program?->name ?? 'Program' }}</p>
                            <h4 class="mt-1 text-lg font-extrabold text-slate-950">{{ $days }} / {{ $template->start_time->format('H:i') }} - {{ $template->end_time->format('H:i') }}</h4>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @if($template->class_type)
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">{{ $template->class_type }}</span>
                                @endif
                                @if($template->level)
                                    <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">{{ $template->level }}</span>
                                @endif
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Tutor: {{ $template->tutor?->name ?? 'Belum dipilih' }}</span>
                            </div>
                        </div>
                        <div class="rounded-xl bg-white px-4 py-3 text-right shadow-sm">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Kapasitas</p>
                            <p class="mt-1 text-lg font-extrabold text-indigo-700">{{ $usedSeats }} / {{ $template->max_students }} siswa</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                        <thead class="bg-white text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Siswa</th>
                                <th class="px-6 py-4">Kontak</th>
                                <th class="px-6 py-4">Status Jadwal</th>
                                <th class="px-6 py-4">Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($activePreferences as $preference)
                                @php($status = $statusLabels[$preference->status] ?? [$preference->status, 'bg-slate-100 text-slate-600'])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-950">{{ $preference->user?->name ?? '-' }}</p>
                                        <p class="mt-1 text-xs font-medium text-slate-500">{{ $preference->user?->email ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <p>{{ $preference->user?->whatsapp ?: '-' }}</p>
                                        <p class="mt-1 max-w-xs truncate text-xs">{{ $preference->user?->address ?: '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $status[1] }}">{{ $status[0] }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-700">{{ $preference->user?->payment_status ?: '-' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm font-semibold text-slate-500">Belum ada siswa pada pilihan jadwal ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        @empty
            <section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-sm font-semibold text-slate-500">
                Belum ada pilihan jadwal yang memakai ruang ini.
            </section>
        @endforelse
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <h3 class="text-lg font-extrabold text-slate-950">Jadwal Mendatang di Ruang Ini</h3>
            <p class="mt-1 text-sm text-slate-500">Jadwal yang sudah terbentuk setelah pembayaran siswa disetujui admin.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Program</th>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Tutor</th>
                        <th class="px-6 py-4">Jam</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($upcomingSchedules as $schedule)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $schedule->class_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-950">{{ $schedule->program?->name ?? '-' }}</p>
                                @if($schedule->class_type)
                                    <p class="mt-1 inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $schedule->class_type }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-700">{{ $schedule->student?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-700">{{ $schedule->tutor?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-700">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm font-semibold text-slate-500">Belum ada jadwal mendatang di ruang ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
