@extends('layouts.admin')

@php
    $pageTitle = 'Jadwal Kelas';
    $scheduleStyle = 'border-indigo-200 bg-indigo-50 text-indigo-800';
@endphp

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Manajemen Kelas</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Jadwal Kelas</h2>
            </div>
            <a href="{{ route('admin.schedules.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Tambah Jadwal
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.schedules.index', ['week' => $previousWeek]) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-500 hover:text-indigo-600" aria-label="Minggu sebelumnya">
                <span class="material-symbols-outlined">chevron_left</span>
            </a>
            <h3 class="text-lg font-extrabold text-slate-950">
                {{ $weekStart->format('d M') }} - {{ $weekStart->copy()->addDays(6)->format('d M') }}
            </h3>
            <a href="{{ route('admin.schedules.index', ['week' => $nextWeek]) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-500 hover:text-indigo-600" aria-label="Minggu berikutnya">
                <span class="material-symbols-outlined">chevron_right</span>
            </a>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="grid min-h-[28rem] grid-cols-1 divide-y divide-slate-200 md:grid-cols-7 md:divide-x md:divide-y-0">
            @foreach($weekDays as $day)
                <div class="{{ $day['is_today'] ? 'bg-emerald-50/60' : 'bg-white' }}">
                    <div class="{{ $day['is_today'] ? 'bg-teal-600 text-white' : 'bg-slate-50 text-slate-900' }} border-b border-slate-200 px-4 py-4 text-center">
                        <p class="text-sm font-extrabold">{{ $day['day'] }}</p>
                        <p class="mt-1 text-xs font-semibold {{ $day['is_today'] ? 'text-teal-50' : 'text-slate-500' }}">{{ $day['date_label'] }}</p>
                    </div>

                    <div class="min-h-[23rem] space-y-3 p-3">
                        @php($daySchedules = $schedules->filter(fn ($schedule) => $schedule->class_date->isSameDay($day['date'])))

                        @forelse($daySchedules as $schedule)
                            <article class="rounded-xl border p-3 shadow-sm {{ $scheduleStyle }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-extrabold">{{ $schedule->program->name }}</p>
                                        @if($schedule->class_type)
                                            <p class="mt-1 inline-flex rounded-full bg-white/70 px-2 py-0.5 text-[11px] font-extrabold">{{ $schedule->class_type }}</p>
                                        @endif
                                        <p class="mt-1 text-xs font-bold opacity-80">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</p>
                                    </div>
                                    <span class="rounded-full bg-white/70 px-2 py-1 text-[11px] font-extrabold">{{ $schedule->students->count() }}</span>
                                </div>
                                <p class="mt-2 text-xs font-semibold opacity-80">{{ $schedule->room ?: 'Ruang belum diisi' }}</p>

                                @if($schedule->students->isNotEmpty())
                                    <div class="mt-3 space-y-1">
                                        @foreach($schedule->students->take(4) as $student)
                                            <p class="truncate rounded-lg bg-white/70 px-2 py-1 text-xs font-bold">{{ $student->name }}</p>
                                        @endforeach
                                        @if($schedule->students->count() > 4)
                                            <p class="px-2 text-xs font-bold opacity-70">+{{ $schedule->students->count() - 4 }} siswa lainnya</p>
                                        @endif
                                    </div>
                                @else
                                    <p class="mt-3 text-center text-xs font-semibold opacity-60">Belum ada siswa</p>
                                @endif
                            </article>
                        @empty
                            <div class="flex h-24 items-center justify-center text-sm font-semibold text-slate-400">
                                Tidak ada kelas
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div>
            <h3 class="text-lg font-extrabold text-slate-950">Daftar Jadwal Program</h3>
            <p class="mt-1 text-sm text-slate-500">Total {{ $schedules->count() }} jadwal minggu ini, {{ $totalStudents }} siswa terhubung ke program terjadwal.</p>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Program</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Jam</th>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">Ruang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-950">{{ $schedule->program->name }}</p>
                                @if($schedule->class_type)
                                    <p class="mt-1 inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $schedule->class_type }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $schedule->class_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-bold text-indigo-700">{{ $schedule->students->count() }} siswa</p>
                                <p class="mt-1 max-w-xs truncate text-xs text-slate-500">{{ $schedule->students->pluck('name')->join(', ') ?: 'Belum ada siswa' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $schedule->room ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">Belum ada jadwal program pada minggu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
