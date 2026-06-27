@extends('layouts.admin')

@php
    $pageTitle = 'Edit Pendaftar';
    $registrant = $registrant ?? null;
    $programs = $programs ?? collect();
    $classTypes = $classTypes ?? [];
    $privatePackages = $privatePackages ?? [];
    $paymentStatuses = $paymentStatuses ?? [];
    $programsWithClassType = $programsWithClassType ?? [];
@endphp

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Manajemen Pendaftar</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Edit Pendaftar</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Perbarui data siswa, program yang diambil, jenis kelas, dan status pembayaran.</p>
        </div>
        <a href="{{ route('admin.registrants.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Kembali
        </a>
    </section>

    @if($errors->any())
        <section class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </section>
    @endif

    <form method="POST" action="{{ route('admin.registrants.update', $registrant) }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
                <label for="name" class="mb-2 block text-sm font-bold text-slate-800">Nama Siswa</label>
                <input id="name" name="name" type="text" value="{{ old('name', $registrant->name) }}" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                @error('name')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="mb-2 block text-sm font-bold text-slate-800">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $registrant->email) }}" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                @error('email')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="whatsapp" class="mb-2 block text-sm font-bold text-slate-800">WhatsApp</label>
                <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp', $registrant->whatsapp) }}"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                @error('whatsapp')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="payment_status" class="mb-2 block text-sm font-bold text-slate-800">Status Pembayaran</label>
                <select id="payment_status" name="payment_status" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    @foreach($paymentStatuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('payment_status', $registrant->payment_status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('payment_status')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="program" class="mb-2 block text-sm font-bold text-slate-800">Program</label>
                <select id="program" name="program" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}"
                            data-uses-class-type="{{ in_array($program->name, $programsWithClassType, true) ? '1' : '0' }}"
                            @selected((string) old('program', $registrant->program) === (string) $program->id)>
                            {{ $program->name }}
                        </option>
                    @endforeach
                </select>
                @error('program')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div id="classTypeField">
                <label for="class_type" class="mb-2 block text-sm font-bold text-slate-800">Jenis Kelas</label>
                <select id="class_type" name="class_type"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Tanpa jenis kelas</option>
                    @foreach($classTypes as $value => $label)
                        <option value="{{ $value }}" @selected(old('class_type', $registrant->class_type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs font-medium text-slate-500">Private hanya tersedia untuk English for Adult.</p>
                @error('class_type')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div id="privatePackageField">
                <label for="private_package" class="mb-2 block text-sm font-bold text-slate-800">Paket Private</label>
                <select id="private_package" name="private_package"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Pilih paket private...</option>
                    @foreach($privatePackages as $value => $label)
                        <option value="{{ $value }}" @selected(old('private_package', $registrant->private_package) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs font-medium text-slate-500">Untuk English for Adult kelas Private: Conversation, TOEFL Preparation, atau TOEIC Preparation.</p>
                @error('private_package')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-6">
            <label for="address" class="mb-2 block text-sm font-bold text-slate-800">Alamat</label>
            <textarea id="address" name="address" rows="4"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">{{ old('address', $registrant->address) }}</textarea>
            @error('address')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.registrants.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>

    <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-rose-900">Batalkan pendaftaran</h3>
                <p class="mt-1 text-sm font-medium text-rose-700">Gunakan ini jika siswa batal mengambil program. Akun siswa tidak dihapus.</p>
            </div>
            <form method="POST" action="{{ route('admin.registrants.cancel', $registrant) }}" onsubmit="return confirm('Batalkan pendaftaran siswa ini? Akun siswa tetap tersimpan.');">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-rose-600 px-5 py-3 text-sm font-bold text-white hover:bg-rose-700">
                    <span class="material-symbols-outlined text-[20px]">person_cancel</span>
                    Batalkan Pendaftaran
                </button>
            </form>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const programSelect = document.getElementById('program');
        const classTypeField = document.getElementById('classTypeField');
        const classTypeSelect = document.getElementById('class_type');
        const privatePackageField = document.getElementById('privatePackageField');
        const privatePackageSelect = document.getElementById('private_package');

        const syncClassTypeField = () => {
            const selectedOption = programSelect.options[programSelect.selectedIndex];
            const usesClassType = selectedOption?.dataset.usesClassType === '1';

            classTypeField.classList.toggle('hidden', !usesClassType);

            if (!usesClassType) {
                classTypeSelect.value = '';
            } else if (!classTypeSelect.value) {
                classTypeSelect.value = 'Reguler';
            }

            syncPrivatePackageField();
        };

        const syncPrivatePackageField = () => {
            const isPrivate = !classTypeField.classList.contains('hidden') && classTypeSelect.value === 'Private';

            privatePackageField.classList.toggle('hidden', !isPrivate);
            privatePackageSelect.disabled = !isPrivate;

            if (!isPrivate) {
                privatePackageSelect.value = '';
            }
        };

        programSelect.addEventListener('change', syncClassTypeField);
        classTypeSelect.addEventListener('change', syncPrivatePackageField);
        syncClassTypeField();
    });
</script>
@endsection
