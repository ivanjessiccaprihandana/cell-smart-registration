@extends('layouts.app')

@php
    $questions = $questions ?? collect();
    $latestAttempt = $latestAttempt ?? null;
    $showResult = (bool) $latestAttempt;
    $placementStartedAt = $placementStartedAt ?? now()->timestamp;
    $remainingSeconds = $remainingSeconds ?? 30 * 60;
@endphp

@section('content')
<style>
    .answer-option.is-selected {
        border-color: #4f46e5 !important;
        background: #eef2ff !important;
        box-shadow: 0 12px 30px rgba(79, 70, 229, 0.12);
    }

    .answer-option.is-selected .answer-letter {
        border-color: #4f46e5 !important;
        background: #4f46e5 !important;
        color: #ffffff !important;
    }

    .answer-option.is-selected .answer-text {
        color: #312e81;
        font-weight: 800;
    }

    .answer-option.is-selected .answer-state {
        color: #4f46e5;
        font-variation-settings: 'FILL' 1, 'wght' 700, 'GRAD' 0, 'opsz' 24;
    }
</style>

<main class="min-h-[calc(100vh-4rem)] bg-slate-50 px-6 py-10 md:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-indigo-600">Placement Test</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">English Proficiency Test</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Jawab soal berikut untuk menentukan level belajar. Hasilnya akan tersimpan dan bisa dilihat admin.</p>
            </div>
            <div class="flex items-center gap-3">
                @unless($showResult)
                    <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-700">
                        <span class="material-symbols-outlined text-[18px]">timer</span>
                        <span id="timer">60:00</span>
                    </span>
                @endunless
                <a href="{{ route('home') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 px-4 text-sm font-bold text-slate-700 hover:border-indigo-500 hover:text-indigo-600">Beranda</a>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if($showResult)
            <section class="mx-auto max-w-4xl rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-xl shadow-slate-900/5">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                    <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                </div>

                <p class="mt-6 text-sm font-extrabold uppercase tracking-wide text-indigo-600">Hasil Placement Test</p>
                <h2 class="mt-2 text-4xl font-extrabold text-slate-950">{{ $latestAttempt->level }}</h2>
                <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                    Placement test Anda sudah selesai dan hasilnya tersimpan. Admin dapat melihat detail skor dan jawaban Anda.
                </p>

                <div class="mx-auto mt-8 grid max-w-3xl gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Skor</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $latestAttempt->correct_answers }}/{{ $latestAttempt->total_questions }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Persentase</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $latestAttempt->score_percentage }}%</p>
                    </div>
                    <div class="rounded-2xl bg-indigo-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Level</p>
                        <p class="mt-2 text-lg font-extrabold text-indigo-700">{{ $latestAttempt->level }}</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ route('student.status') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
                        <span class="material-symbols-outlined text-[18px]">assignment_ind</span>
                        Kembali ke Status Saya
                    </a>
                </div>

                <p class="mx-auto mt-5 max-w-2xl text-xs font-semibold leading-5 text-slate-500">
                    Jika terjadi kendala teknis dan perlu mengulang test, silakan hubungi admin agar akses test dibuka kembali.
                </p>
            </section>
        @elseif($questions->isEmpty())
            <section class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                    <span class="material-symbols-outlined">quiz</span>
                </div>
                <h2 class="mt-5 text-2xl font-bold text-slate-950">Belum Ada Soal Aktif</h2>
                <p class="mt-2 text-sm text-slate-500">Silakan hubungi admin untuk mengaktifkan soal placement test.</p>
            </section>
        @else
            <form id="placementForm" method="POST" action="{{ route('placement-test.store') }}" class="grid gap-8 lg:grid-cols-[1fr_280px]">
                @csrf
                <input type="hidden" name="started_at" value="{{ $placementStartedAt }}">

                <section>
                    <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4">
                        <div class="flex gap-3">
                            <span class="material-symbols-outlined mt-0.5 text-[22px] text-amber-700">wifi</span>
                            <div>
                                <p class="text-sm font-extrabold text-amber-800">Sebelum mulai test</p>
                                <p class="mt-1 text-sm font-semibold leading-6 text-amber-800">
                                    Waktu test tetap berjalan meskipun halaman ditutup atau dibuka ulang. Pastikan koneksi internet stabil, baterai perangkat cukup, dan jangan menutup halaman sampai jawaban dikirim. Jika ada kendala, admin dapat membuka ulang akses test.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="mb-4 flex items-end justify-between gap-4">
                            <div>
                                <h2 id="questionCounter" class="text-2xl font-extrabold tracking-tight text-slate-950">Question 1 of {{ $questions->count() }}</h2>
                                <p id="questionSection" class="mt-1 text-sm font-semibold text-slate-500">{{ $questions->first()->section }}</p>
                            </div>
                            <p id="progressText" class="text-sm font-bold text-indigo-600">1% Complete</p>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-200">
                            <div id="progressBar" class="h-full rounded-full bg-indigo-600 transition-all" style="width: 1%"></div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @foreach($questions as $index => $question)
                            <article class="question-card {{ $index === 0 ? '' : 'hidden' }} rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 md:p-8" data-index="{{ $index }}" data-section="{{ $question->section }}">
                                <h3 class="text-xl font-extrabold leading-8 text-slate-950">{{ $question->question_text }}</h3>

                                <div class="mt-8 space-y-4">
                                    @foreach($question->options as $optionIndex => $option)
                                        <label class="answer-option flex w-full cursor-pointer items-center gap-4 rounded-xl border border-slate-200 bg-white px-5 py-4 text-left transition hover:border-indigo-300 hover:bg-indigo-50/50" role="radio" aria-checked="false">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $optionIndex }}" class="sr-only" data-question-index="{{ $index }}">
                                            <span class="answer-letter flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-xs font-extrabold text-indigo-700">{{ chr(65 + $optionIndex) }}</span>
                                            <span class="answer-text flex-1 text-sm font-semibold text-slate-700">{{ $option }}</span>
                                            <span class="answer-state material-symbols-outlined text-[22px] text-slate-300">radio_button_unchecked</span>
                                        </label>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-8 flex items-center justify-between gap-4">
                        <button id="prevButton" type="button" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-600 hover:border-indigo-400 hover:text-indigo-600">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            Sebelumnya
                        </button>
                        <button id="nextButton" type="button" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-8 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
                            Lanjut
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>
                </section>

                <aside class="lg:sticky lg:top-24 lg:self-start">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-900/5">
                        <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-400">Test Overview</h2>
                        <div id="overviewGrid" class="mt-5 grid grid-cols-5 gap-2">
                            @foreach($questions as $index => $question)
                                <button type="button" class="overview-button h-9 rounded-lg border text-xs font-extrabold {{ $index === 0 ? 'bg-indigo-600 text-white' : 'border-slate-200 bg-white text-slate-500' }}" data-index="{{ $index }}">{{ $index + 1 }}</button>
                            @endforeach
                        </div>
                        <div class="mt-6 rounded-xl bg-slate-50 p-4 text-xs font-medium leading-5 text-slate-500">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[18px] text-indigo-600">info</span>
                                <p>Hasil akan tersimpan otomatis setelah dikirim. Admin dapat melihat level, skor, dan detail jawaban Anda.</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </form>
        @endif
    </div>
</main>

@if($questions->isNotEmpty() && !$showResult)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cards = Array.from(document.querySelectorAll('.question-card'));
        const overviewButtons = Array.from(document.querySelectorAll('.overview-button'));
        const radios = Array.from(document.querySelectorAll('input[type="radio"][data-question-index]'));
        const form = document.getElementById('placementForm');
        const questionCounter = document.getElementById('questionCounter');
        const questionSection = document.getElementById('questionSection');
        const progressText = document.getElementById('progressText');
        const progressBar = document.getElementById('progressBar');
        const prevButton = document.getElementById('prevButton');
        const nextButton = document.getElementById('nextButton');
        const timer = document.getElementById('timer');
        let currentQuestion = 0;
        let remainingSeconds = Number(@json($remainingSeconds));

        function answeredIndexes() {
            return new Set(radios.filter((radio) => radio.checked).map((radio) => Number(radio.dataset.questionIndex)));
        }

        function renderQuestion() {
            const answered = answeredIndexes();
            const progress = Math.round(((currentQuestion + 1) / cards.length) * 100);

            cards.forEach((card, index) => card.classList.toggle('hidden', index !== currentQuestion));
            cards.forEach((card) => {
                card.querySelectorAll('.answer-option').forEach((label) => {
                    const radio = label.querySelector('input[type="radio"]');
                    const stateIcon = label.querySelector('.answer-state');
                    label.classList.toggle('is-selected', radio.checked);
                    label.setAttribute('aria-checked', radio.checked ? 'true' : 'false');
                    if (stateIcon) {
                        stateIcon.textContent = radio.checked ? 'check_circle' : 'radio_button_unchecked';
                    }
                });
            });
            overviewButtons.forEach((button, index) => {
                button.className = 'overview-button h-9 rounded-lg border text-xs font-extrabold';
                if (index === currentQuestion) {
                    button.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                } else if (answered.has(index)) {
                    button.classList.add('bg-indigo-50', 'text-indigo-700', 'border-indigo-200');
                } else {
                    button.classList.add('bg-white', 'text-slate-500', 'border-slate-200');
                }
            });

            questionCounter.textContent = `Question ${currentQuestion + 1} of ${cards.length}`;
            questionSection.textContent = cards[currentQuestion].dataset.section;
            progressText.textContent = `${progress}% Complete`;
            progressBar.style.width = `${progress}%`;
            prevButton.disabled = currentQuestion === 0;
            prevButton.classList.toggle('opacity-50', currentQuestion === 0);
            nextButton.innerHTML = currentQuestion === cards.length - 1
                ? 'Kirim Test <span class="material-symbols-outlined text-[18px]">check</span>'
                : 'Lanjut <span class="material-symbols-outlined text-[18px]">chevron_right</span>';
        }

        radios.forEach((radio) => {
            radio.addEventListener('change', function () {
                renderQuestion();
            });
        });

        overviewButtons.forEach((button) => {
            button.addEventListener('click', function () {
                currentQuestion = Number(button.dataset.index);
                renderQuestion();
            });
        });

        prevButton.addEventListener('click', function () {
            if (currentQuestion > 0) {
                currentQuestion -= 1;
                renderQuestion();
            }
        });

        nextButton.addEventListener('click', function () {
            if (currentQuestion === cards.length - 1) {
                if (answeredIndexes().size === 0) {
                    alert('Silakan jawab minimal satu soal sebelum mengirim placement test.');
                    return;
                }

                form.submit();
                return;
            }

            currentQuestion += 1;
            renderQuestion();
        });

        function updateTimer() {
            const minutes = Math.floor(remainingSeconds / 60).toString().padStart(2, '0');
            const seconds = (remainingSeconds % 60).toString().padStart(2, '0');
            timer.textContent = `${minutes}:${seconds}`;

            if (remainingSeconds <= 0) {
                form.submit();
                return;
            }

            remainingSeconds -= 1;
        }

        renderQuestion();
        updateTimer();
        setInterval(updateTimer, 1000);
    });
</script>
@endif
@endsection
