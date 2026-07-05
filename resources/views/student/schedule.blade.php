@extends('layouts.app')

@section('content')
@php
    $priceText = $program ? $program->formattedPriceForClassType($auth->class_type) : '-';
    $assignedSchedules = $assignedSchedules ?? collect();
    $scheduleTemplates = $scheduleTemplates ?? collect();
    $schedulePreferences = $schedulePreferences ?? collect();
    $requiresPlacementTest = $requiresPlacementTest ?? true;
    $isProgramFinished = $isProgramFinished ?? false;
    $scheduleDisplayMode = $scheduleDisplayMode ?? 'upcoming';
    $dayLabels = $dayLabels ?? [];
    $adminWhatsappUrl = 'https://wa.me/6281292538501?text=' . rawurlencode('Halo admin CELL English Course, saya ingin konsultasi jadwal belajar.');
    $mainSchedule = $assignedSchedules->first();
    $scheduleDays = $assignedSchedules
        ->map(fn ($schedule) => $dayLabels[$schedule->class_date->isoWeekday()] ?? $schedule->class_date->format('D'))
        ->unique()
        ->values()
        ->join(' & ');
    $periodStart = $assignedSchedules->min('class_date');
    $periodEnd = $assignedSchedules->max('class_date');
    $studentClassTypeText = trim(($auth->class_type ?: '') . ($auth->private_package ? ' - ' . $auth->private_package : ''));
    $defaultScheduleDescription = 'Kelas berlangsung 2 kali seminggu selama periode belajar. Detail setiap pertemuan dapat dilihat pada daftar pertemuan mendatang.';
    $scheduleDescription = $mainSchedule?->notes ?: $defaultScheduleDescription;
@endphp

<main class="min-h-[calc(100vh-4rem)] bg-slate-50 px-6 py-10 md:px-8">
    <div class="mx-auto max-w-6xl space-y-8">
        <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-indigo-600">Jadwal Belajar</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Jadwal Belajar Anda</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    Lihat jadwal kelas, ruang, tutor, dan daftar pertemuan selama periode belajar.
                </p>
            </div>
            <a href="{{ route('student.status') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 hover:border-indigo-500 hover:text-indigo-600">
                <span class="material-symbols-outlined text-[18px]">assignment_ind</span>
                Status Saya
            </a>
        </section>

        @if(session('success'))
            <section class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </section>
        @endif

        @if($errors->any())
            <section class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">
                {{ $errors->first() }}
            </section>
        @endif

        <section class="grid gap-6 lg:grid-cols-[1fr_320px]">
            <article class="overflow-hidden rounded-3xl border border-indigo-100 bg-white shadow-xl shadow-slate-900/5">
                <div class="bg-indigo-600 px-6 py-6 text-white md:px-8">
                    <p class="text-sm font-bold uppercase tracking-wide text-indigo-100">CELL English Course</p>
                    <h2 class="mt-2 text-3xl font-extrabold">Informasi Jadwal Belajar</h2>
                    <p class="mt-2 text-sm font-semibold text-indigo-100">
                        @if($assignedSchedules->isNotEmpty())
                            @if($isProgramFinished)
                                Program sudah selesai. Jadwal ditampilkan sebagai riwayat belajar siswa.
                            @else
                                Jadwal belajar sudah ditentukan. Silakan ikuti pertemuan sesuai periode yang tertera.
                            @endif
                        @elseif($requiresPlacementTest)
                            Placement test selesai, jadwal akan dipilih setelah konsultasi.
                        @else
                            Program ini tidak memerlukan placement test, jadwal akan dipilih setelah konsultasi.
                        @endif
                    </p>
                </div>

                <div class="grid gap-4 p-6 md:grid-cols-2 md:p-8">
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Level Hasil Test</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-950">{{ $requiresPlacementTest ? $latestPlacementAttempt?->level : 'Tidak Diperlukan' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Skor Test</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-950">{{ $requiresPlacementTest ? ($latestPlacementAttempt?->correct_answers . '/' . $latestPlacementAttempt?->total_questions . ' (' . $latestPlacementAttempt?->score_percentage . '%)') : 'Tidak Diperlukan' }}</p>
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
                        <p class="mt-2 text-xl font-extrabold text-indigo-700">
                            @if($isProgramFinished)
                                Program Selesai
                            @else
                                {{ $assignedSchedules->isNotEmpty() ? 'Jadwal Ditentukan' : 'Menunggu Konsultasi' }}
                            @endif
                        </p>
                    </div>
                </div>

                @if($assignedSchedules->isNotEmpty())
                    <div class="border-t border-slate-100 px-6 py-6 md:px-8">
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-950">Jadwal Belajar Anda</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $isProgramFinished ? 'Riwayat jadwal belajar yang sudah selesai.' : 'Ringkasan jadwal utama selama periode belajar.' }}
                                </p>
                            </div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $isProgramFinished ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">
                                {{ $isProgramFinished ? 'Program Selesai' : 'Jadwal Aktif' }}
                            </span>
                        </div>

                        <article class="mt-5 rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Program</p>
                                    <h4 class="mt-1 text-2xl font-extrabold text-slate-950">{{ $mainSchedule->program?->name ?? '-' }}</h4>
                                    @php
                                        $mainClassType = $mainSchedule->class_type ?: $auth->class_type;
                                        $mainPrivatePackage = $mainSchedule->private_package ?: $auth->private_package;
                                        $mainClassTypeText = trim(($mainClassType ?: '') . ($mainPrivatePackage ? ' - ' . $mainPrivatePackage : ''));
                                    @endphp
                                    @if($mainClassTypeText)
                                        <p class="mt-2 inline-flex rounded-full bg-white px-3 py-1 text-xs font-bold text-indigo-700">{{ $mainClassTypeText }}</p>
                                    @endif
                                </div>
                                <div class="rounded-xl bg-white px-4 py-3 text-sm font-extrabold text-indigo-700">
                                    {{ $mainSchedule->start_time->format('H:i') }} - {{ $mainSchedule->end_time->format('H:i') }}
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 text-sm font-semibold text-slate-700 md:grid-cols-2">
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-indigo-600">calendar_month</span>
                                    <span>{{ $scheduleDays ?: '-' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-indigo-600">meeting_room</span>
                                    <span>{{ $mainSchedule->classRoom?->name ?? $mainSchedule->room ?: 'Ruang belum diisi' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-indigo-600">person_book</span>
                                    <span>{{ $mainSchedule->tutor?->name ?: 'Tutor belum ditentukan' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-indigo-600">date_range</span>
                                    <span>{{ $periodStart?->format('d M Y') }} - {{ $periodEnd?->format('d M Y') }}</span>
                                </div>
                            </div>

                            <div class="mt-5 rounded-xl bg-white/70 px-4 py-3 text-sm font-semibold leading-6 text-slate-600">
                                {{ $isProgramFinished ? 'Program sudah selesai. Jadwal ini tetap tersimpan sebagai riwayat belajar dan dapat digunakan untuk melihat kembali periode, ruang, tutor, serta pertemuan yang pernah diikuti.' : $scheduleDescription }}
                            </div>
                        </article>

                        <div class="mt-6">
                            <h4 class="text-sm font-extrabold uppercase tracking-wide text-slate-500">
                                {{ $isProgramFinished ? 'Riwayat Pertemuan' : 'Pertemuan Mendatang' }}
                            </h4>
                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                @foreach($assignedSchedules as $schedule)
                                    <button type="button"
                                        class="schedule-detail-button flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-left text-sm transition hover:border-indigo-300 hover:bg-indigo-50"
                                        data-title="Pertemuan {{ $loop->iteration }}"
                                        data-date="{{ $schedule->class_date->format('d M Y') }}"
                                        data-day="{{ $dayLabels[$schedule->class_date->isoWeekday()] ?? $schedule->class_date->format('D') }}"
                                        data-time="{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}"
                                        data-program="{{ $schedule->program?->name ?? '-' }}"
                                        data-room="{{ $schedule->classRoom?->name ?? $schedule->room ?: 'Ruang belum diisi' }}"
                                        data-tutor="{{ $schedule->tutor?->name ?: 'Tutor belum ditentukan' }}"
                                        data-description="{{ $isProgramFinished ? 'Pertemuan ini sudah selesai dan tersimpan sebagai riwayat belajar siswa.' : ($schedule->notes ?: 'Pertemuan belajar sesuai jadwal aktif siswa. Hadir sesuai tanggal, jam, ruang, dan tutor yang sudah ditentukan.') }}">
                                        <span class="font-bold text-slate-900">{{ $schedule->class_date->format('d M Y') }}</span>
                                        <span class="inline-flex items-center gap-2 font-extrabold text-indigo-700">
                                            {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                            <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="border-t border-slate-100 px-6 py-6 md:px-8">
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-950">Pilih Jadwal Belajar</h3>
                                <p class="mt-1 text-sm text-slate-500">Pilih satu jadwal belajar dari CELL. Jadwal akan dipakai setelah pembayaran disetujui admin.</p>
                            </div>
                            @if($schedulePreferences->where('status', 'pending')->isNotEmpty())
                                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">Jadwal dipilih</span>
                            @endif
                        </div>

                        @if($scheduleTemplates->isNotEmpty())
                            <form method="POST" action="{{ route('student.schedule.preferences.store') }}" class="mt-5 space-y-3">
                                @csrf
                                @foreach($scheduleTemplates as $template)
                                    @php
                                        $days = collect($template->days ?? [])->map(fn ($day) => $dayLabels[$day] ?? $day)->join(' & ');
                                        $isSelected = $schedulePreferences->where('schedule_template_id', $template->id)->where('status', 'pending')->isNotEmpty();
                                    @endphp
                                    <label class="flex cursor-pointer items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                        <input type="radio" name="schedule_template_id" value="{{ $template->id }}" required class="schedule-preference-radio mt-1 h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked($isSelected)>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-sm font-extrabold text-slate-950">{{ $days }}</span>
                                            <span class="mt-1 block text-sm font-semibold text-indigo-700">{{ $template->start_time->format('H:i') }} - {{ $template->end_time->format('H:i') }}</span>
                                            <span class="mt-2 block text-xs font-medium text-slate-500">
                                                {{ $template->classRoom?->name ?? $template->room ?: 'Ruang belum ditentukan' }}{{ $template->tutor ? ' / Tutor: ' . $template->tutor->name : '' }} / Sisa {{ $template->remainingSeats() }} kursi
                                            </span>
                                        </span>
                                    </label>
                                @endforeach

                                <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                                    <p id="preferenceCounter" class="text-xs font-bold text-slate-500">Pilih satu jadwal belajar.</p>
                                    <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
                                        <span class="material-symbols-outlined text-[18px]">send</span>
                                        Simpan Jadwal
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="mt-5 rounded-2xl border border-amber-100 bg-amber-50 p-5 text-sm font-semibold leading-6 text-amber-800">
                                Belum ada pilihan jadwal belajar untuk program ini. Silakan hubungi admin agar jadwal pilihan dapat dibuat.
                            </div>
                        @endif
                    </div>
                @endif

            </article>

            <aside class="space-y-6">
                <article class="rounded-2xl border {{ $isProgramFinished ? 'border-blue-100 bg-blue-50' : ($assignedSchedules->isNotEmpty() ? 'border-emerald-100 bg-emerald-50' : 'border-amber-100 bg-amber-50') }} p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white {{ $isProgramFinished ? 'text-blue-700' : ($assignedSchedules->isNotEmpty() ? 'text-emerald-700' : 'text-amber-700') }}">
                            <span class="material-symbols-outlined">{{ $isProgramFinished ? 'history' : ($assignedSchedules->isNotEmpty() ? 'event_available' : 'event_note') }}</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold {{ $isProgramFinished ? 'text-blue-950' : ($assignedSchedules->isNotEmpty() ? 'text-emerald-950' : 'text-amber-950') }}">
                                {{ $isProgramFinished ? 'Program selesai' : ($assignedSchedules->isNotEmpty() ? 'Jadwal aktif' : 'Jadwal belum final') }}
                            </h2>
                            <p class="mt-2 text-sm font-semibold leading-6 {{ $isProgramFinished ? 'text-blue-800' : ($assignedSchedules->isNotEmpty() ? 'text-emerald-800' : 'text-amber-800') }}">
                                {{ $isProgramFinished ? 'Akun tetap aktif. Siswa masih dapat melihat riwayat jadwal, invoice, dan melakukan perpanjangan program.' : ($assignedSchedules->isNotEmpty() ? 'Jadwal ini sudah menjadi acuan belajar siswa. Jika ada perubahan mendesak, silakan hubungi admin.' : 'Pilih jadwal yang tersedia atau hubungi admin jika perlu menyesuaikan waktu belajar.') }}
                            </p>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-extrabold text-slate-950">Bantuan Jadwal</h2>
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">
                        Gunakan tombol WhatsApp jika perlu konfirmasi ruang, tutor, atau perubahan jadwal mendesak.
                    </p>
                    <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener" class="mt-5 inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 text-sm font-bold text-white hover:bg-emerald-700">
                        <svg class="h-5 w-5" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
                            <path d="M16.01 3.2A12.71 12.71 0 0 0 5.16 22.54L3.6 28.8l6.41-1.5A12.76 12.76 0 1 0 16.01 3.2Zm0 23.25c-2.05 0-3.95-.59-5.57-1.61l-.4-.25-3.8.89.92-3.7-.26-.42a10.48 10.48 0 1 1 9.11 5.09Zm5.78-7.84c-.32-.16-1.89-.93-2.18-1.04-.29-.11-.5-.16-.72.16-.21.32-.82 1.04-1.01 1.25-.18.21-.37.24-.69.08-.32-.16-1.35-.5-2.57-1.58-.95-.85-1.59-1.9-1.78-2.22-.18-.32-.02-.49.14-.65.14-.14.32-.37.48-.56.16-.18.21-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.73-.98-2.37-.26-.62-.52-.54-.72-.55h-.61c-.21 0-.56.08-.85.4-.29.32-1.12 1.09-1.12 2.66 0 1.57 1.15 3.09 1.31 3.3.16.21 2.26 3.45 5.48 4.84.77.33 1.37.53 1.84.68.77.24 1.47.21 2.02.13.62-.09 1.89-.77 2.16-1.52.27-.75.27-1.39.19-1.52-.08-.13-.29-.21-.61-.37Z" />
                        </svg>
                        Hubungi Admin
                    </a>
                </article>
            </aside>
        </section>
    </div>
</main>

<div id="scheduleDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 px-4 py-8">
    <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
            <div>
                <p id="modalMeetingTitle" class="text-sm font-bold uppercase tracking-wide text-indigo-600">Detail Pertemuan</p>
                <h2 id="modalMeetingDate" class="mt-1 text-2xl font-extrabold text-slate-950">-</h2>
            </div>
            <button type="button" id="closeScheduleModal" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-500 hover:text-indigo-600" aria-label="Tutup detail pertemuan">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="space-y-4 px-6 py-5">
            <div class="rounded-xl bg-indigo-50 px-4 py-3">
                <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Deskripsi</p>
                <p id="modalMeetingDescription" class="mt-1 text-sm font-semibold leading-6 text-slate-700">-</p>
            </div>

            <div class="grid gap-3 text-sm sm:grid-cols-2">
                <div class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Hari</p>
                    <p id="modalMeetingDay" class="mt-1 font-extrabold text-slate-950">-</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Jam</p>
                    <p id="modalMeetingTime" class="mt-1 font-extrabold text-indigo-700">-</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Program</p>
                    <p id="modalMeetingProgram" class="mt-1 font-extrabold text-slate-950">-</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Ruang</p>
                    <p id="modalMeetingRoom" class="mt-1 font-extrabold text-slate-950">-</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Tutor</p>
                    <p id="modalMeetingTutor" class="mt-1 font-extrabold text-slate-950">-</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const radios = Array.from(document.querySelectorAll('.schedule-preference-radio'));
        const counter = document.getElementById('preferenceCounter');
        const modal = document.getElementById('scheduleDetailModal');
        const closeModalButton = document.getElementById('closeScheduleModal');
        const detailButtons = Array.from(document.querySelectorAll('.schedule-detail-button'));

        const syncPreferenceLimit = () => {
            const selected = radios.some((radio) => radio.checked);

            if (counter) {
                counter.textContent = selected
                    ? 'Jadwal belajar sudah dipilih.'
                    : 'Pilih satu jadwal belajar.';
            }
        };

        radios.forEach((radio) => radio.addEventListener('change', syncPreferenceLimit));
        syncPreferenceLimit();

        const modalFields = {
            title: document.getElementById('modalMeetingTitle'),
            date: document.getElementById('modalMeetingDate'),
            day: document.getElementById('modalMeetingDay'),
            time: document.getElementById('modalMeetingTime'),
            program: document.getElementById('modalMeetingProgram'),
            room: document.getElementById('modalMeetingRoom'),
            tutor: document.getElementById('modalMeetingTutor'),
            description: document.getElementById('modalMeetingDescription'),
        };

        function openScheduleModal(button) {
            modalFields.title.textContent = button.dataset.title || 'Detail Pertemuan';
            modalFields.date.textContent = button.dataset.date || '-';
            modalFields.day.textContent = button.dataset.day || '-';
            modalFields.time.textContent = button.dataset.time || '-';
            modalFields.program.textContent = button.dataset.program || '-';
            modalFields.room.textContent = button.dataset.room || '-';
            modalFields.tutor.textContent = button.dataset.tutor || '-';
            modalFields.description.textContent = button.dataset.description || '-';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeScheduleModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        detailButtons.forEach((button) => {
            button.addEventListener('click', () => openScheduleModal(button));
        });

        closeModalButton?.addEventListener('click', closeScheduleModal);
        modal?.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeScheduleModal();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeScheduleModal();
            }
        });
    });
</script>
@endsection
