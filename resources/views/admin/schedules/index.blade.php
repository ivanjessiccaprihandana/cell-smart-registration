@extends('layouts.admin')

@php
    $pageTitle = 'Jadwal Belajar Siswa';
    $scheduleStyle = 'border-indigo-200 bg-indigo-50 text-indigo-900';
@endphp

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Manajemen Kelas</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Jadwal Belajar Siswa</h2>
                <p class="mt-1 text-sm text-slate-500">Jadwal ditampilkan per kelas/pertemuan agar admin mudah mengecek kapasitas dan peserta.</p>
            </div>
            <a href="{{ route('admin.schedules.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Tambah Jadwal Belajar
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
                        @php
                            $daySchedules = $schedules->filter(fn ($schedule) => $schedule->class_date->isSameDay($day['date']));
                        @endphp

                        @if($daySchedules->isNotEmpty())
                            @foreach($daySchedules as $schedule)
                                @php
                                    $capacity = max(1, (int) ($schedule->capacity ?? $schedule->max_students ?? 1));
                                    $studentCount = (int) ($schedule->student_count ?? $schedule->students->count());
                                    $isFull = $studentCount >= $capacity;
                                @endphp
                            <article class="rounded-xl border p-3 shadow-sm {{ $scheduleStyle }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-extrabold leading-tight">{{ $schedule->program->name }}</p>
                                        @if($schedule->class_type)
                                            <p class="mt-1 inline-flex rounded-full bg-white/80 px-2 py-0.5 text-[11px] font-extrabold text-indigo-700">{{ $schedule->class_type }}</p>
                                        @endif
                                    </div>
                                    <span class="rounded-full {{ $isFull ? 'bg-rose-50 text-rose-700' : 'bg-white/80 text-indigo-700' }} px-2 py-1 text-[11px] font-extrabold">
                                        {{ $studentCount }}/{{ $capacity }}
                                    </span>
                                </div>

                                <div class="mt-3 space-y-1 text-xs font-bold text-slate-600">
                                    <p>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</p>
                                    <p>{{ $schedule->classRoom?->name ?? $schedule->room ?: 'Ruang belum diisi' }}</p>
                                    <p>{{ $schedule->tutor?->name ?? 'Tutor belum dipilih' }}</p>
                                </div>

                                <button type="button" data-schedule-detail="{{ $schedule->group_key }}" class="mt-3 inline-flex h-8 w-full items-center justify-center gap-1 rounded-lg bg-white/85 text-xs font-extrabold text-indigo-700 hover:bg-white">
                                    <span class="material-symbols-outlined text-[16px]">groups</span>
                                    Lihat Siswa
                                </button>
                            </article>
                            @endforeach
                        @else
                            <div class="flex h-24 items-center justify-center text-sm font-semibold text-slate-400">
                                Tidak ada jadwal
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div>
            <h3 class="text-lg font-extrabold text-slate-950">Ringkasan Kelas Minggu Ini</h3>
            <p class="mt-1 text-sm text-slate-500">Total {{ $schedules->count() }} pertemuan kelas, {{ $totalStudents }} siswa terjadwal.</p>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Jadwal</th>
                        <th class="px-4 py-3">Tutor</th>
                        <th class="px-4 py-3">Ruang</th>
                        <th class="px-4 py-3">Peserta</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @if($schedules->isNotEmpty())
                        @foreach($schedules as $schedule)
                        @php
                            $capacity = max(1, (int) ($schedule->capacity ?? $schedule->max_students ?? 1));
                            $studentCount = (int) ($schedule->student_count ?? $schedule->students->count());
                            $isFull = $studentCount >= $capacity;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-950">{{ $schedule->program->name }}</p>
                                @if($schedule->class_type)
                                    <p class="mt-1 inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $schedule->class_type }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <p class="font-bold text-slate-900">{{ $schedule->class_date->format('d M Y') }}</p>
                                <p class="mt-1 text-xs font-semibold">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($schedule->tutor)
                                    <p class="font-bold text-slate-950">{{ $schedule->tutor->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $schedule->tutor->level ?: 'Semua level' }}</p>
                                @else
                                    <span class="text-sm font-semibold text-slate-400">Belum dipilih</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $schedule->classRoom?->name ?? $schedule->room ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <p class="font-extrabold {{ $isFull ? 'text-rose-700' : 'text-slate-950' }}">{{ $studentCount }} / {{ $capacity }} siswa</p>
                                <p class="mt-1 text-xs font-semibold {{ $isFull ? 'text-rose-600' : 'text-emerald-600' }}">{{ $isFull ? 'Penuh' : 'Masih tersedia' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <button type="button" data-schedule-detail="{{ $schedule->group_key }}" class="inline-flex h-9 items-center justify-center gap-2 rounded-lg border border-slate-200 px-3 text-xs font-extrabold text-indigo-700 hover:border-indigo-500 hover:bg-indigo-50">
                                        <span class="material-symbols-outlined text-[18px]">groups</span>
                                        Lihat Siswa
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">Belum ada jadwal siswa pada minggu ini.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>
</div>

@foreach($schedules as $schedule)
    @php
        $capacity = max(1, (int) ($schedule->capacity ?? $schedule->max_students ?? 1));
        $students = $schedule->students->map(fn ($student) => [
            'name' => $student->name,
            'email' => $student->email,
            'phone' => $student->phone ?: $student->whatsapp ?: '-',
        ])->values();
        $detail = [
            'program' => $schedule->program->name,
            'class_type' => $schedule->class_type ?: '-',
            'date' => $schedule->class_date->format('d M Y'),
            'time' => $schedule->start_time->format('H:i') . ' - ' . $schedule->end_time->format('H:i'),
            'room' => $schedule->classRoom?->name ?? $schedule->room ?: '-',
            'tutor' => $schedule->tutor?->name ?? 'Tutor belum dipilih',
            'capacity' => $capacity,
            'student_count' => (int) ($schedule->student_count ?? $students->count()),
            'notes' => $schedule->notes ?: 'Tidak ada catatan tambahan.',
            'students' => $students,
        ];
    @endphp
    <script type="application/json" id="{{ $schedule->group_key }}-data">@json($detail)</script>
@endforeach

<div id="scheduleDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4">
    <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 p-5">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Detail Kelas</p>
                <h3 id="scheduleModalTitle" class="mt-1 text-xl font-extrabold text-slate-950">Jadwal</h3>
                <p id="scheduleModalMeta" class="mt-1 text-sm font-semibold text-slate-500"></p>
            </div>
            <button type="button" id="scheduleModalClose" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-rose-300 hover:text-rose-600" aria-label="Tutup detail">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="space-y-5 p-5">
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-500">Ruang</p>
                    <p id="scheduleModalRoom" class="mt-1 font-extrabold text-slate-950"></p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-500">Tutor</p>
                    <p id="scheduleModalTutor" class="mt-1 font-extrabold text-slate-950"></p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-500">Peserta</p>
                    <p id="scheduleModalCapacity" class="mt-1 font-extrabold text-slate-950"></p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-500">Jenis Kelas</p>
                    <p id="scheduleModalType" class="mt-1 font-extrabold text-slate-950"></p>
                </div>
            </div>

            <div>
                <p class="text-sm font-extrabold text-slate-950">Catatan</p>
                <p id="scheduleModalNotes" class="mt-2 rounded-xl border border-slate-100 bg-white p-3 text-sm font-semibold text-slate-600"></p>
            </div>

            <div>
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-extrabold text-slate-950">Daftar Siswa</p>
                    <span id="scheduleModalStudentBadge" class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-extrabold text-indigo-700"></span>
                </div>
                <div id="scheduleModalStudents" class="mt-3 max-h-72 space-y-2 overflow-y-auto"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('scheduleDetailModal');
        const closeButton = document.getElementById('scheduleModalClose');
        const title = document.getElementById('scheduleModalTitle');
        const meta = document.getElementById('scheduleModalMeta');
        const room = document.getElementById('scheduleModalRoom');
        const tutor = document.getElementById('scheduleModalTutor');
        const capacity = document.getElementById('scheduleModalCapacity');
        const type = document.getElementById('scheduleModalType');
        const notes = document.getElementById('scheduleModalNotes');
        const badge = document.getElementById('scheduleModalStudentBadge');
        const studentsContainer = document.getElementById('scheduleModalStudents');

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        const openModal = (detail) => {
            title.textContent = detail.program;
            meta.textContent = `${detail.date} | ${detail.time}`;
            room.textContent = detail.room;
            tutor.textContent = detail.tutor;
            capacity.textContent = `${detail.student_count} / ${detail.capacity} siswa`;
            type.textContent = detail.class_type;
            notes.textContent = detail.notes;
            badge.textContent = `${detail.students.length} siswa`;
            studentsContainer.replaceChildren();

            if (detail.students.length === 0) {
                const empty = document.createElement('p');
                empty.className = 'rounded-xl border border-dashed border-slate-200 p-4 text-center text-sm font-semibold text-slate-500';
                empty.textContent = 'Belum ada siswa pada kelas ini.';
                studentsContainer.appendChild(empty);
            } else {
                detail.students.forEach((student, index) => {
                    const item = document.createElement('div');
                    item.className = 'flex items-start justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3';

                    const info = document.createElement('div');
                    const name = document.createElement('p');
                    name.className = 'font-extrabold text-slate-950';
                    name.textContent = `${index + 1}. ${student.name}`;
                    const contact = document.createElement('p');
                    contact.className = 'mt-1 text-xs font-semibold text-slate-500';
                    contact.textContent = `${student.email} | ${student.phone}`;

                    info.append(name, contact);
                    item.appendChild(info);
                    studentsContainer.appendChild(item);
                });
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        document.querySelectorAll('[data-schedule-detail]').forEach((button) => {
            button.addEventListener('click', () => {
                const script = document.getElementById(`${button.dataset.scheduleDetail}-data`);
                if (!script) {
                    return;
                }

                openModal(JSON.parse(script.textContent));
            });
        });

        closeButton.addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    });
</script>
@endsection
