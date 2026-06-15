@extends('layouts.app')

@section('content')
@php
    $selectedProgram = (string) old('program', request('program', $auth->program ?? ''));
    $selectedClassType = old('class_type', $auth->class_type ?? 'Reguler');
    $classTypeProgramNames = ['English for Kids', 'English for Teens', 'English for Adult'];
    $isProgramLocked = filled($auth->program ?? null);
    $selectedProgramModel = $selectedProgramModel ?? $programs->firstWhere('id', $selectedProgram);
    $hasSelectedProgram = filled($selectedProgram) && $selectedProgramModel;
    $shouldLockProgram = $isProgramLocked && $selectedProgramModel;
    $programNameMatches = fn (string $actualName, string $expectedName) => \Illuminate\Support\Str::lower($actualName) === \Illuminate\Support\Str::lower($expectedName);
    $usesClassType = fn (string $name) => collect($classTypeProgramNames)
            ->contains(fn ($programName) => $programNameMatches($name, $programName));
    $shouldDisableClassType = $shouldLockProgram;
    $selectedClassType = old('class_type', $auth->class_type ?? 'Reguler');
    $variantPrice = fn ($program, string $classType) => $program?->formattedPriceForClassType($classType, 'hubungi admin') ?? 'hubungi admin';
    $programChoiceForName = function (string $name) use ($programs, $usesClassType, $variantPrice, $programNameMatches) {
        $program = $programs->first(fn ($item) => $programNameMatches($item->name, $name));

        if (!$program) {
            return null;
        }

        $quotaLabel = $program->quota ? $program->remaining_quota . ' kuota tersisa' : 'kuota tidak dibatasi';

        return [
            'id' => (string) $program->id,
            'name' => $name,
            'option_label' => $name . ' - ' . $quotaLabel,
            'class_type' => $usesClassType($program->name) ? '1' : '0',
            'price_regular' => $variantPrice($program, 'Reguler'),
            'price_private' => $variantPrice($program, 'Private'),
            'price_conversation' => $variantPrice($program, 'Conversation'),
            'is_full' => (bool) $program->is_full,
        ];
    };
    $programGroups = [
        'kids-teens' => [
            'label' => 'Kids & Teens',
            'description' => 'English for Kids, Teens, dan Adult.',
            'programs' => collect(['English for Kids', 'English for Teens', 'English for Adult'])
                ->map(fn ($name) => $programChoiceForName($name))
                ->filter()
                ->values()
                ->all(),
        ],
        'conversation-test' => [
            'label' => 'Conversation & Test Prep',
            'description' => 'Conversation, TOEIC, dan TOEFL.',
            'programs' => collect(['English Conversation', 'TOEIC', 'TOEFL'])
                ->map(fn ($name) => $programChoiceForName($name))
                ->filter()
                ->values()
                ->all(),
        ],
        'bimbel' => [
            'label' => 'BIMBEL Sekolah',
            'description' => 'Jenjang TK, SD, SMP, dan SMA.',
            'programs' => collect(['BIMBEL TK', 'BIMBEL SD', 'BIMBEL SMP', 'BIMBEL SMA'])
                ->map(fn ($name) => $programChoiceForName($name))
                ->filter()
                ->values()
                ->all(),
        ],
    ];
    $flatProgramChoices = collect($programGroups)->flatMap(fn ($group) => $group['programs']);
    $selectedProgramChoice = $selectedProgramModel
        ? $flatProgramChoices->first(fn ($choice) => $choice['id'] === (string) $selectedProgramModel->id || $programNameMatches($selectedProgramModel->name, $choice['name']))
        : null;
    $selectedProgramGroup = collect($programGroups)
        ->keys()
        ->first(fn ($groupKey) => collect($programGroups[$groupKey]['programs'])->contains(fn ($choice) => $selectedProgramChoice && $choice['name'] === $selectedProgramChoice['name']));
@endphp

<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>

<main class="min-h-[calc(100vh-4rem)] bg-slate-50 px-6 py-12 md:px-8">
    <div class="mx-auto w-full max-w-2xl">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
            <div class="border-b border-slate-200 bg-slate-50 px-6 pb-6 pt-8 md:px-10">
                <div class="relative mb-4 flex items-start justify-between">
                    <div class="absolute left-6 right-6 top-5 h-[2px] bg-slate-200"></div>
                    <div id="stepProgress" class="absolute left-6 top-5 h-[2px] w-1/3 bg-indigo-600 transition-all"></div>

                    <div class="relative z-10 flex flex-col items-center gap-2 step-indicator is-active" data-step-indicator="1">
                        <div class="step-number flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white shadow-md">1</div>
                        <span class="step-label text-xs font-semibold text-indigo-600">Data Diri</span>
                    </div>

                    <div class="relative z-10 flex flex-col items-center gap-2 step-indicator" data-step-indicator="2">
                        <div class="step-number flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-500">2</div>
                        <span class="step-label text-xs font-semibold text-slate-400">Pilih Program</span>
                    </div>

                    <div class="relative z-10 flex flex-col items-center gap-2 step-indicator" data-step-indicator="3">
                        <div class="step-number flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-500">3</div>
                        <span class="step-label text-xs font-semibold text-slate-400">Konfirmasi</span>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-10">
                <div class="mb-8">
                    <span class="mb-4 inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">CELL FUN N EASY ENGLISH</span>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Daftar Program CELL English Course</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Lengkapi data diri untuk mendaftar program Bahasa Inggris atau BIMBEL dari TK sampai SMA.</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if($shouldLockProgram)
                    <div class="mb-6 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-semibold leading-6 text-indigo-800">
                        Anda sudah terdaftar pada program ini. Pendaftaran tidak bisa dikirim ulang dari halaman ini.
                    </div>
                @endif

                <form id="registrationForm" method="POST" action="{{ route('programs.store') }}" class="space-y-6">
                    @csrf
                    @if($hasSelectedProgram)
                        <input type="hidden" name="program" value="{{ $selectedProgram }}">
                    @endif
                    <div id="formStepFields" class="space-y-6">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-slate-800">Nama Lengkap</label>
                            <div class="relative">
                                <input id="name" name="name" type="text" value="{{ $auth->name ?? old('name') }}" required autofocus
                                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 pr-11 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 disabled:bg-slate-100 disabled:border-slate-200 disabled:text-slate-500"
                                    placeholder="Masukkan nama sesuai identitas" disabled/>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-[20px] text-indigo-600">check_circle</span>
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="email" class="mb-2 block text-sm font-semibold text-slate-800">Email</label>
                                <input id="email" name="email" type="email" value="{{ $auth->email ?? old('email') }}" required
                                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 disabled:bg-slate-100 disabled:border-slate-200 disabled:text-slate-500"
                                    placeholder="contoh@email.com"
                                    disabled />
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="whatsapp" class="mb-2 block text-sm font-semibold text-slate-800">Nomor WhatsApp</label>
                                <div class="flex">
                                    <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-100 px-4 text-sm font-semibold text-slate-600">+62</span>
                                    <input id="whatsapp" name="whatsapp" type="tel" value="{{ old('whatsapp') }}" required
                                        class="w-full rounded-r-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                                        placeholder="812345678" />
                                </div>
                                @error('whatsapp')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="address" class="mb-2 block text-sm font-semibold text-slate-800">Alamat Lengkap</label>
                            <textarea id="address" name="address" rows="3" required
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                                placeholder="Masukkan alamat lengkap siswa">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                            <label for="program" class="mb-2 block text-sm font-semibold text-slate-800">Pilih Program</label>
                            <div class="relative">
                                @if($hasSelectedProgram)
                                    @php
                                        $quotaLabel = $selectedProgramModel->quota ? $selectedProgramModel->remaining_quota . ' kuota tersisa' : 'kuota tidak dibatasi';
                                        $priceLabel = $selectedProgramModel->formattedPriceForClassType(null, 'hubungi admin');
                                    @endphp
                                    <input id="program" type="text" value="{{ $selectedProgramModel->name }} " readonly
                                        data-program-name="{{ $selectedProgramModel->name }}"
                                        data-class-type="{{ $usesClassType($selectedProgramModel->name) ? '1' : '0' }}"
                                        data-price-regular="{{ $variantPrice($selectedProgramModel, 'Reguler') }}"
                                        data-price-private="{{ $variantPrice($selectedProgramModel, 'Private') }}"
                                        data-price-conversation="{{ $variantPrice($selectedProgramModel, 'Conversation') }}"
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 pr-11 text-sm text-slate-600 outline-none" />
                                    <span class="material-symbols-outlined pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[20px] text-indigo-600">lock</span>
                                @else
                                    <input id="program" name="program" type="hidden" value="{{ $selectedProgram }}">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="programCategory" class="mb-2 block text-sm font-semibold text-slate-800">Kategori</label>
                                            <div class="relative">
                                                <select id="programCategory" required
                                                    class="w-full appearance-none rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                                                    <option value="" disabled @selected(!$selectedProgramGroup)>Pilih kategori program...</option>
                                                    @foreach($programGroups as $groupKey => $group)
                                                        <option value="{{ $groupKey }}" @selected($selectedProgramGroup === $groupKey) @disabled(empty($group['programs']))>
                                                            {{ $group['label'] }}{{ empty($group['programs']) ? ' - belum ada program aktif' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="material-symbols-outlined pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-500">expand_more</span>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="programSubmenu" class="mb-2 block text-sm font-semibold text-slate-800">Sub-Program</label>
                                            <div class="relative">
                                                <select id="programSubmenu" required
                                                    class="w-full appearance-none rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                                                    <option value="" disabled selected>Pilih kategori dulu...</option>
                                                </select>
                                                <span class="material-symbols-outlined pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-500">expand_more</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($hasSelectedProgram)
                                <p class="mt-2 text-xs font-medium text-slate-500">
                                    {{ $shouldLockProgram ? 'Program sudah dipilih dan tidak perlu dipilih ulang.' : 'Program sudah dipilih dari halaman sebelumnya.' }}
                                </p>
                            @endif

                            <div id="classTypeWrapper" class="mt-5 hidden">
                                <label for="classType" class="mb-2 block text-sm font-semibold text-slate-800">Jenis Kelas</label>
                                <div class="grid gap-3 lg:grid-cols-3">
                                    @foreach([
                                        'Reguler' => 'Kelas reguler bersama siswa lain.',
                                        'Private' => 'Kelas privat dengan pendampingan lebih personal.',
                                        'Conversation' => 'Fokus latihan speaking dan percakapan aktif.',
                                    ] as $type => $description)
                                        <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 text-sm transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 {{ $shouldDisableClassType ? 'cursor-not-allowed opacity-75' : 'cursor-pointer' }}">
                                            <input type="radio" name="class_type" value="{{ $type }}" class="mt-1 h-4 w-4 text-indigo-600 disabled:cursor-not-allowed" @checked($selectedClassType === $type) @disabled($shouldDisableClassType)>
                                            <span>
                                                <span class="block font-bold text-slate-900">{{ $type }}</span>
                                                <span class="mt-1 block text-xs font-medium leading-5 text-slate-500">{{ $description }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-5 rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3">
                                <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Estimasi Biaya</p>
                                <p id="selectedPrice" class="mt-1 text-lg font-extrabold text-indigo-700">Pilih program dahulu</p>
                            </div>

                            <p class="mt-3 text-xs font-medium text-slate-500">Untuk English for Kids, Teens, dan Adult, pilih apakah kelasnya Reguler, Private, atau Conversation.</p>
                            @if($shouldDisableClassType)
                                <p class="mt-2 text-xs font-medium text-slate-500">
                                    {{ $shouldLockProgram ? 'Jenis kelas sudah tersimpan saat pendaftaran pertama.' : 'Jenis kelas mengikuti program yang dipilih.' }}
                                </p>
                            @endif
                            @error('program')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="confirmationStep" class="hidden space-y-6">
                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white">
                                    <span class="material-symbols-outlined">fact_check</span>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900">Konfirmasi Pendaftaran</h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">Periksa kembali data pendaftaran sebelum dikirim ke admin CELL English Course.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 rounded-2xl border border-slate-200 bg-white p-5">
                            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                                <span class="text-sm font-semibold text-slate-500">Nama</span>
                                <span id="confirmName" class="text-right text-sm font-bold text-slate-900">-</span>
                            </div>
                            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                                <span class="text-sm font-semibold text-slate-500">Email</span>
                                <span id="confirmEmail" class="text-right text-sm font-bold text-slate-900">-</span>
                            </div>
                            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                                <span class="text-sm font-semibold text-slate-500">WhatsApp</span>
                                <span id="confirmWhatsapp" class="text-right text-sm font-bold text-slate-900">-</span>
                            </div>
                            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                                <span class="text-sm font-semibold text-slate-500">Program</span>
                                <span id="confirmProgram" class="text-right text-sm font-bold text-indigo-700">-</span>
                            </div>
                            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                                <span class="text-sm font-semibold text-slate-500">Estimasi Biaya</span>
                                <span id="confirmPrice" class="text-right text-sm font-bold text-indigo-700">-</span>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-slate-500">Alamat</span>
                                <p id="confirmAddress" class="mt-2 rounded-xl bg-slate-50 p-4 text-sm font-medium leading-6 text-slate-700">-</p>
                            </div>
                        </div>
                    </div>


                    @if($shouldLockProgram)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[22px] text-indigo-600">lock</span>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Pendaftaran sudah tersimpan</p>
                                    <p class="mt-1 text-sm font-medium leading-6 text-slate-600">Anda tidak perlu lanjut ke step konfirmasi lagi untuk program yang sama.</p>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('student.status') }}" class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 active:scale-[0.98]">
                            Lihat Status Saya
                            <span class="material-symbols-outlined text-[20px]">assignment_ind</span>
                        </a>
                    @else
                        <button id="continueButton" type="button" class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 active:scale-[0.98]">
                            Lanjut Daftar Program
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </button>
                    @endif

                    <div id="confirmationActions" class="hidden grid grid-cols-1 gap-3 md:grid-cols-[0.45fr_1fr]">
                        <button id="backButton" type="button" class="flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-indigo-600 hover:text-indigo-600">
                            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                            Kembali
                        </button>
                        <button type="submit" class="flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 active:scale-[0.98]">
                            Kirim Pendaftaran
                            <span class="material-symbols-outlined text-[20px]">send</span>
                        </button>
                    </div>
                </form>
            </div>

            
        </div>

        <div class="mt-10 flex flex-wrap justify-center gap-8 text-sm font-semibold text-slate-500">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined">verified_user</span>
                Data Pendaftaran Aman
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined">workspace_premium</span>
                CELL English Course
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined">support_agent</span>
                Program TK-SMA
            </div>
        </div>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('registrationForm');
        const fieldsStep = document.getElementById('formStepFields');
        const confirmationStep = document.getElementById('confirmationStep');
        const continueButton = document.getElementById('continueButton');
        const confirmationActions = document.getElementById('confirmationActions');
        const backButton = document.getElementById('backButton');
        const stepProgress = document.getElementById('stepProgress');
        const indicators = document.querySelectorAll('.step-indicator');

        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const whatsappInput = document.getElementById('whatsapp');
        const addressInput = document.getElementById('address');
        const programInput = document.getElementById('program');
        const programCategoryInput = document.getElementById('programCategory');
        const programSubmenuInput = document.getElementById('programSubmenu');
        const classTypeWrapper = document.getElementById('classTypeWrapper');
        const classTypeInputs = document.querySelectorAll('input[name="class_type"]');
        const selectedPrice = document.getElementById('selectedPrice');
        const isProgramLocked = @json((bool) $shouldLockProgram);
        const shouldDisableClassType = @json((bool) $shouldDisableClassType);
        const programGroups = @json($programGroups);
        const initialProgramGroup = @json($selectedProgramGroup);
        const initialProgramId = @json($selectedProgram);

        function setStep(step) {
            indicators.forEach(function (indicator) {
                const indicatorStep = Number(indicator.dataset.stepIndicator);
                const number = indicator.querySelector('.step-number');
                const label = indicator.querySelector('.step-label');
                const isActive = indicatorStep <= step;

                number.classList.toggle('bg-indigo-600', isActive);
                number.classList.toggle('text-white', isActive);
                number.classList.toggle('shadow-md', isActive);
                number.classList.toggle('bg-slate-200', !isActive);
                number.classList.toggle('text-slate-500', !isActive);
                label.classList.toggle('text-indigo-600', isActive);
                label.classList.toggle('text-slate-400', !isActive);
            });

            if (stepProgress) {
                stepProgress.style.width = step === 3 ? '100%' : '50%';
            }
        }

        function selectedProgramText() {
            const selectedProgram = currentProgramData();
            const programText = selectedProgram?.programName || selectedProgram?.text || '-';
            const checkedClassType = document.querySelector('input[name="class_type"]:checked');

            if (!classTypeWrapper.classList.contains('hidden') && checkedClassType) {
                return `${programText} - ${checkedClassType.value}`;
            }

            return programText;
        }

        function selectedClassType() {
            return document.querySelector('input[name="class_type"]:checked')?.value || 'Reguler';
        }

        function currentProgramData() {
            if (!programInput) {
                return null;
            }

            if (programSubmenuInput) {
                const selectedOption = programSubmenuInput.options[programSubmenuInput.selectedIndex];

                if (!selectedOption || !selectedOption.value) {
                    return null;
                }

                return {
                    value: selectedOption.value,
                    text: selectedOption.text.trim(),
                    ...selectedOption.dataset,
                };
            }

            if (programInput.tagName === 'SELECT') {
                const selectedOption = programInput.options[programInput.selectedIndex];

                if (!selectedOption || !selectedOption.value) {
                    return null;
                }

                return {
                    value: selectedOption.value,
                    text: selectedOption.text.trim(),
                    ...selectedOption.dataset,
                };
            }

            return {
                value: programInput.value,
                text: programInput.value,
                ...programInput.dataset,
            };
        }

        function populateSubPrograms(selectedProgramId = '') {
            if (!programCategoryInput || !programSubmenuInput) {
                return;
            }

            const selectedGroup = programGroups[programCategoryInput.value];
            const programs = selectedGroup?.programs || [];
            programSubmenuInput.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.disabled = true;
            placeholder.selected = true;
            placeholder.textContent = programs.length ? 'Pilih sub-program...' : 'Belum ada program aktif';
            programSubmenuInput.appendChild(placeholder);

            programs.forEach(function (program) {
                const option = document.createElement('option');
                option.value = program.id;
                option.textContent = `${program.option_label} - ${program.price_regular}${program.is_full ? ' (Penuh)' : ''}`;
                option.disabled = Boolean(program.is_full) && selectedProgramId !== program.id;
                option.dataset.programName = program.name;
                option.dataset.classType = program.class_type;
                option.dataset.priceRegular = program.price_regular;
                option.dataset.pricePrivate = program.price_private;
                option.dataset.priceConversation = program.price_conversation;
                option.selected = selectedProgramId === program.id;
                programSubmenuInput.appendChild(option);
            });

            if (selectedProgramId && programSubmenuInput.value !== selectedProgramId) {
                programSubmenuInput.value = '';
            }

            programInput.value = programSubmenuInput.value || '';
        }

        function selectedProgramPrice() {
            const selectedProgram = currentProgramData();

            if (!selectedProgram) {
                return 'Pilih program dahulu';
            }

            if (classTypeWrapper.classList.contains('hidden')) {
                return selectedProgram.priceRegular || 'hubungi admin';
            }

            return {
                Private: selectedProgram.pricePrivate,
                Conversation: selectedProgram.priceConversation,
                Reguler: selectedProgram.priceRegular,
            }[selectedClassType()] || selectedProgram.priceRegular || 'hubungi admin';
        }

        function updateProgramSelection() {
            if (programSubmenuInput) {
                programInput.value = programSubmenuInput.value || '';
            }

            const selectedProgram = currentProgramData();
            const shouldShowClassType = selectedProgram?.classType === '1';

            classTypeWrapper.classList.toggle('hidden', !shouldShowClassType);
            classTypeInputs.forEach(function (input) {
                input.required = shouldShowClassType;
                input.disabled = shouldDisableClassType || isProgramLocked || !shouldShowClassType;
            });

            if (selectedPrice) {
                selectedPrice.textContent = selectedProgramPrice();
            }
        }

        function fillConfirmation() {
            document.getElementById('confirmName').textContent = nameInput.value || '-';
            document.getElementById('confirmEmail').textContent = emailInput.value || '-';
            document.getElementById('confirmWhatsapp').textContent = whatsappInput.value ? '+62 ' + whatsappInput.value : '-';
            document.getElementById('confirmProgram').textContent = selectedProgramText();
            document.getElementById('confirmPrice').textContent = selectedProgramPrice();
            document.getElementById('confirmAddress').textContent = addressInput.value || '-';
        }

        function goToConfirmation() {
            if (!form.reportValidity()) {
                return;
            }

            fillConfirmation();
            fieldsStep.classList.add('hidden');
            continueButton.classList.add('hidden');
            confirmationStep.classList.remove('hidden');
            confirmationActions.classList.remove('hidden');
            setStep(3);
            confirmationStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function backToForm() {
            confirmationStep.classList.add('hidden');
            confirmationActions.classList.add('hidden');
            fieldsStep.classList.remove('hidden');
            continueButton.classList.remove('hidden');
            setStep(2);
            fieldsStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        continueButton?.addEventListener('click', goToConfirmation);
        backButton?.addEventListener('click', backToForm);
        if (programInput?.tagName === 'SELECT') {
            programInput.addEventListener('change', updateProgramSelection);
        }
        programCategoryInput?.addEventListener('change', function () {
            populateSubPrograms();
            updateProgramSelection();
        });
        programSubmenuInput?.addEventListener('change', updateProgramSelection);
        classTypeInputs.forEach(function (input) {
            input.addEventListener('change', updateProgramSelection);
        });
        if (programCategoryInput && initialProgramGroup) {
            programCategoryInput.value = initialProgramGroup;
            populateSubPrograms(initialProgramId);
        }
        updateProgramSelection();
        setStep(2);
    });
</script>
@endsection
