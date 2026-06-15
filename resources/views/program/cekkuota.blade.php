@extends('layouts.app')

@section('content')
@php
    $programs = $programs ?? collect();
    $categories = $programs->pluck('category')->filter()->unique()->values();
    $selectedCategory = request('kategori', 'semua');
@endphp

<main class="min-h-screen bg-slate-50">
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-6 py-12 md:px-8">
            <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div>
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-600">
                        Cek Kuota Program
                    </span>
                    <h1 class="mt-5 max-w-3xl text-4xl font-bold tracking-tight text-slate-950 md:text-5xl">
                        Lihat ketersediaan kelas CELL English Course
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">
                        Pilih kelas yang masih tersedia. Data kuota mengikuti program aktif yang diatur dari halaman admin.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-semibold text-slate-500">Program</p>
                        <p class="mt-2 text-3xl font-bold text-slate-950">{{ $totalPrograms ?? $programs->count() }}</p>
                    </div>
                    <div class="rounded-lg border border-emerald-100 bg-emerald-50 p-5">
                        <p class="text-sm font-semibold text-emerald-700">Tersedia</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $availablePrograms ?? 0 }}</p>
                    </div>
                    <div class="rounded-lg border border-rose-100 bg-rose-50 p-5">
                        <p class="text-sm font-semibold text-rose-700">Penuh</p>
                        <p class="mt-2 text-3xl font-bold text-rose-700">{{ $fullPrograms ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-10 md:px-8">
        <div class="flex flex-col gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <label class="relative block w-full md:max-w-md">
                <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input
                    id="programSearch"
                    type="search"
                    placeholder="Cari kelas, misalnya TOEFL atau BIMBEL SD"
                    class="h-12 w-full rounded-lg border-slate-200 pl-12 pr-4 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500"
                >
            </label>

            <div class="flex gap-2 overflow-x-auto pb-1 md:pb-0">
                <button type="button" class="quota-filter active rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white" data-category="semua">
                    Semua
                </button>
                @foreach($categories as $category)
                    <button type="button" class="quota-filter rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:text-indigo-600" data-category="{{ \Illuminate\Support\Str::slug($category) }}">
                        {{ $category }}
                    </button>
                @endforeach
            </div>
        </div>

        @if($programs->isEmpty())
            <div class="mt-8 rounded-lg border border-dashed border-slate-300 bg-white p-10 text-center">
                <span class="material-symbols-outlined text-5xl text-slate-300">event_busy</span>
                <h2 class="mt-4 text-2xl font-bold text-slate-950">Belum ada program aktif</h2>
                <p class="mt-2 text-slate-600">Silakan tambahkan program dan kuota dari halaman admin.</p>
            </div>
        @else
            <div id="programGrid" class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($programs as $program)
                    @php
                        $quota = $program->quota;
                        $registered = $program->registered_users_count ?? $program->registeredUsersCount();
                        $remaining = $program->remaining_quota ?? $program->remainingQuota();
                        $isFull = $program->is_full ?? $program->isFull();
                        $percent = $quota ? min(100, round(($registered / max(1, $quota)) * 100)) : 0;
                        $categorySlug = \Illuminate\Support\Str::slug($program->category ?? 'program');
                        $priceText = $program->price !== null ? 'Rp ' . number_format($program->price, 0, ',', '.') : 'Hubungi Admin';
                        $chooseUrl = auth()->check()
                            ? route('programs.index', ['program' => $program->id])
                            : route('login');
                    @endphp

                    <article
                        class="quota-card group flex min-h-[430px] flex-col rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-100/70"
                        data-category="{{ $categorySlug }}"
                        data-name="{{ \Illuminate\Support\Str::lower($program->name . ' ' . $program->category . ' ' . $program->description) }}"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <span class="inline-flex items-center rounded-full {{ $isFull ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }} px-4 py-2 text-sm font-bold">
                                {{ $isFull ? 'Kuota Penuh' : 'Masih Tersedia' }}
                            </span>
                            <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                <span class="material-symbols-outlined text-3xl">
                                    {{ ($program->category ?? '') === 'BIMBEL' ? 'event_available' : (($program->category ?? '') === 'Test Preparation' ? 'quiz' : (($program->category ?? '') === 'Private' ? 'person_search' : 'school')) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 flex-1">
                            <h2 class="text-3xl font-bold leading-tight text-slate-950">{{ $program->name }}</h2>
                            <p class="mt-2 text-sm font-bold uppercase tracking-wide text-indigo-600">{{ $program->category }}</p>
                            <p class="mt-6 min-h-[72px] text-base leading-7 text-slate-600">{{ $program->description }}</p>

                            <div class="mt-5 flex items-center justify-between rounded-lg bg-indigo-50 px-4 py-3">
                                <span class="text-xs font-bold uppercase tracking-wide text-indigo-500">Biaya</span>
                                <span class="text-lg font-extrabold text-indigo-700">{{ $priceText }}</span>
                            </div>

                            <div class="mt-6 rounded-lg bg-slate-50 p-5">
                                <div class="flex items-center justify-between">
                                    <p class="font-bold text-slate-700">Kuota</p>
                                    <p class="text-lg font-bold text-slate-950">
                                        {{ $registered }} / {{ $quota ?? 'Tidak dibatasi' }}
                                    </p>
                                </div>
                                <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-200">
                                    <div class="h-full rounded-full {{ $isFull ? 'bg-rose-500' : 'bg-indigo-600' }}" style="width: {{ $quota ? $percent : 12 }}%"></div>
                                </div>
                                <p class="mt-4 font-semibold {{ $isFull ? 'text-rose-600' : 'text-slate-500' }}">
                                    @if($quota === null)
                                        Kuota fleksibel
                                    @elseif($isFull)
                                        Kelas ini sudah penuh
                                    @else
                                        {{ $remaining }} kuota tersisa
                                    @endif
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ $chooseUrl }}"
                            class="mt-6 inline-flex h-14 items-center justify-center gap-3 rounded-lg {{ $isFull ? 'pointer-events-none bg-slate-200 text-slate-500' : 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700' }} px-5 text-base font-bold transition"
                            aria-disabled="{{ $isFull ? 'true' : 'false' }}"
                        >
                            {{ $isFull ? 'Kuota Penuh' : 'Pilih Program' }}
                            @unless($isFull)
                                <span class="material-symbols-outlined">arrow_forward</span>
                            @endunless
                        </a>
                    </article>
                @endforeach
            </div>

            <div id="emptySearchState" class="mt-8 hidden rounded-lg border border-dashed border-slate-300 bg-white p-10 text-center">
                <span class="material-symbols-outlined text-5xl text-slate-300">search_off</span>
                <h2 class="mt-4 text-2xl font-bold text-slate-950">Program tidak ditemukan</h2>
                <p class="mt-2 text-slate-600">Coba cari dengan nama kelas lain atau pilih kategori semua.</p>
            </div>
        @endif
    </section>
</main>

<script>
    const searchInput = document.getElementById('programSearch');
    const filterButtons = Array.from(document.querySelectorAll('.quota-filter'));
    const cards = Array.from(document.querySelectorAll('.quota-card'));
    const emptyState = document.getElementById('emptySearchState');
    let activeCategory = 'semua';

    function setActiveButton(button) {
        filterButtons.forEach((item) => {
            item.classList.remove('active', 'bg-indigo-600', 'text-white');
            item.classList.add('border', 'border-slate-200', 'bg-white', 'text-slate-600');
        });

        button.classList.add('active', 'bg-indigo-600', 'text-white');
        button.classList.remove('border', 'border-slate-200', 'bg-white', 'text-slate-600');
    }

    function filterPrograms() {
        const keyword = (searchInput?.value || '').trim().toLowerCase();
        let visibleCount = 0;

        cards.forEach((card) => {
            const matchesCategory = activeCategory === 'semua' || card.dataset.category === activeCategory;
            const matchesSearch = !keyword || card.dataset.name.includes(keyword);
            const isVisible = matchesCategory && matchesSearch;

            card.classList.toggle('hidden', !isVisible);
            if (isVisible) visibleCount += 1;
        });

        emptyState?.classList.toggle('hidden', visibleCount !== 0);
    }

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeCategory = button.dataset.category;
            setActiveButton(button);
            filterPrograms();
        });
    });

    searchInput?.addEventListener('input', filterPrograms);
</script>
@endsection
