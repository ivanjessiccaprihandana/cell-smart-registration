@extends('layouts.app')

@section('content')
<style>
    html { scroll-behavior: smooth; }
    main { display: flex; flex-direction: column; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .glass-card { background: rgba(255,255,255,0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(229,231,235,0.5); }
    section[id] { scroll-margin-top: 5rem; }
    #home { order: 1; }
    #stats { order: 2; }
    #programs { order: 3; }
    #pricing { order: 4; }
    #learning-gallery { order: 5; }
    #tutors { order: 6; }
    #testimonials { order: 7; }
    #contact { order: 8; }
    main > footer { order: 9; }
    .section-reveal { opacity: 0; transform: translateY(24px); transition: opacity 700ms ease, transform 700ms ease; }
    .section-reveal.is-visible { opacity: 1; transform: none; }
    .program-card { transition: background-color 220ms ease, border-color 220ms ease, color 220ms ease, box-shadow 220ms ease, transform 220ms ease; }
    .program-card:not(.is-active) { background: #fff; border-color: #e2e8f0; color: #0f172a; box-shadow: 0 1px 2px rgba(15,23,42,0.04); transform: translateY(0); }
    .program-card:not(.is-active) .program-title,
    .program-card:not(.is-active) .program-heading { color: #0f172a; }
    .program-card:not(.is-active) .program-muted { color: #475569; }
    .program-card:not(.is-active) .program-check { color: #4f46e5; }
    .program-card:not(.is-active) .program-button { background: #fff; border-color: #4f46e5; color: #4f46e5; }
    .program-card:not(.is-active) .program-button:hover { background: #eef2ff; }
    .program-card.is-active { background: #4f46e5; border-color: #4f46e5; color: #fff; box-shadow: 0 24px 45px rgba(79,70,229,0.22); transform: translateY(-4px); }
    .program-card.is-active .program-title,
    .program-card.is-active .program-heading { color: #fff; }
    .program-card.is-active .program-muted { color: #e0e7ff; }
    .program-card.is-active .program-check { color: #fff; }
    .program-card.is-active .program-button { background: #fff; border-color: #fff; color: #4338ca; }
    .program-card.is-active .program-button:hover { background: #eef2ff; }
    .cell-class-card { transition: border-color 220ms ease, box-shadow 220ms ease, transform 220ms ease; }
    .cell-class-card:hover { border-color: #2563eb; box-shadow: 0 22px 45px rgba(37,99,235,0.14); transform: translateY(-3px); }
    .cell-class-card.is-active { background: #2563eb; border-color: #2563eb; color: #fff; box-shadow: 0 26px 50px rgba(37,99,235,0.24); }
    .cell-class-card.is-active .cell-card-muted,
    .cell-class-card.is-active .cell-card-heading-suffix { color: #dbeafe; }
    .cell-class-card.is-active .cell-card-title,
    .cell-class-card.is-active .cell-card-heading { color: #fff; }
    .cell-class-card.is-active .cell-card-check { color: #fff; }
    .cell-class-card.is-active .cell-card-badge { background: rgba(255,255,255,0.14); color: #fff; }
    .cell-class-card.is-active .cell-card-icon { background: rgba(255,255,255,0.16); color: #fff; }
    .sub-program-card { transition: background-color 220ms ease, border-color 220ms ease, color 220ms ease, box-shadow 220ms ease, transform 220ms ease; }
    .sub-program-card:not(.is-active) { background: #fff; border-color: #e2e8f0; color: #0f172a; transform: translateY(0); }
    .sub-program-card:not(.is-active) .sub-program-title { color: #0f172a; }
    .sub-program-card:not(.is-active) .sub-program-muted { color: #475569; }
    .sub-program-card:not(.is-active) .sub-program-check { color: #4f46e5; }
    .sub-program-card:not(.is-active) .sub-program-button { background: #fff; border-color: #4f46e5; color: #4f46e5; }
    .sub-program-card:not(.is-active) .sub-program-visual { background: #eef2ff; }
    .sub-program-card:not(.is-active) .sub-program-icon { color: #6366f1; }
    .sub-program-card.is-active { background: #4f46e5; border-color: #4f46e5; color: #fff; box-shadow: 0 24px 45px rgba(79,70,229,0.22); transform: translateY(-4px); }
    .sub-program-card.is-active .sub-program-title { color: #fff; }
    .sub-program-card.is-active .sub-program-muted { color: #e0e7ff; }
    .sub-program-card.is-active .sub-program-check { color: #fff; }
    .sub-program-card.is-active .sub-program-button { background: #fff; border-color: #fff; color: #4338ca; }
    .sub-program-card.is-active .sub-program-visual { background: #4338ca; }
    .sub-program-card.is-active .sub-program-icon { color: rgba(255,255,255,0.82); }
    .learning-photo-card { transition: border-color 220ms ease, box-shadow 220ms ease, transform 220ms ease; }
    .learning-photo-card:hover,
    .learning-photo-card.is-active { border-color: #2563eb; box-shadow: 0 20px 40px rgba(37,99,235,0.16); transform: translateY(-3px); }
    .modal-panel { animation: modal-pop 180ms ease-out; }
    @keyframes modal-pop {
        from { opacity: 0; transform: translateY(12px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        .section-reveal { opacity: 1; transform: none; transition: none; }
        .program-card { transition: none; }
        .sub-program-card { transition: none; }
    }
</style>

@php
    $landingPrograms = $landingPrograms ?? collect();
    $landingProgramsByName = $landingProgramsByName ?? collect();
    $programNameMatches = fn (string $actualName, string $expectedName) => \Illuminate\Support\Str::lower($actualName) === \Illuminate\Support\Str::lower($expectedName);
    $programByName = function (string $name) use ($landingPrograms, $programNameMatches) {
        return $landingPrograms
            ->first(fn ($program) => $programNameMatches($program->name, $name));
    };
    $programUrl = function (string $name) use ($programByName) {
        $program = $programByName($name);

        return $program
            ? route('programs.index', ['program' => $program->id])
            : route('programs.index');
    };
    $quotaText = function (string $name) use ($programByName) {
        $program = $programByName($name);

        if (!$program) {
            return 'Belum tersedia di admin';
        }

        if (!$program->quota) {
            return 'Kuota tidak dibatasi';
        }

        return (int) $program->remaining_quota . ' kuota tersisa';
    };
    $priceText = function (string $name) use ($programByName) {
        $program = $programByName($name);

        if (!$program) {
            return 'Harga hubungi admin';
        }

        $prices = collect([
                $program->price,
                $program->private_price,
                $program->conversation_price,
            ])
            ->filter(fn ($price) => $price !== null)
            ->values();

        if ($prices->isEmpty()) {
            return 'Harga hubungi admin';
        }

        return 'Mulai Rp ' . number_format($prices->min(), 0, ',', '.');
    };
    $adminWhatsappUrl = 'https://wa.me/6281292538501?text=' . rawurlencode('Halo admin CELL English Course, saya ingin bertanya tentang program.');
@endphp

<main>
    <!-- Hero -->
    <section id="home" class="section-reveal relative pt-20 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 md:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="z-10">
                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full mb-6">CELL FUN N EASY ENGLISH</span>
                <h1 class="text-5xl md:text-6xl font-bold text-slate-900 mb-6 leading-tight">Belajar Bahasa Inggris dan Bimbel Bersama <span class="text-indigo-600">CELL English Course</span></h1>
                <p class="text-lg text-slate-600 mb-8 max-w-xl">LKP CELL English Course menghadirkan kelas Bahasa Inggris dan bimbingan belajar untuk anak TK sampai SMA dengan suasana belajar yang mudah, menyenangkan, dan terarah.</p>

                <div class="flex gap-4 flex-wrap">
                    <a href="{{ route('programs.index') }}" class="inline-flex items-center justify-center rounded-lg px-8 py-3 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow">Daftar Sekarang</a>
                    <a href="{{ route('programs.quota') }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-600 bg-white px-8 py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">
                        <span class="material-symbols-outlined mr-2">event_available</span>Cek Kuota
                    </a>
                    <a href="#programs" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-8 py-3 text-sm font-semibold text-slate-900 hover:border-indigo-600 hover:text-indigo-600">
                        <span class="material-symbols-outlined mr-2">menu_book</span>Lihat Program
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl z-10 border border-white/10">
                    <img src="{{ asset('images/cell-logo-home.svg') }}" alt="CELL Fun n Easy English" class="w-full h-auto object-cover">
                </div>
                <div class="absolute -top-8 -right-8 w-32 h-32 bg-indigo-100 rounded-full blur-3xl opacity-40"></div>
                <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-30"></div>
            </div>
        </div>
    </section>

    <!-- Highlights -->
    <section id="stats" class="section-reveal py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 md:px-8">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                        <span class="material-symbols-outlined">translate</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900">6 Kelas Inggris</div>
                    <div class="mt-2 text-sm leading-6 text-slate-600">Kids, Teens, Adult, TOEIC, dan TOEFL.</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900">BIMBEL TK-SMA</div>
                    <div class="mt-2 text-sm leading-6 text-slate-600">Pendampingan pelajaran sekolah dari usia TK sampai SMA.</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <span class="material-symbols-outlined">record_voice_over</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900">Conversation</div>
                    <div class="mt-2 text-sm leading-6 text-slate-600">Latihan berbicara agar siswa lebih percaya diri memakai Bahasa Inggris.</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                        <span class="material-symbols-outlined">sentiment_satisfied</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900">Fun N Easy</div>
                    <div class="mt-2 text-sm leading-6 text-slate-600">Belajar dibuat ringan, aktif, dan mudah diikuti oleh siswa.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs -->
    <section id="programs" class="section-reveal py-16">
        <div class="max-w-7xl mx-auto px-6 md:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Program Lembaga</h2>
                    <p class="text-slate-600">CELL English Course menyediakan pilihan belajar Bahasa Inggris dan bimbingan belajar sekolah.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-lg transition">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-600"><span class="material-symbols-outlined">translate</span></div>
                        <h3 class="text-xl font-bold">Bahasa Inggris</h3>
                    </div>
                    <p class="text-slate-600 mb-6">Program belajar Bahasa Inggris untuk berbagai usia dan kebutuhan, dari dasar sampai persiapan tes.</p>
                    <ul class="mb-6 grid grid-cols-1 gap-3 text-sm font-medium text-slate-700 sm:grid-cols-2">
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>English for Kids</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>English for Teens</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>English for Adult</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>TOEIC</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>TOEFL</li>
                    </ul>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 flex items-center gap-2"><span class="material-symbols-outlined">record_voice_over</span>Fun N Easy English</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-lg transition">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><span class="material-symbols-outlined">school</span></div>
                        <h3 class="text-xl font-bold">Bimbingan Belajar (Bimbel)</h3>
                    </div>
                    <p class="text-slate-600 mb-6">Bimbingan belajar untuk membantu siswa memahami pelajaran sekolah dengan pendampingan yang terarah.</p>
                    <ul class="mb-6 space-y-3 text-sm font-medium text-slate-700">
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>Untuk usia TK sampai SMA</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>Pendampingan materi sekolah</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>Belajar lebih mudah dan menyenangkan</li>
                    </ul>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 flex items-center gap-2"><span class="material-symbols-outlined">groups</span>TK sampai SMA</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Institution Profile -->
    <section id="tutors" class="section-reveal bg-blue-700 py-16 text-white">
        <div class="mx-auto max-w-6xl px-6 md:px-8">
            <div class="grid gap-10 lg:grid-cols-[0.64fr_0.36fr] lg:items-start">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-100">Profil Lembaga</p>
                    <h2 class="mt-4 max-w-3xl text-4xl font-extrabold leading-tight tracking-tight md:text-5xl">CELL English Course</h2>
                    <p class="mt-5 max-w-2xl text-base leading-8 text-blue-50">
                        Lembaga kursus Bahasa Inggris dan bimbingan belajar sekolah untuk anak, remaja, dan dewasa.
                    </p>
                    <p class="mt-3 max-w-2xl text-base leading-8 text-blue-50">
                        Program belajar disusun melalui pilihan kelas reguler, private Adult, test preparation, BIMBEL, serta jadwal yang lebih terarah.
                    </p>
                </div>

                <div class="space-y-6 border-t border-white/20 pt-6 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-1">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-100">Pimpinan</p>
                        <p class="mt-2 text-lg font-extrabold leading-7">Edy Supriyanto, SS., M.Pd</p>
                    </div>
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-100">Bidang Layanan</p>
                        <p class="mt-2 text-lg font-extrabold leading-7">English Course dan BIMBEL</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 border-y border-white/20">
                <div class="grid divide-y divide-white/20 md:grid-cols-4 md:divide-x md:divide-y-0">
                    <div class="min-h-[8.5rem] py-6 md:pr-7">
                        <p class="text-xl font-extrabold">English Course</p>
                        <p class="mt-3 text-sm leading-6 text-blue-100">Kids, Teens, Adult, Conversation, TOEIC, dan TOEFL.</p>
                    </div>
                    <div class="min-h-[8.5rem] py-6 md:px-7">
                        <p class="text-xl font-extrabold">BIMBEL Sekolah</p>
                        <p class="mt-3 text-sm leading-6 text-blue-100">Pendampingan belajar TK, SD, SMP, dan SMA.</p>
                    </div>
                    <div class="min-h-[8.5rem] py-6 md:px-7">
                        <p class="text-xl font-extrabold">6 Ruang</p>
                        <p class="mt-3 text-sm leading-6 text-blue-100">3 ruang English dan 3 ruang BIMBEL.</p>
                    </div>
                    <div class="min-h-[8.5rem] py-6 md:pl-7">
                        <p class="text-xl font-extrabold">25 Pertemuan</p>
                        <p class="mt-3 text-sm leading-6 text-blue-100">Paket pertemuan untuk private Adult.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Learning Gallery -->
    <section id="learning-gallery" class="section-reveal bg-blue-50/40 py-16">
        <div class="mx-auto max-w-7xl px-6 md:px-8">
            <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="max-w-3xl">
                    <span class="inline-flex rounded-full bg-white px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-blue-700 shadow-sm ring-1 ring-blue-100">
                        Suasana Belajar
                    </span>
                    <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 md:text-4xl">Layanan Belajar di CELL</h2>
                    <p class="mt-3 text-base leading-7 text-slate-600">
                        Gambaran pilihan kelas yang tersedia agar calon siswa lebih mudah memahami layanan sebelum mendaftar.
                    </p>
                </div>
                <a href="{{ route('programs.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-blue-200 bg-white px-5 py-3 text-sm font-extrabold text-blue-700 shadow-sm hover:border-blue-600 hover:bg-blue-50">
                    Lihat Program
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>

            <div class="grid gap-5 lg:grid-cols-[1fr_0.62fr]">
                <div class="grid gap-5 md:grid-cols-2">
                    <button type="button" class="learning-photo-card is-active group overflow-hidden rounded-2xl border border-blue-200 bg-white text-left shadow-sm" data-learning-card="english">
                        <div class="relative h-64 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=900&q=80" alt="Suasana kelas English for Kids dan Teens" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 p-6 text-white">
                                <p class="text-xs font-extrabold uppercase tracking-wide text-blue-100">English Course</p>
                                <h3 class="mt-2 text-2xl font-extrabold">Kids & Teens</h3>
                            </div>
                        </div>
                    </button>

                    <button type="button" class="learning-photo-card group overflow-hidden rounded-2xl border border-blue-100 bg-white text-left shadow-sm" data-learning-card="adult">
                        <div class="relative h-64 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=900&q=80" alt="Suasana kelas private adult" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 p-6 text-white">
                                <p class="text-xs font-extrabold uppercase tracking-wide text-blue-100">Private Adult</p>
                                <h3 class="mt-2 text-2xl font-extrabold">Conversation</h3>
                            </div>
                        </div>
                    </button>

                    <button type="button" class="learning-photo-card group overflow-hidden rounded-2xl border border-blue-100 bg-white text-left shadow-sm" data-learning-card="test">
                        <div class="relative h-64 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=900&q=80" alt="Suasana persiapan TOEIC dan TOEFL" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 p-6 text-white">
                                <p class="text-xs font-extrabold uppercase tracking-wide text-blue-100">Test Preparation</p>
                                <h3 class="mt-2 text-2xl font-extrabold">TOEIC & TOEFL</h3>
                            </div>
                        </div>
                    </button>

                    <button type="button" class="learning-photo-card group overflow-hidden rounded-2xl border border-blue-100 bg-white text-left shadow-sm" data-learning-card="bimbel">
                        <div class="relative h-64 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?auto=format&fit=crop&w=900&q=80" alt="Suasana bimbingan belajar sekolah" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 p-6 text-white">
                                <p class="text-xs font-extrabold uppercase tracking-wide text-blue-100">BIMBEL Sekolah</p>
                                <h3 class="mt-2 text-2xl font-extrabold">TK sampai SMA</h3>
                            </div>
                        </div>
                    </button>
                </div>

                <aside class="rounded-2xl border border-blue-100 bg-white p-7 shadow-sm">
                    <div class="learning-detail" data-learning-detail="english">
                        <span class="material-symbols-outlined text-4xl text-blue-700">translate</span>
                        <h3 class="mt-5 text-2xl font-extrabold text-slate-950">English for Kids & Teens</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Kelas Bahasa Inggris untuk anak dan remaja dengan materi bertahap, latihan speaking, vocabulary, dan grammar sesuai kebutuhan siswa.</p>
                        <ul class="mt-6 space-y-3 text-sm font-semibold text-slate-700">
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Belajar sesuai usia siswa</li>
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Placement test untuk penyesuaian level</li>
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Jadwal tetap dari CELL</li>
                        </ul>
                    </div>

                    <div class="learning-detail hidden" data-learning-detail="adult">
                        <span class="material-symbols-outlined text-4xl text-blue-700">person</span>
                        <h3 class="mt-5 text-2xl font-extrabold text-slate-950">Private Adult</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Kelas private untuk dewasa dengan 1 siswa dalam 1 kelas. Pilihan paketnya meliputi Conversation, TOEIC Preparation, dan TOEFL Preparation.</p>
                        <ul class="mt-6 space-y-3 text-sm font-semibold text-slate-700">
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Paket 25 pertemuan</li>
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Fokus sesuai kebutuhan siswa</li>
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>1 siswa per kelas</li>
                        </ul>
                    </div>

                    <div class="learning-detail hidden" data-learning-detail="test">
                        <span class="material-symbols-outlined text-4xl text-blue-700">quiz</span>
                        <h3 class="mt-5 text-2xl font-extrabold text-slate-950">TOEIC & TOEFL Preparation</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Program persiapan tes untuk membantu siswa memahami pola soal, mengatur strategi pengerjaan, dan membangun kebiasaan latihan yang terarah.</p>
                        <ul class="mt-6 space-y-3 text-sm font-semibold text-slate-700">
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Latihan listening dan reading</li>
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Materi berbasis kebutuhan tes</li>
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Termasuk paket private Adult</li>
                        </ul>
                    </div>

                    <div class="learning-detail hidden" data-learning-detail="bimbel">
                        <span class="material-symbols-outlined text-4xl text-blue-700">menu_book</span>
                        <h3 class="mt-5 text-2xl font-extrabold text-slate-950">BIMBEL Sekolah</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Pendampingan belajar untuk siswa TK sampai SMA agar lebih terbantu memahami pelajaran sekolah dan mengerjakan latihan secara terarah.</p>
                        <ul class="mt-6 space-y-3 text-sm font-semibold text-slate-700">
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Jenjang TK, SD, SMP, dan SMA</li>
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Kelas reguler</li>
                            <li class="flex gap-2"><span class="material-symbols-outlined text-blue-700">check_circle</span>Materi mengikuti kebutuhan sekolah</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Learning Focus -->
    <section id="pricing" class="section-reveal bg-blue-50/50 py-16">
        <div class="max-w-7xl mx-auto px-6 md:px-8">
            <div class="mx-auto mb-10 max-w-3xl text-center">
                <span class="inline-flex rounded-full bg-white px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-blue-700 shadow-sm ring-1 ring-blue-100">
                    Program CELL
                </span>
                <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 md:text-4xl">Kelas CELL</h2>
                <p class="mt-3 text-base leading-7 text-slate-600">Pilih kelas Bahasa Inggris atau BIMBEL dengan jadwal yang sudah disiapkan CELL agar proses belajar lebih jelas dan terarah.</p>
            </div>

            @php
                $homeClassCards = $homeClassCards ?? collect();
            @endphp

            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-6 md:grid-cols-2">
                <?php foreach ($homeClassCards as $homeClass): ?>
                    @php
                        $modalId = $homeClass->modalId();
                        $quotaLabel = $homeClass->quota_label ?: ($homeClass->quota_program_name ? $quotaText($homeClass->quota_program_name) : null);
                    @endphp
                    <div class="cell-class-card {{ $homeClass->is_featured ? 'is-active' : 'border-blue-100 bg-white text-slate-950 shadow-sm' }} flex min-h-[28rem] cursor-pointer flex-col rounded-2xl border p-7" role="button" tabindex="0" aria-pressed="{{ $homeClass->is_featured ? 'true' : 'false' }}" data-modal-target="{{ $modalId }}">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                @if($homeClass->badge)
                                    <div class="cell-card-badge mb-4 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-extrabold text-blue-700">{{ $homeClass->badge }}</div>
                                @endif
                                <h3 class="cell-card-title text-xl font-extrabold text-slate-950">{{ $homeClass->title }}</h3>
                                <p class="cell-card-muted mt-3 text-sm leading-6 text-slate-600">{{ $homeClass->description }}</p>
                            </div>
                            <div class="cell-card-icon flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                                <span class="material-symbols-outlined text-[30px]">{{ str_contains(strtolower($homeClass->title), 'bimbel') ? 'menu_book' : 'school' }}</span>
                            </div>
                        </div>

                        <div class="mt-9">
                            <div class="cell-card-heading text-4xl font-extrabold tracking-tight text-slate-950">
                                {{ $homeClass->heading }}
                                <span class="cell-card-heading-suffix text-base font-bold text-slate-500">{{ $homeClass->heading_suffix }}</span>
                            </div>
                            @if($quotaLabel)
                                <p class="cell-card-badge mt-5 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-extrabold text-blue-700">{{ $quotaLabel }}</p>
                            @endif
                        </div>

                        <ul class="cell-card-muted mt-8 flex-1 space-y-4 text-sm font-semibold text-slate-600">
                            @foreach(($homeClass->features ?? []) as $feature)
                                <li class="flex gap-3">
                                    <span class="cell-card-check material-symbols-outlined text-[22px] text-blue-600">check_circle</span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <a href="#{{ $modalId }}" class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                            Pilih Program
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php foreach ($homeClassCards as $homeClass): ?>
                @php
                    $modalId = $homeClass->modalId();
                @endphp
                <div id="{{ $modalId }}" class="program-modal fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/65 px-4 py-6 backdrop-blur-lg" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}Title">
                    <div class="modal-panel max-h-[88vh] w-full max-w-6xl overflow-y-auto rounded-3xl border border-white/70 bg-white p-6 shadow-2xl md:p-8">
                        <div class="mb-8 flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                            <div>
                                <div class="mb-4 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
                                    @foreach(($homeClass->modal_breadcrumbs ?? []) as $breadcrumb)
                                        @if(!$loop->first)
                                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                        @endif
                                        <span class="{{ $loop->last ? 'text-indigo-600' : '' }}">{{ $breadcrumb }}</span>
                                    @endforeach
                                </div>
                                <h3 id="{{ $modalId }}Title" class="text-3xl font-bold text-slate-900">{{ str_replace('Sub-Program', 'Program', $homeClass->modal_title) }}</h3>
                                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">{{ $homeClass->modal_description }}</p>
                            </div>

                            <button type="button" data-close-modal="{{ $modalId }}" aria-label="Tutup pilihan {{ $homeClass->title }}" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-6 {{ $homeClass->grid_columns }}">
                            @foreach(($homeClass->sub_programs ?? []) as $subProgram)
                                @php
                                    $subProgramName = $subProgram['program_name'] ?? $subProgram['title'];
                                    $isFeaturedSubProgram = (bool) ($subProgram['is_featured'] ?? false);
                                @endphp
                                <article class="sub-program-card {{ $isFeaturedSubProgram ? 'is-active border-indigo-600 bg-indigo-600 text-white shadow-xl shadow-indigo-600/20' : 'border-slate-200 bg-white shadow-sm' }} cursor-pointer overflow-hidden rounded-2xl border" role="button" tabindex="0" aria-pressed="{{ $isFeaturedSubProgram ? 'true' : 'false' }}">
                                    <div class="sub-program-visual relative flex h-36 items-center justify-center {{ $isFeaturedSubProgram ? 'bg-indigo-700' : 'bg-indigo-50' }}">
                                        @if(!empty($subProgram['badge']))
                                            <span class="absolute left-4 top-4 rounded-full {{ $isFeaturedSubProgram ? 'bg-white text-indigo-700' : 'bg-indigo-600 text-white' }} px-3 py-1 text-xs font-bold">{{ $subProgram['badge'] }}</span>
                                        @endif
                                        @if($isFeaturedSubProgram)
                                            <span class="absolute right-4 top-4 rounded-full bg-amber-500 px-3 py-1 text-xs font-bold text-white">Populer</span>
                                        @endif
                                        <span class="sub-program-icon material-symbols-outlined text-7xl {{ $isFeaturedSubProgram ? 'text-white/80' : 'text-indigo-500' }}">{{ $subProgram['icon'] ?? 'school' }}</span>
                                    </div>
                                    <div class="p-6">
                                        <h4 class="sub-program-title text-xl font-bold text-slate-900">{{ $subProgram['title'] }}</h4>
                                        <p class="sub-program-muted mt-2 text-sm leading-6 text-slate-600">{{ $subProgram['description'] ?? '' }}</p>
                                        @if(!empty($subProgram['features']))
                                            <ul class="sub-program-muted mt-5 space-y-3 text-sm font-medium text-slate-700">
                                                @foreach($subProgram['features'] as $feature)
                                                    <li class="flex gap-2"><span class="sub-program-check material-symbols-outlined text-indigo-600">check_circle</span>{{ $feature }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        <p class="sub-program-muted mt-5 text-xs font-bold text-slate-500">{{ $quotaText($subProgramName) }}</p>
                                        <p class="mt-2 inline-flex rounded-lg {{ $isFeaturedSubProgram ? 'bg-white/15 text-white' : 'bg-indigo-50 text-indigo-700' }} px-3 py-2 text-sm font-extrabold">{{ $priceText($subProgramName) }}</p>
                                        <a href="{{ $programUrl($subProgramName) }}" class="sub-program-button mt-8 inline-flex w-full items-center justify-center rounded-lg border border-indigo-600 px-5 py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">Pilih Program</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Learning Atmosphere -->
    <section id="testimonials" class="section-reveal py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 md:px-8 text-center mb-10">
            <h2 class="text-3xl font-bold text-slate-900 mb-2">Suasana Belajar</h2>
            <p class="text-slate-600">Kegiatan belajar diarahkan agar siswa lebih percaya diri, aktif bertanya, dan nyaman berlatih.</p>
        </div>
        <div class="mx-auto max-w-5xl px-6 md:px-8">
            <div class="relative">
                <div class="overflow-hidden rounded-3xl">
                    <div id="testimonialTrack" class="flex transition-transform duration-500 ease-out">
                        <article class="w-full flex-none px-1">
                            <div class="glass-card relative rounded-3xl p-8 text-center shadow md:p-10">
                                <span class="material-symbols-outlined absolute right-8 top-6 select-none text-6xl text-indigo-100" style="font-variation-settings: 'FILL' 1;">format_quote</span>
                                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full border-2 border-indigo-600 bg-indigo-50 text-xl font-bold text-indigo-700">EC</div>
                                <p class="mx-auto mb-6 max-w-3xl text-lg italic leading-8 text-slate-600">"Belajar Bahasa Inggris jadi lebih menyenangkan karena materi dijelaskan pelan-pelan dan banyak latihan percakapan."</p>
                                <h4 class="font-bold text-slate-900">Siswa CELL English Course</h4>
                                <p class="text-sm text-slate-600">Kelas Conversation</p>
                            </div>
                        </article>

                        <article class="w-full flex-none px-1">
                            <div class="glass-card relative rounded-3xl p-8 text-center shadow md:p-10">
                                <span class="material-symbols-outlined absolute right-8 top-6 select-none text-6xl text-emerald-100" style="font-variation-settings: 'FILL' 1;">format_quote</span>
                                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full border-2 border-emerald-600 bg-emerald-50 text-xl font-bold text-emerald-700">BK</div>
                                <p class="mx-auto mb-6 max-w-3xl text-lg italic leading-8 text-slate-600">"Anak saya lebih semangat belajar karena suasana kelasnya santai, gurunya sabar, dan tugas sekolah lebih mudah dipahami."</p>
                                <h4 class="font-bold text-slate-900">Orang Tua Siswa</h4>
                                <p class="text-sm text-slate-600">Program BIMBEL TK-SMA</p>
                            </div>
                        </article>

                        <article class="w-full flex-none px-1">
                            <div class="glass-card relative rounded-3xl p-8 text-center shadow md:p-10">
                                <span class="material-symbols-outlined absolute right-8 top-6 select-none text-6xl text-amber-100" style="font-variation-settings: 'FILL' 1;">format_quote</span>
                                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full border-2 border-amber-600 bg-amber-50 text-xl font-bold text-amber-700">TF</div>
                                <p class="mx-auto mb-6 max-w-3xl text-lg italic leading-8 text-slate-600">"Latihan TOEFL-nya terarah. Saya jadi tahu bagian mana yang harus diperbaiki dan lebih percaya diri saat mengerjakan soal."</p>
                                <h4 class="font-bold text-slate-900">Peserta Test Preparation</h4>
                                <p class="text-sm text-slate-600">Program TOEFL</p>
                            </div>
                        </article>
                    </div>
                </div>

                <button type="button" id="testimonialPrev" aria-label="Testimoni sebelumnya" class="absolute left-0 top-1/2 hidden h-11 w-11 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-lg transition hover:border-indigo-600 hover:text-indigo-600 md:flex">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <button type="button" id="testimonialNext" aria-label="Testimoni berikutnya" class="absolute right-0 top-1/2 hidden h-11 w-11 translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-lg transition hover:border-indigo-600 hover:text-indigo-600 md:flex">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>

            <div class="mt-6 flex justify-center gap-3">
                <button type="button" aria-label="Lihat testimoni 1" class="testimonial-dot h-3 w-3 rounded-full bg-indigo-600 transition" data-testimonial-index="0"></button>
                <button type="button" aria-label="Lihat testimoni 2" class="testimonial-dot h-3 w-3 rounded-full bg-slate-300 transition" data-testimonial-index="1"></button>
                <button type="button" aria-label="Lihat testimoni 3" class="testimonial-dot h-3 w-3 rounded-full bg-slate-300 transition" data-testimonial-index="2"></button>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section id="contact" class="section-reveal py-16">
        <div class="max-w-7xl mx-auto px-6 md:px-8">
            <div class="bg-indigo-600 rounded-3xl p-12 text-center text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold mb-4">Siap Bergabung di CELL English Course?</h2>
                    <p class="text-lg text-indigo-100 mb-6 max-w-2xl mx-auto">Pilih program Bahasa Inggris, TOEIC/TOEFL, atau BIMBEL sesuai kebutuhan belajar siswa.</p>
                    <div class="flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ route('programs.quota') }}" class="inline-flex items-center justify-center px-8 py-3 bg-white text-indigo-700 rounded-lg font-semibold">Cek Kuota Sekarang</a>
                        <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-500 px-8 py-3 font-semibold text-white hover:bg-emerald-600">
                            <svg class="h-5 w-5" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
                                <path d="M16.01 3.2A12.71 12.71 0 0 0 5.16 22.54L3.6 28.8l6.41-1.5A12.76 12.76 0 1 0 16.01 3.2Zm0 23.25c-2.05 0-3.95-.59-5.57-1.61l-.4-.25-3.8.89.92-3.7-.26-.42a10.48 10.48 0 1 1 9.11 5.09Zm5.78-7.84c-.32-.16-1.89-.93-2.18-1.04-.29-.11-.5-.16-.72.16-.21.32-.82 1.04-1.01 1.25-.18.21-.37.24-.69.08-.32-.16-1.35-.5-2.57-1.58-.95-.85-1.59-1.9-1.78-2.22-.18-.32-.02-.49.14-.65.14-.14.32-.37.48-.56.16-.18.21-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.73-.98-2.37-.26-.62-.52-.54-.72-.55h-.61c-.21 0-.56.08-.85.4-.29.32-1.12 1.09-1.12 2.66 0 1.57 1.15 3.09 1.31 3.3.16.21 2.26 3.45 5.48 4.84.77.33 1.37.53 1.84.68.77.24 1.47.21 2.02.13.62-.09 1.89-.77 2.16-1.52.27-.75.27-1.39.19-1.52-.08-.13-.29-.21-.61-.37Z" />
                            </svg>
                            Chat WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 md:px-8 py-16">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-4">
                <div>
                    <h3 class="text-2xl font-bold text-indigo-600 mb-6">CELL English Course</h3>
                    <p class="text-base leading-7 text-slate-700 max-w-xs">CELL FUN N EASY ENGLISH untuk program Bahasa Inggris dan BIMBEL dari TK sampai SMA.</p>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-5">Layanan</h4>
                    <ul class="space-y-3 text-sm font-medium text-slate-700">
                        <li><a href="#programs" class="hover:text-indigo-600 transition-colors">English for Kids</a></li>
                        <li><a href="#programs" class="hover:text-indigo-600 transition-colors">TOEIC & TOEFL</a></li>
                        <li><a href="#programs" class="hover:text-indigo-600 transition-colors">BIMBEL TK-SMA</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-5">Perusahaan</h4>
                    <ul class="space-y-3 text-sm font-medium text-slate-700">
                        <li><a href="#tutors" class="hover:text-indigo-600 transition-colors">Profile Lembaga</a></li>
                        <li><a href="#contact" class="hover:text-indigo-600 transition-colors">Hubungi Kami</a></li>
                        <li><a href="#programs" class="hover:text-indigo-600 transition-colors">Program</a></li>
                        <li><a href="#pricing" class="hover:text-indigo-600 transition-colors">Kelas</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-5">Kontak</h4>
                    <ul class="space-y-3 text-sm font-medium text-slate-700">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">mail</span>
                            cellenglishcourse@email.com
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">call</span>
                            <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener" class="hover:text-emerald-600">0812 9253 8501</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">location_on</span>
                            Indonesia
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-6 md:px-8 py-8 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <p class="text-xs font-semibold text-slate-700">&copy; 2026 CELL English Course. CELL FUN N EASY ENGLISH.</p>

                <div class="flex items-center gap-6 text-slate-700">
                    <a href="#" aria-label="Instagram" class="hover:text-indigo-600 transition-colors">
                        <span class="material-symbols-outlined">photo_camera</span>
                    </a>
                    <a href="#" aria-label="Website" class="hover:text-indigo-600 transition-colors">
                        <span class="material-symbols-outlined">public</span>
                    </a>
                    <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener" aria-label="WhatsApp" class="hover:text-emerald-600 transition-colors">
                        <svg class="h-6 w-6" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
                            <path d="M16.01 3.2A12.71 12.71 0 0 0 5.16 22.54L3.6 28.8l6.41-1.5A12.76 12.76 0 1 0 16.01 3.2Zm0 23.25c-2.05 0-3.95-.59-5.57-1.61l-.4-.25-3.8.89.92-3.7-.26-.42a10.48 10.48 0 1 1 9.11 5.09Zm5.78-7.84c-.32-.16-1.89-.93-2.18-1.04-.29-.11-.5-.16-.72.16-.21.32-.82 1.04-1.01 1.25-.18.21-.37.24-.69.08-.32-.16-1.35-.5-2.57-1.58-.95-.85-1.59-1.9-1.78-2.22-.18-.32-.02-.49.14-.65.14-.14.32-.37.48-.56.16-.18.21-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.73-.98-2.37-.26-.62-.52-.54-.72-.55h-.61c-.21 0-.56.08-.85.4-.29.32-1.12 1.09-1.12 2.66 0 1.57 1.15 3.09 1.31 3.3.16.21 2.26 3.45 5.48 4.84.77.33 1.37.53 1.84.68.77.24 1.47.21 2.02.13.62-.09 1.89-.77 2.16-1.52.27-.75.27-1.39.19-1.52-.08-.13-.29-.21-.61-.37Z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sections = document.querySelectorAll('.section-reveal');

        const revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        sections.forEach(function (section) {
            revealObserver.observe(section);
        });

        document.querySelectorAll('a[href^="#"]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                const target = document.querySelector(link.getAttribute('href'));

                if (!target) {
                    return;
                }

                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });

                const menu = link.closest('details');
                if (menu) {
                    menu.removeAttribute('open');
                }
            });
        });

        const profileTabs = document.querySelectorAll('.profile-tab');
        const profilePanels = document.querySelectorAll('.profile-panel');

        profileTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                const selectedTab = tab.dataset.profileTab;

                profileTabs.forEach(function (item) {
                    const isActive = item.dataset.profileTab === selectedTab;
                    item.classList.toggle('bg-white', isActive);
                    item.classList.toggle('shadow-sm', isActive);
                    item.classList.toggle('text-blue-700', isActive);
                    item.classList.toggle('text-slate-600', !isActive);
                });

                profilePanels.forEach(function (panel) {
                    panel.classList.toggle('hidden', panel.dataset.profilePanel !== selectedTab);
                });
            });
        });

        const learningCards = document.querySelectorAll('.learning-photo-card');
        const learningDetails = document.querySelectorAll('.learning-detail');

        learningCards.forEach(function (card) {
            card.addEventListener('click', function () {
                const selectedCard = card.dataset.learningCard;

                learningCards.forEach(function (item) {
                    item.classList.toggle('is-active', item.dataset.learningCard === selectedCard);
                });

                learningDetails.forEach(function (detail) {
                    detail.classList.toggle('hidden', detail.dataset.learningDetail !== selectedCard);
                });
            });
        });

        const testimonialTrack = document.getElementById('testimonialTrack');
        const testimonialDots = document.querySelectorAll('.testimonial-dot');
        const testimonialPrev = document.getElementById('testimonialPrev');
        const testimonialNext = document.getElementById('testimonialNext');
        let testimonialIndex = 0;

        function showTestimonial(index) {
            if (!testimonialTrack || testimonialDots.length === 0) {
                return;
            }

            testimonialIndex = (index + testimonialDots.length) % testimonialDots.length;
            testimonialTrack.style.transform = `translateX(-${testimonialIndex * 100}%)`;

            testimonialDots.forEach(function (dot, dotIndex) {
                dot.classList.toggle('bg-indigo-600', dotIndex === testimonialIndex);
                dot.classList.toggle('bg-slate-300', dotIndex !== testimonialIndex);
            });
        }

        if (testimonialPrev && testimonialNext) {
            testimonialPrev.addEventListener('click', function () {
                showTestimonial(testimonialIndex - 1);
            });

            testimonialNext.addEventListener('click', function () {
                showTestimonial(testimonialIndex + 1);
            });
        }

        testimonialDots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                showTestimonial(Number(dot.dataset.testimonialIndex));
            });
        });

        const programCards = document.querySelectorAll('.program-card, .cell-class-card');
        const programModals = document.querySelectorAll('.program-modal');

        function openProgramModal(modalId) {
            const modal = document.getElementById(modalId);

            if (!modal) {
                return;
            }

            programModals.forEach(function (item) {
                item.classList.add('hidden');
                item.classList.remove('flex');
            });

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeProgramModals() {
            programModals.forEach(function (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

            document.body.classList.remove('overflow-hidden');
        }

        function activateProgramCard(selectedCard) {
            programCards.forEach(function (card) {
                const isSelected = card === selectedCard;
                card.classList.toggle('is-active', isSelected);
                card.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            });

            openProgramModal(selectedCard.dataset.modalTarget);
        }

        programCards.forEach(function (card) {
            card.addEventListener('click', function () {
                activateProgramCard(card);
            });

            card.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }

                event.preventDefault();
                activateProgramCard(card);
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach(function (button) {
            button.addEventListener('click', closeProgramModals);
        });

        programModals.forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeProgramModals();
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeProgramModals();
            }
        });

        const subProgramCards = document.querySelectorAll('.sub-program-card');

        function activateSubProgramCard(selectedCard) {
            subProgramCards.forEach(function (card) {
                const isSelected = card === selectedCard;
                card.classList.toggle('is-active', isSelected);
                card.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            });
        }

        subProgramCards.forEach(function (card) {
            card.addEventListener('click', function () {
                activateSubProgramCard(card);
            });

            card.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }

                event.preventDefault();
                activateSubProgramCard(card);
            });
        });
    });
</script>

@endsection
