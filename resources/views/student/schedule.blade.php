@extends('layouts.app')

@section('content')
@php
    $priceText = $program ? $program->formattedPriceForClassType($auth->class_type) : '-';
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
                        <p class="mt-2 text-xl font-extrabold text-indigo-700">Menunggu Konsultasi</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50 px-6 py-5 md:px-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-950">Konsultasikan waktu belajar</p>
                            <p class="mt-1 text-sm text-slate-500">Sampaikan hari dan jam yang tersedia agar admin dapat menyesuaikan kelas Anda.</p>
                        </div>
                        <a href="{{ route('home') }}#contact" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
                            <span class="material-symbols-outlined text-[18px]">chat</span>
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
                    <h2 class="mt-4 text-lg font-extrabold text-amber-950">Jadwal belum final</h2>
                    <p class="mt-2 text-sm font-semibold leading-6 text-amber-800">
                        Pembayaran sudah disetujui dan placement test selesai. Jadwal kelas akan ditentukan setelah konsultasi dengan pihak les.
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
