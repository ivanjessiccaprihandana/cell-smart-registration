@extends('layouts.app')

@section('content')
@php
    $priceText = $program ? $program->formattedPriceForClassType($auth->class_type) : '-';
    $assignedSchedules = $assignedSchedules ?? collect();
    $adminWhatsappUrl = 'https://wa.me/6281292538501?text=' . rawurlencode('Halo admin CELL English Course, saya ingin konsultasi jadwal kelas.');
@endphp

<main class="min-h-[calc(100vh-4rem)] bg-slate-50 px-6 py-10 md:px-8">
    <div class="mx-auto max-w-6xl space-y-8">
        <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-indigo-600">Konsultasi Jadwal</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Atur Jadwal Belajar Anda</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    Jadwal kelas akan disesuaikan bersama pihak CELL English Course berdasarkan level, program, dan ketersediaan waktu Anda.
                </p>
            </div>
            <a href="{{ route('student.status') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 hover:border-indigo-500 hover:text-indigo-600">
                <span class="material-symbols-outlined text-[18px]">assignment_ind</span>
                Status Saya
            </a>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <article class="overflow-hidden rounded-3xl border border-indigo-100 bg-white shadow-xl shadow-slate-900/5">
                <div class="bg-indigo-600 px-6 py-6 text-white md:px-8">
                    <p class="text-sm font-bold uppercase tracking-wide text-indigo-100">CELL English Course</p>
                    <h2 class="mt-2 text-3xl font-extrabold">Konsultasi Jadwal Kelas</h2>
                    <p class="mt-2 text-sm font-semibold text-indigo-100">Placement test selesai, jadwal akan dipilih setelah konsultasi.</p>
                </div>

                <div class="grid gap-4 p-6 md:grid-cols-2 md:p-8">
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Level Hasil Test</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-950">{{ $latestPlacementAttempt->level }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Skor Test</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-950">{{ $latestPlacementAttempt->correct_answers }}/{{ $latestPlacementAttempt->total_questions }} ({{ $latestPlacementAttempt->score_percentage }}%)</p>
                    </div>
                    <div class="rounded-2xl bg-indigo-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Program Dipilih</p>
                        <p class="mt-2 text-xl font-extrabold text-indigo-700">{{ $program->name ?? '-' }}</p>
                        @if($auth->class_type)
                            <p class="mt-2 text-sm font-bold text-indigo-500">{{ $auth->class_type }}</p>
                        @endif
                    </div>
                    <div class="rounded-2xl bg-indigo-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Status Jadwal</p>
                        <p class="mt-2 text-xl font-extrabold text-indigo-700">{{ $assignedSchedules->isNotEmpty() ? 'Jadwal Ditentukan' : 'Menunggu Konsultasi' }}</p>
                    </div>
                </div>

                @if($assignedSchedules->isNotEmpty())
                    <div class="border-t border-slate-100 px-6 py-6 md:px-8">
                        <h3 class="text-lg font-extrabold text-slate-950">Jadwal Anda</h3>
                        <div class="mt-4 space-y-3">
                            @foreach($assignedSchedules as $schedule)
                                <article class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                        <div>
                                            <p class="text-sm font-bold uppercase tracking-wide text-indigo-500">{{ $schedule->class_date->format('d M Y') }}</p>
                                            <h4 class="mt-1 text-xl font-extrabold text-slate-950">{{ $schedule->program->name }}</h4>
                                        @if($schedule->class_type)
                                            <p class="mt-2 inline-flex rounded-full bg-white px-3 py-1 text-xs font-bold text-indigo-700">{{ $schedule->class_type }}</p>
                                        @endif
                                        </div>
                                        <div class="rounded-xl bg-white px-4 py-3 text-sm font-extrabold text-indigo-700">
                                            {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                        </div>
                                    </div>
                                    <div class="mt-4 grid gap-3 text-sm font-semibold text-slate-600 md:grid-cols-2">
                                        <div class="flex items-start gap-2">
                                            <span class="material-symbols-outlined text-[20px] text-indigo-600">meeting_room</span>
                                            <span>{{ $schedule->room ?: 'Ruang belum diisi' }}</span>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <span class="material-symbols-outlined text-[20px] text-indigo-600">person_book</span>
                                            <span>{{ $schedule->tutor?->name ?: 'Tutor belum ditentukan' }}</span>
                                        </div>
                                        @if($schedule->notes)
                                            <div class="flex items-start gap-2">
                                                <span class="material-symbols-outlined text-[20px] text-indigo-600">notes</span>
                                                <span>{{ $schedule->notes }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="border-t border-slate-100 bg-slate-50 px-6 py-5 md:px-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-950">{{ $assignedSchedules->isNotEmpty() ? 'Jadwal sudah ditentukan' : 'Konsultasikan waktu belajar' }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $assignedSchedules->isNotEmpty() ? 'Hubungi admin jika ada perubahan jadwal yang perlu dikonsultasikan.' : 'Sampaikan hari dan jam yang tersedia agar admin dapat menyesuaikan kelas Anda.' }}</p>
                        </div>
                        <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 text-sm font-bold text-white hover:bg-emerald-700">
                            <svg class="h-5 w-5" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
                                <path d="M16.01 3.2A12.71 12.71 0 0 0 5.16 22.54L3.6 28.8l6.41-1.5A12.76 12.76 0 1 0 16.01 3.2Zm0 23.25c-2.05 0-3.95-.59-5.57-1.61l-.4-.25-3.8.89.92-3.7-.26-.42a10.48 10.48 0 1 1 9.11 5.09Zm5.78-7.84c-.32-.16-1.89-.93-2.18-1.04-.29-.11-.5-.16-.72.16-.21.32-.82 1.04-1.01 1.25-.18.21-.37.24-.69.08-.32-.16-1.35-.5-2.57-1.58-.95-.85-1.59-1.9-1.78-2.22-.18-.32-.02-.49.14-.65.14-.14.32-.37.48-.56.16-.18.21-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.73-.98-2.37-.26-.62-.52-.54-.72-.55h-.61c-.21 0-.56.08-.85.4-.29.32-1.12 1.09-1.12 2.66 0 1.57 1.15 3.09 1.31 3.3.16.21 2.26 3.45 5.48 4.84.77.33 1.37.53 1.84.68.77.24 1.47.21 2.02.13.62-.09 1.89-.77 2.16-1.52.27-.75.27-1.39.19-1.52-.08-.13-.29-.21-.61-.37Z" />
                            </svg>
                            Hubungi Admin
                        </a>
                    </div>
                </div>
            </article>

            <aside class="space-y-6">
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-extrabold text-slate-950">Ringkasan Siswa</h2>
                    <div class="mt-5 divide-y divide-slate-100 rounded-xl border border-slate-100">
                        <div class="grid gap-2 p-4 sm:grid-cols-[0.8fr_1.2fr]">
                            <p class="text-sm font-semibold text-slate-500">Nama</p>
                            <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $auth->name }}</p>
                        </div>
                        <div class="grid gap-2 p-4 sm:grid-cols-[0.8fr_1.2fr]">
                            <p class="text-sm font-semibold text-slate-500">Program</p>
                            <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $program->name ?? '-' }}</p>
                        </div>
                        @if($auth->class_type)
                            <div class="grid gap-2 p-4 sm:grid-cols-[0.8fr_1.2fr]">
                                <p class="text-sm font-semibold text-slate-500">Jenis Kelas</p>
                                <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $auth->class_type }}</p>
                            </div>
                        @endif
                        <div class="grid gap-2 p-4 sm:grid-cols-[0.8fr_1.2fr]">
                            <p class="text-sm font-semibold text-slate-500">Biaya</p>
                            <p class="text-sm font-extrabold text-indigo-700 sm:text-right">{{ $priceText }}</p>
                        </div>
                        <div class="grid gap-2 p-4 sm:grid-cols-[0.8fr_1.2fr]">
                            <p class="text-sm font-semibold text-slate-500">Skor Test</p>
                            <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $latestPlacementAttempt->correct_answers }}/{{ $latestPlacementAttempt->total_questions }} ({{ $latestPlacementAttempt->score_percentage }}%)</p>
                        </div>
                        <div class="grid gap-2 p-4 sm:grid-cols-[0.8fr_1.2fr]">
                            <p class="text-sm font-semibold text-slate-500">Rekomendasi</p>
                            <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $latestPlacementAttempt->recommended_program }}</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-amber-100 bg-amber-50 p-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-amber-700">
                        <span class="material-symbols-outlined">event_available</span>
                    </div>
                    <h2 class="mt-4 text-lg font-extrabold text-amber-950">{{ $assignedSchedules->isNotEmpty() ? 'Jadwal sudah tersedia' : 'Jadwal belum final' }}</h2>
                    <p class="mt-2 text-sm font-semibold leading-6 text-amber-800">
                        {{ $assignedSchedules->isNotEmpty() ? 'Silakan ikuti jadwal yang sudah ditentukan admin setelah proses konsultasi.' : 'Pembayaran sudah disetujui dan placement test selesai. Jadwal kelas akan ditentukan setelah konsultasi dengan pihak les.' }}
                    </p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-extrabold text-slate-950">Yang Perlu Dikonsultasikan</h2>
                    <div class="mt-4 space-y-3 text-sm font-semibold text-slate-600">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[20px] text-indigo-600">calendar_month</span>
                            Hari belajar yang paling cocok untuk siswa.
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[20px] text-indigo-600">schedule</span>
                            Jam belajar yang tersedia setelah sekolah atau aktivitas lain.
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[20px] text-indigo-600">school</span>
                            Penempatan kelas sesuai level hasil placement test.
                        </div>
                    </div>
                </article>
            </aside>
        </section>
    </div>
</main>
@endsection
