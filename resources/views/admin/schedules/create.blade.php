@extends('layouts.admin')

@php($pageTitle = 'Tambah Jadwal')

@section('content')
<div class="mx-auto max-w-3xl space-y-5">
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.schedules.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 hover:text-indigo-600" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h2 class="text-xl font-extrabold text-slate-950">Tambah Jadwal Kelas</h2>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.schedules.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="program_id" class="mb-2 block text-sm font-bold text-slate-700">Program</label>
                <select id="program_id" name="program_id" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Pilih program...</option>
                    @foreach($programs as $program)
                        <option
                            value="{{ $program->id }}"
                            data-class-type="{{ in_array($program->name, $programsWithClassType, true) ? '1' : '0' }}"
                            @selected(old('program_id') == $program->id)
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
                            <input type="radio" name="class_type" value="{{ $value }}" class="mt-1 h-4 w-4 text-indigo-600" @checked(old('class_type', 'Reguler') === $value)>
                            <span class="font-bold text-slate-900">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-2 text-xs font-medium text-slate-500">Dipakai untuk English for Kids, Teens, dan Adult agar jadwal sesuai Reguler, Private, atau Conversation.</p>
                @error('class_type')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="class_date" class="mb-2 block text-sm font-bold text-slate-700">Tanggal</label>
                <input id="class_date" name="class_date" type="date" value="{{ old('class_date') }}" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100" />
                @error('class_date')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="start_time" class="mb-2 block text-sm font-bold text-slate-700">Jam Mulai</label>
                    <input id="start_time" name="start_time" type="time" value="{{ old('start_time') }}" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100" />
                    @error('start_time')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="end_time" class="mb-2 block text-sm font-bold text-slate-700">Jam Selesai</label>
                    <input id="end_time" name="end_time" type="time" value="{{ old('end_time') }}" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100" />
                    @error('end_time')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="room" class="mb-2 block text-sm font-bold text-slate-700">Ruang / Kelas</label>
                <input id="room" name="room" type="text" value="{{ old('room') }}"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                    placeholder="Contoh: Ruang A / Online Zoom" />
                @error('room')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="notes" class="mb-2 block text-sm font-bold text-slate-700">Catatan</label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                    placeholder="Catatan opsional untuk jadwal ini.">{{ old('notes') }}</textarea>
                @error('notes')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.schedules.index') }}" class="inline-flex h-12 items-center justify-center rounded-lg border border-slate-300 px-5 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Batal</a>
                <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-teal-600 px-6 text-sm font-bold text-white hover:bg-teal-700">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const programSelect = document.getElementById('program_id');
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

        programSelect?.addEventListener('change', toggleClassType);
        toggleClassType();
    });
</script>
@endsection
