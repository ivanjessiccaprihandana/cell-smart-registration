@csrf

@php
    $selectedDays = collect(old('days', $scheduleTemplate->days ?? []))->map(fn ($day) => (int) $day)->all();
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div>
        <label for="program_id" class="mb-2 block text-sm font-bold text-slate-700">Program</label>
        <select id="program_id" name="program_id" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            <option value="">Pilih program...</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" data-class-type="{{ in_array($program->name, $programsWithClassType, true) ? '1' : '0' }}" @selected((string) old('program_id', $scheduleTemplate->program_id ?? '') === (string) $program->id)>
                    {{ $program->name }}
                </option>
            @endforeach
        </select>
        @error('program_id')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div id="classTypeField">
        <label for="class_type" class="mb-2 block text-sm font-bold text-slate-700">Jenis Kelas</label>
        <select id="class_type" name="class_type"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            @foreach($classTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('class_type', $scheduleTemplate->class_type ?? 'Reguler') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('class_type')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div id="privatePackageField">
        <label for="private_package" class="mb-2 block text-sm font-bold text-slate-700">Paket Private</label>
        <select id="private_package" name="private_package"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            <option value="">Pilih paket private...</option>
            @foreach(($privatePackages ?? []) as $value => $label)
                <option value="{{ $value }}" @selected(old('private_package', $scheduleTemplate->private_package ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-2 text-xs font-medium text-slate-500">Diisi hanya untuk English for Adult kelas Private.</p>
        @error('private_package')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="level" class="mb-2 block text-sm font-bold text-slate-700">Level</label>
        <select id="level" name="level"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            <option value="">Semua level</option>
            @foreach($levels as $value => $label)
                <option value="{{ $value }}" @selected(old('level', $scheduleTemplate->level ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-2 text-xs font-medium text-slate-500">Kosongkan untuk BIMBEL atau kelas yang tidak perlu level khusus.</p>
        @error('level')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="batch_name" class="mb-2 block text-sm font-bold text-slate-700">Nama Batch</label>
        <input id="batch_name" name="batch_name" type="text" value="{{ old('batch_name', $scheduleTemplate->batch_name ?? '') }}"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
            placeholder="Contoh: Batch Juli 2026">
        <p class="mt-2 text-xs font-medium text-slate-500">Dipakai untuk membedakan periode kelas yang bisa dipilih siswa.</p>
        @error('batch_name')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="tutor_id" class="mb-2 block text-sm font-bold text-slate-700">Tutor</label>
        <select id="tutor_id" name="tutor_id"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            <option value="">Belum dipilih</option>
            @foreach($tutors as $tutor)
                <option value="{{ $tutor->id }}" @selected((string) old('tutor_id', $scheduleTemplate->tutor_id ?? '') === (string) $tutor->id)>
                    {{ $tutor->name }} - {{ $tutor->program?->name ?: 'Semua program' }} / {{ $tutor->level ?: 'Semua level' }}
                </option>
            @endforeach
        </select>
        @error('tutor_id')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-5">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <label for="registration_start_date" class="mb-2 block text-sm font-bold text-slate-700">Mulai Pendaftaran</label>
            <input id="registration_start_date" name="registration_start_date" type="date"
                value="{{ old('registration_start_date', $scheduleTemplate?->registration_start_date?->format('Y-m-d')) }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            @error('registration_start_date')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="registration_end_date" class="mb-2 block text-sm font-bold text-slate-700">Akhir Pendaftaran</label>
            <input id="registration_end_date" name="registration_end_date" type="date"
                value="{{ old('registration_end_date', $scheduleTemplate?->registration_end_date?->format('Y-m-d')) }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            @error('registration_end_date')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="learning_start_date" class="mb-2 block text-sm font-bold text-slate-700">Mulai Belajar</label>
            <input id="learning_start_date" name="learning_start_date" type="date"
                value="{{ old('learning_start_date', $scheduleTemplate?->learning_start_date?->format('Y-m-d')) }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            @error('learning_start_date')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="learning_end_date" class="mb-2 block text-sm font-bold text-slate-700">Akhir Belajar</label>
            <input id="learning_end_date" name="learning_end_date" type="date"
                value="{{ old('learning_end_date', $scheduleTemplate?->learning_end_date?->format('Y-m-d')) }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            @error('learning_end_date')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>
    <p class="mt-3 text-xs font-medium leading-5 text-slate-500">Jika tanggal batch diisi, siswa hanya bisa memilih jadwal ini selama periode pendaftaran. Setelah pembayaran diterima, jadwal siswa dibuat mengikuti periode belajar.</p>
</div>

<div class="mt-6">
    <label class="mb-2 block text-sm font-bold text-slate-700">Hari Belajar</label>
    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
        @foreach($dayLabels as $value => $label)
            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 bg-white p-4 text-sm transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                <input type="checkbox" name="days[]" value="{{ $value }}" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(in_array((int) $value, $selectedDays, true))>
                <span class="font-bold text-slate-900">{{ $label }}</span>
            </label>
        @endforeach
    </div>
    <p class="mt-2 text-xs font-medium text-slate-500">Pilih 1 hari untuk sesi offline TOEIC/TOEFL, atau 2 hari untuk kelas mingguan.</p>
    @error('days')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    @error('days.*')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
</div>

<div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <label for="start_time" class="mb-2 block text-sm font-bold text-slate-700">Jam Mulai</label>
        <input id="start_time" name="start_time" type="time" value="{{ old('start_time', $scheduleTemplate?->start_time?->format('H:i')) }}" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
        @error('start_time')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="end_time" class="mb-2 block text-sm font-bold text-slate-700">Jam Selesai</label>
        <input id="end_time" name="end_time" type="time" value="{{ old('end_time', $scheduleTemplate?->end_time?->format('H:i')) }}" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
        @error('end_time')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <label for="class_room_id" class="mb-2 block text-sm font-bold text-slate-700">Ruang Kelas</label>
        <select id="class_room_id" name="class_room_id" required
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
            <option value="">Pilih ruang...</option>
            @foreach($classRooms as $room)
                <option
                    value="{{ $room->id }}"
                    data-category="{{ $room->category }}"
                    data-capacity="{{ $room->capacity }}"
                    @selected((string) old('class_room_id', $scheduleTemplate->class_room_id ?? '') === (string) $room->id)
                >
                    {{ $room->name }} - {{ $room->category }} / {{ $room->capacity }} siswa
                </option>
            @endforeach
        </select>
        <p class="mt-2 text-xs font-medium text-slate-500">English memakai ruang English. BIMBEL memakai ruang Bimbel.</p>
        @error('class_room_id')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="max_students" class="mb-2 block text-sm font-bold text-slate-700">Kapasitas Jadwal</label>
        <input id="max_students" name="max_students" type="number" min="1" max="8" value="{{ old('max_students', $scheduleTemplate->max_students ?? 8) }}"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
        <p id="capacityHint" class="mt-2 text-xs font-medium text-slate-500">Reguler, Conversation, dan BIMBEL maksimal 8 siswa. Private otomatis 1 siswa.</p>
        @error('max_students')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6">
    <label for="notes" class="mb-2 block text-sm font-bold text-slate-700">Catatan</label>
    <textarea id="notes" name="notes" rows="3"
        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">{{ old('notes', $scheduleTemplate->notes ?? '') }}</textarea>
    @error('notes')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
</div>

<label class="mt-6 flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm font-bold text-slate-800">
    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_active', $scheduleTemplate->is_active ?? true))>
    Aktif
</label>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.schedule-templates.index') }}" class="inline-flex h-12 items-center justify-center rounded-lg border border-slate-300 px-5 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Batal</a>
    <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-6 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
        <span class="material-symbols-outlined text-[18px]">save</span>
        Simpan Batch Jadwal
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const programSelect = document.getElementById('program_id');
        const classTypeField = document.getElementById('classTypeField');
        const classTypeSelect = document.getElementById('class_type');
        const privatePackageField = document.getElementById('privatePackageField');
        const privatePackageSelect = document.getElementById('private_package');
        const classRoomSelect = document.getElementById('class_room_id');
        const maxStudentsInput = document.getElementById('max_students');
        const capacityHint = document.getElementById('capacityHint');

        const syncClassType = () => {
            const option = programSelect.options[programSelect.selectedIndex];
            const usesClassType = option?.dataset.classType === '1';

            classTypeField.classList.toggle('hidden', !usesClassType);
            classTypeSelect.disabled = !usesClassType;

            if (!usesClassType) {
                classTypeSelect.value = '';
            } else if (!classTypeSelect.value) {
                classTypeSelect.value = 'Reguler';
            }

            syncPrivatePackage();
            syncCapacity();
            filterRooms();
        };

        const selectedClassType = () => classTypeField.classList.contains('hidden') ? '' : classTypeSelect.value;

        const syncPrivatePackage = () => {
            const isPrivate = selectedClassType() === 'Private';

            privatePackageField.classList.toggle('hidden', !isPrivate);
            privatePackageSelect.disabled = !isPrivate;

            if (!isPrivate) {
                privatePackageSelect.value = '';
            }
        };

        const syncCapacity = () => {
            const isPrivate = selectedClassType() === 'Private';
            const selectedRoom = classRoomSelect.options[classRoomSelect.selectedIndex];
            const roomCapacity = Number(selectedRoom?.dataset.capacity || 8);
            const maxValue = isPrivate ? 1 : Math.min(8, roomCapacity || 8);

            maxStudentsInput.value = maxValue;
            maxStudentsInput.readOnly = isPrivate;
            maxStudentsInput.max = String(maxValue);
            capacityHint.textContent = isPrivate
                ? 'Private otomatis 1 siswa untuk 1 kelas.'
                : `Kapasitas jadwal mengikuti ruang, maksimal ${maxValue} siswa.`;
        };

        const filterRooms = () => {
            const option = programSelect.options[programSelect.selectedIndex];
            const programName = option?.textContent?.toLowerCase() || '';
            const expectedCategory = programName.includes('bimbel') ? 'Bimbel' : 'English';
            let selectedStillVisible = true;

            Array.from(classRoomSelect.options).forEach((roomOption) => {
                if (!roomOption.value) {
                    roomOption.hidden = false;
                    return;
                }

                const shouldShow = roomOption.dataset.category === expectedCategory;
                roomOption.hidden = !shouldShow;

                if (roomOption.selected && !shouldShow) {
                    selectedStillVisible = false;
                }
            });

            if (!selectedStillVisible) {
                classRoomSelect.value = '';
            }
        };

        programSelect.addEventListener('change', syncClassType);
        classTypeSelect.addEventListener('change', () => {
            syncPrivatePackage();
            syncCapacity();
        });
        classRoomSelect.addEventListener('change', syncCapacity);
        syncClassType();
    });
</script>
