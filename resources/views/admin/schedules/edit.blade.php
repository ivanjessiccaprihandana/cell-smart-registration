@extends('layouts.admin')

@php($pageTitle = 'Edit Jadwal Siswa')

@section('content')
<div class="mx-auto max-w-3xl space-y-5">
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.schedules.index', ['week' => $schedule->class_date?->copy()->startOfWeek()->toDateString()]) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 hover:text-indigo-600" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h2 class="text-xl font-extrabold text-slate-950">Edit Jadwal Siswa</h2>
                <p class="mt-1 text-sm font-semibold text-slate-500">{{ $schedule->student?->name ?? 'Siswa' }} - {{ $schedule->program?->name ?? 'Program' }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="user_id" class="mb-2 block text-sm font-bold text-slate-700">Siswa</label>
                <select id="user_id" name="user_id" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Pilih siswa yang sudah mengambil program...</option>
                    @foreach($students as $student)
                        <option
                            value="{{ $student->id }}"
                            data-program="{{ $student->program }}"
                            data-class-type="{{ $student->class_type }}"
                            data-level="{{ $student->latestPlacementAttempt?->level }}"
                            @selected((string) old('user_id', $schedule->user_id) === (string) $student->id)
                        >
                            {{ $student->name }} - {{ $student->email }}{{ $student->latestPlacementAttempt?->level ? ' / ' . $student->latestPlacementAttempt->level : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs font-medium text-slate-500">Pilih siswa hasil konsultasi. Program dan jenis kelas harus sesuai dengan data pendaftaran siswa.</p>
                @error('user_id')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="program_id" class="mb-2 block text-sm font-bold text-slate-700">Program</label>
                <select id="program_id" name="program_id" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Pilih program...</option>
                    @foreach($programs as $program)
                        <option
                            value="{{ $program->id }}"
                            data-class-type="{{ in_array($program->name, $programsWithClassType, true) ? '1' : '0' }}"
                            @selected((string) old('program_id', $schedule->program_id) === (string) $program->id)
                        >
                            {{ $program->name }}
                        </option>
                    @endforeach
                </select>
                @error('program_id')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div id="classTypeWrapper" class="hidden">
                <label class="mb-2 block text-sm font-bold text-slate-700">Jenis Kelas</label>
                <div class="grid gap-3 md:grid-cols-3">
                    @foreach($classTypes as $value => $label)
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 text-sm transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="class_type" value="{{ $value }}" class="mt-1 h-4 w-4 text-indigo-600" @checked(old('class_type', $schedule->class_type ?? 'Reguler') === $value)>
                            <span class="font-bold text-slate-900">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-2 text-xs font-medium text-slate-500">Dipakai untuk English for Kids, Teens, dan Adult agar jadwal sesuai Reguler, Private, atau Conversation.</p>
                @error('class_type')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="tutor_id" class="mb-2 block text-sm font-bold text-slate-700">Tutor</label>
                <select id="tutor_id" name="tutor_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Pilih tutor...</option>
                    @foreach($tutors as $tutor)
                        <option
                            value="{{ $tutor->id }}"
                            data-program="{{ $tutor->program_id }}"
                            data-level="{{ $tutor->level }}"
                            @selected((string) old('tutor_id', $schedule->tutor_id) === (string) $tutor->id)
                        >
                            {{ $tutor->name }} - {{ $tutor->program?->name ?: 'Semua program' }} / {{ $tutor->level ?: 'Semua level' }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs font-medium text-slate-500">Tutor akan difilter sesuai program dan level hasil placement test siswa. Tutor semua program/level tetap bisa dipilih.</p>
                @error('tutor_id')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="class_date" class="mb-2 block text-sm font-bold text-slate-700">Tanggal</label>
                <input id="class_date" name="class_date" type="date" value="{{ old('class_date', $schedule->class_date?->format('Y-m-d')) }}" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100" />
                @error('class_date')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="start_time" class="mb-2 block text-sm font-bold text-slate-700">Jam Mulai</label>
                    <input id="start_time" name="start_time" type="time" value="{{ old('start_time', $schedule->start_time?->format('H:i')) }}" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100" />
                    @error('start_time')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="end_time" class="mb-2 block text-sm font-bold text-slate-700">Jam Selesai</label>
                    <input id="end_time" name="end_time" type="time" value="{{ old('end_time', $schedule->end_time?->format('H:i')) }}" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100" />
                    @error('end_time')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="class_room_id" class="mb-2 block text-sm font-bold text-slate-700">Ruang Kelas</label>
                <select id="class_room_id" name="class_room_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Belum dipilih</option>
                    @foreach($classRooms as $room)
                        <option value="{{ $room->id }}" data-category="{{ $room->category }}" @selected((string) old('class_room_id', $schedule->class_room_id) === (string) $room->id)>
                            {{ $room->name }} - {{ $room->category }} / {{ $room->capacity }} siswa
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs font-medium text-slate-500">Ruang akan difilter sesuai kategori program English atau BIMBEL.</p>
                @error('class_room_id')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="notes" class="mb-2 block text-sm font-bold text-slate-700">Catatan</label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                    placeholder="Catatan opsional untuk jadwal ini.">{{ old('notes', $schedule->notes) }}</textarea>
                @error('notes')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.schedules.index', ['week' => $schedule->class_date?->copy()->startOfWeek()->toDateString()]) }}" class="inline-flex h-12 items-center justify-center rounded-lg border border-slate-300 px-5 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Batal</a>
                <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-teal-600 px-6 text-sm font-bold text-white hover:bg-teal-700">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const studentSelect = document.getElementById('user_id');
        const programSelect = document.getElementById('program_id');
        const tutorSelect = document.getElementById('tutor_id');
        const classRoomSelect = document.getElementById('class_room_id');
        const classTypeWrapper = document.getElementById('classTypeWrapper');
        const classTypeInputs = document.querySelectorAll('input[name="class_type"]');

        function toggleClassType() {
            const selectedOption = programSelect.options[programSelect.selectedIndex];
            const shouldShow = selectedOption?.dataset.classType === '1';

            classTypeWrapper.classList.toggle('hidden', !shouldShow);
            classTypeInputs.forEach(function (input) {
                input.disabled = !shouldShow;
                input.required = shouldShow;
            });
        }

        function selectedClassType() {
            return Array.from(classTypeInputs).find((input) => input.checked)?.value || '';
        }

        function syncFromStudent() {
            const selectedOption = studentSelect.options[studentSelect.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                return;
            }

            if (selectedOption.dataset.program) {
                programSelect.value = selectedOption.dataset.program;
            }

            toggleClassType();

            if (selectedOption.dataset.classType) {
                classTypeInputs.forEach(function (input) {
                    input.checked = input.value === selectedOption.dataset.classType;
                });
            }
        }

        function filterStudents() {
            const programId = programSelect.value;
            const classType = selectedClassType();
            let selectedStillVisible = true;

            Array.from(studentSelect.options).forEach(function (option) {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                const matchesProgram = !programId || option.dataset.program === programId;
                const matchesClassType = classTypeWrapper.classList.contains('hidden') || !classType || option.dataset.classType === classType;
                const shouldShow = matchesProgram && matchesClassType;

                option.hidden = !shouldShow;

                if (option.selected && !shouldShow) {
                    selectedStillVisible = false;
                }
            });

            if (!selectedStillVisible) {
                studentSelect.value = '';
            }
        }

        function selectedStudentLevel() {
            const selectedOption = studentSelect.options[studentSelect.selectedIndex];
            return selectedOption?.dataset.level || '';
        }

        function filterTutors() {
            const programId = programSelect.value;
            const studentLevel = selectedStudentLevel();
            let selectedStillVisible = true;

            Array.from(tutorSelect.options).forEach(function (option) {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                const matchesProgram = !option.dataset.program || !programId || option.dataset.program === programId;
                const matchesLevel = !option.dataset.level || !studentLevel || option.dataset.level === studentLevel;
                const shouldShow = matchesProgram && matchesLevel;

                option.hidden = !shouldShow;

                if (option.selected && !shouldShow) {
                    selectedStillVisible = false;
                }
            });

            if (!selectedStillVisible) {
                tutorSelect.value = '';
            }
        }

        function filterRooms() {
            const selectedProgram = programSelect.options[programSelect.selectedIndex];
            const programName = selectedProgram?.textContent?.toLowerCase() || '';
            const expectedCategory = programName.includes('bimbel') ? 'Bimbel' : 'English';
            let selectedStillVisible = true;

            Array.from(classRoomSelect.options).forEach(function (option) {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                const shouldShow = option.dataset.category === expectedCategory;
                option.hidden = !shouldShow;

                if (option.selected && !shouldShow) {
                    selectedStillVisible = false;
                }
            });

            if (!selectedStillVisible) {
                classRoomSelect.value = '';
            }
        }

        programSelect?.addEventListener('change', toggleClassType);
        programSelect?.addEventListener('change', filterStudents);
        programSelect?.addEventListener('change', filterTutors);
        programSelect?.addEventListener('change', filterRooms);
        studentSelect?.addEventListener('change', syncFromStudent);
        studentSelect?.addEventListener('change', filterStudents);
        studentSelect?.addEventListener('change', filterTutors);
        classTypeInputs.forEach(function (input) {
            input.addEventListener('change', filterStudents);
        });

        toggleClassType();
        filterStudents();
        filterTutors();
        filterRooms();
    });
</script>
@endsection
