@extends('layouts.app')

@section('content')
@php
    $programNameMatches = fn (string $actualName, string $expectedName) => \Illuminate\Support\Str::lower($actualName) === \Illuminate\Support\Str::lower($expectedName);
    $programByName = fn (string $name) => $programs->first(fn ($program) => $programNameMatches($program->name, $name));
    $price = fn ($program) => $program?->formattedPriceForClassType('Reguler', 'Hubungi Admin') ?? 'Hubungi Admin';
    $remaining = fn ($program) => $program && $program->remaining_quota !== null ? $program->remaining_quota . ' kuota tersisa' : 'Kuota mengikuti jadwal';
    $groups = [
        [
            'breadcrumb' => 'Bahasa Inggris',
            'title' => 'Pilih Sub-Program Bahasa Inggris',
            'description' => 'Pilih kelas sesuai usia dan kebutuhan siswa agar proses belajar lebih tepat, nyaman, dan mudah diikuti.',
            'programs' => [
                [
                    'name' => 'English for Kids',
                    'age' => 'Ages 6-12',
                    'icon' => 'sentiment_satisfied',
                    'description' => 'Membangun fondasi Bahasa Inggris melalui aktivitas ringan, visual, dan menyenangkan.',
                    'features' => ['Vocabulary dasar', 'Fun activities', 'Confidence speaking'],
                ],
                [
                    'name' => 'English for Teens',
                    'age' => 'Ages 13-18',
                    'icon' => 'groups',
                    'description' => 'Mempersiapkan remaja untuk komunikasi aktif, akademik, dan percaya diri berbahasa.',
                    'features' => ['Speaking practice', 'Grammar terarah', 'School support'],
                    'featured' => true,
                ],
                [
                    'name' => 'English for Adult',
                    'age' => 'Ages 18+',
                    'icon' => 'business_center',
                    'description' => 'Penguasaan Bahasa Inggris untuk komunikasi kerja, studi, dan kebutuhan profesional.',
                    'features' => ['Daily conversation', 'Professional English', 'Grammar for adults'],
                ],
            ],
        ],
        [
            'breadcrumb' => 'Conversation & Test Prep',
            'title' => 'Pilih Program Conversation atau Test Preparation',
            'description' => 'Pilih program sesuai target speaking, TOEIC, atau TOEFL.',
            'programs' => [
                [
                    'name' => 'English Conversation',
                    'age' => 'Speaking',
                    'icon' => 'record_voice_over',
                    'description' => 'Latihan percakapan aktif untuk meningkatkan kelancaran dan kepercayaan diri.',
                    'features' => ['Speaking drill', 'Role play', 'Active conversation'],
                ],
                [
                    'name' => 'TOEIC',
                    'age' => 'TOEIC',
                    'icon' => 'workspace_premium',
                    'description' => 'Persiapan TOEIC dengan latihan soal dan strategi pengerjaan.',
                    'features' => ['Listening', 'Reading', 'Test strategy'],
                ],
                [
                    'name' => 'TOEFL',
                    'age' => 'TOEFL',
                    'icon' => 'school',
                    'description' => 'Persiapan TOEFL untuk kebutuhan akademik dan tes kemampuan bahasa.',
                    'features' => ['Structure', 'Reading', 'Listening'],
                ],
            ],
        ],
        [
            'breadcrumb' => 'BIMBEL',
            'title' => 'Pilih Program BIMBEL Sekolah',
            'description' => 'Pilih jenjang bimbingan belajar sesuai kebutuhan siswa.',
            'programs' => [
                [
                    'name' => 'BIMBEL TK',
                    'age' => 'TK',
                    'icon' => 'child_care',
                    'description' => 'Pendampingan belajar awal dengan aktivitas yang ringan dan menyenangkan.',
                    'features' => ['Calistung dasar', 'Aktivitas anak', 'Pendampingan'],
                ],
                [
                    'name' => 'BIMBEL SD',
                    'age' => 'SD',
                    'icon' => 'menu_book',
                    'description' => 'Bimbingan mata pelajaran sekolah untuk siswa SD.',
                    'features' => ['PR sekolah', 'Materi harian', 'Latihan soal'],
                ],
                [
                    'name' => 'BIMBEL SMP',
                    'age' => 'SMP',
                    'icon' => 'auto_stories',
                    'description' => 'Pendampingan belajar untuk memperkuat pemahaman materi SMP.',
                    'features' => ['Konsep materi', 'Latihan soal', 'Evaluasi'],
                ],
                [
                    'name' => 'BIMBEL SMA',
                    'age' => 'SMA',
                    'icon' => 'history_edu',
                    'description' => 'Bimbingan belajar untuk siswa SMA dengan materi yang lebih terarah.',
                    'features' => ['Materi sekolah', 'Pembahasan soal', 'Persiapan ujian'],
                ],
            ],
        ],
    ];
@endphp

<main class="min-h-[calc(100vh-4rem)] bg-slate-900/70 px-6 py-10 md:px-8">
    <div class="mx-auto max-w-7xl rounded-[2rem] bg-white p-6 shadow-2xl shadow-slate-950/20 md:p-10">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3 text-sm font-bold text-slate-500">
                    <span>Kelas</span>
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                    <span>Ubah Program</span>
                    @if($paymentDeadline)
                        <span class="hidden sm:inline text-slate-300">|</span>
                        <span class="text-rose-600">Batas upload: {{ $paymentDeadline->format('d M Y H:i') }}</span>
                    @endif
                </div>
                <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-slate-950 md:text-4xl">Pilih Sub-Program</h1>
                <p class="mt-3 max-w-3xl text-sm font-semibold leading-6 text-slate-600">Pilih program baru terlebih dahulu. Setelah itu Anda akan memilih jenis kelas dan jadwal belajar.</p>
            </div>
            <a href="{{ route('programs.payment') }}" class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-md hover:border-indigo-500 hover:text-indigo-600" aria-label="Kembali ke pembayaran">
                <span class="material-symbols-outlined">close</span>
            </a>
        </div>

        <div class="mt-8 space-y-10">
            @foreach($groups as $group)
                @php
                    $availableCards = collect($group['programs'])
                        ->map(function ($card) use ($programByName) {
                            $card['program'] = $programByName($card['name']);
                            return $card;
                        })
                        ->filter(fn ($card) => $card['program'])
                        ->values();
                @endphp

                @if($availableCards->isNotEmpty())
                    <section>
                        <div>
                            <p class="text-sm font-bold text-indigo-600">{{ $group['breadcrumb'] }}</p>
                            <h2 class="mt-1 text-2xl font-extrabold text-slate-950">{{ $group['title'] }}</h2>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">{{ $group['description'] }}</p>
                        </div>

                        <div class="mt-5 grid gap-5 {{ $availableCards->count() === 4 ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }}">
                            @foreach($availableCards as $card)
                                @php
                                    $program = $card['program'];
                                    $isCurrent = (string) $program->id === (string) $currentProgramId;
                                    $isFeatured = (bool) ($card['featured'] ?? false);
                                    $isFull = (bool) $program->is_full;
                                @endphp

                                <article class="overflow-hidden rounded-2xl border {{ $isFeatured ? 'border-indigo-600 bg-indigo-600 text-white shadow-xl shadow-indigo-600/20' : 'border-slate-200 bg-white text-slate-950 shadow-sm' }}">
                                    <div class="{{ $isFeatured ? 'bg-indigo-700/30' : 'bg-indigo-50' }} flex h-40 flex-col justify-between p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="inline-flex rounded-full {{ $isFeatured ? 'bg-white text-indigo-700' : 'bg-indigo-600 text-white' }} px-3 py-1 text-xs font-extrabold">{{ $card['age'] }}</span>
                                            @if($isCurrent)
                                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-700">Saat ini</span>
                                            @elseif($isFeatured)
                                                <span class="inline-flex rounded-full bg-amber-400 px-3 py-1 text-xs font-extrabold text-white">Populer</span>
                                            @endif
                                        </div>
                                        <span class="material-symbols-outlined self-center text-[34px] {{ $isFeatured ? 'text-white/80' : 'text-indigo-500' }}">{{ $card['icon'] }}</span>
                                    </div>

                                    <div class="space-y-5 p-6">
                                        <div>
                                            <h3 class="text-xl font-extrabold">{{ $program->name }}</h3>
                                            <p class="mt-3 text-sm font-semibold leading-6 {{ $isFeatured ? 'text-white/80' : 'text-slate-600' }}">{{ $card['description'] }}</p>
                                        </div>

                                        <div class="space-y-3">
                                            @foreach($card['features'] as $feature)
                                                <div class="flex items-center gap-3 text-sm font-bold">
                                                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                                    <span>{{ $feature }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div>
                                            <p class="text-sm font-extrabold {{ $isFeatured ? 'text-white' : 'text-slate-700' }}">{{ $remaining($program) }}</p>
                                            <p class="mt-3 inline-flex rounded-lg {{ $isFeatured ? 'bg-white/15 text-white' : 'bg-indigo-50 text-indigo-700' }} px-4 py-2 text-base font-extrabold">Mulai {{ $price($program) }}</p>
                                        </div>

                                        @if($isFull && !$isCurrent)
                                            <span class="inline-flex h-12 w-full items-center justify-center rounded-lg bg-slate-200 text-sm font-extrabold text-slate-500">Kuota Penuh</span>
                                        @else
                                            <a href="{{ route('programs.index', ['program' => $program->id, 'change' => 1]) }}" class="inline-flex h-12 w-full items-center justify-center rounded-lg {{ $isFeatured ? 'bg-white text-indigo-700 hover:bg-indigo-50' : 'border border-indigo-600 bg-white text-indigo-700 hover:bg-indigo-50' }} text-sm font-extrabold">
                                                Pilih Program
                                            </a>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach
        </div>
    </div>
</main>
@endsection
