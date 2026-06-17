@extends('layouts.admin')

@php
    $pageTitle = 'Data Pendaftar';
    $registrants = $registrants ?? collect();
    $groupedRegistrants = $groupedRegistrants ?? collect();
    $programs = $programs ?? collect();
    $programLabels = $programLabels ?? [];
    $programSummaries = $programSummaries ?? collect();
    $selectedProgram = $selectedProgram ?? null;
    $selectedClassType = $selectedClassType ?? null;
    $classTypes = $classTypes ?? [];
    $paymentLabels = $paymentLabels ?? [];
    $paymentStyles = [
        'belum_upload' => 'bg-slate-100 text-slate-700',
        'menunggu_verifikasi' => 'bg-amber-50 text-amber-700',
        'diterima' => 'bg-emerald-50 text-emerald-700',
        'ditolak' => 'bg-rose-50 text-rose-700',
    ];
    $classTypeLabels = [
        'Reguler' => 'Reguler',
        'Private' => 'Private',
        'Conversation' => 'Conversation',
        'Tanpa Jenis Kelas' => 'Tanpa Jenis Kelas',
    ];
    $classTypeStyles = [
        'Reguler' => 'bg-sky-50 text-sky-700',
        'Private' => 'bg-violet-50 text-violet-700',
        'Conversation' => 'bg-emerald-50 text-emerald-700',
        'Tanpa Jenis Kelas' => 'bg-slate-100 text-slate-700',
    ];
    $selectedProgramName = $selectedProgram ? ($programLabels[$selectedProgram] ?? 'program ini') : null;
@endphp

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Manajemen Pendaftar</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Data Pendaftar</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Data siswa yang sudah memilih program, dikelompokkan berdasarkan program dan jenis kelas yang diambil.</p>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">
            <span class="material-symbols-outlined text-[20px]">payments</span>
            Verifikasi Pembayaran
        </a>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">Total Pendaftar</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $registrants->count() }}</p>
            <p class="mt-1 text-xs font-medium text-slate-500">{{ $selectedProgram || $selectedClassType ? 'Sesuai filter admin' : 'Semua program dan kelas' }}</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">Program Berisi Pendaftar</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $groupedRegistrants->count() }}</p>
            <p class="mt-1 text-xs font-medium text-slate-500">Kelompok berdasarkan pilihan siswa</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">Pembayaran Diterima</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $registrants->where('payment_status', 'diterima')->count() }}</p>
            <p class="mt-1 text-xs font-medium text-slate-500">Siswa yang sudah disetujui admin</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.registrants.index') }}" class="grid gap-4 lg:grid-cols-[1fr_260px_auto_auto] lg:items-end">
            <div>
                <label for="program" class="mb-2 block text-sm font-bold text-slate-800">Filter Program</label>
                <select id="program" name="program"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Semua program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" @selected((string) $selectedProgram === (string) $program->id)>{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="class_type" class="mb-2 block text-sm font-bold text-slate-800">Jenis Kelas</label>
                <select id="class_type" name="class_type"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Semua jenis kelas</option>
                    @foreach($classTypes as $value => $label)
                        <option value="{{ $value }}" @selected((string) $selectedClassType === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
                <span class="material-symbols-outlined text-[20px]">filter_alt</span>
                Terapkan
            </button>
            <a href="{{ route('admin.registrants.index') }}" class="inline-flex h-12 items-center justify-center rounded-lg border border-slate-300 px-5 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Reset</a>
        </form>
    </section>

    @if(!$selectedProgram && $programSummaries->isNotEmpty())
        <section class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            @foreach($programSummaries as $summary)
                <a href="{{ route('admin.registrants.index', array_filter(['program' => $summary['id'], 'class_type' => $selectedClassType])) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-extrabold text-slate-950">{{ $summary['name'] }}</h3>
                            <p class="mt-2 text-sm font-semibold text-slate-500">{{ $summary['total'] }} pendaftar</p>
                        </div>
                        <span class="material-symbols-outlined text-indigo-600">groups</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-emerald-50 px-3 py-2">
                            <p class="text-xs font-bold text-emerald-700">Diterima</p>
                            <p class="mt-1 text-lg font-extrabold text-emerald-800">{{ $summary['accepted'] }}</p>
                        </div>
                        <div class="rounded-xl bg-amber-50 px-3 py-2">
                            <p class="text-xs font-bold text-amber-700">Menunggu</p>
                            <p class="mt-1 text-lg font-extrabold text-amber-800">{{ $summary['pending'] }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </section>
    @endif

    <section class="space-y-5">
        @forelse($groupedRegistrants as $programId => $users)
            @php
                $classTypeGroups = $users
                    ->groupBy(fn ($user) => $user->class_type ?: 'Tanpa Jenis Kelas')
                    ->sortBy(function ($group, $classType) {
                        return [
                            'Reguler' => 10,
                            'Private' => 20,
                            'Conversation' => 30,
                            'Tanpa Jenis Kelas' => 40,
                        ][$classType] ?? 99;
                    });
            @endphp
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-950">{{ $programLabels[$programId] ?? 'Program tidak ditemukan' }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $users->count() }} pendaftar pada program ini.</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($classTypeGroups as $classType => $classUsers)
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $classTypeStyles[$classType] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $classTypeLabels[$classType] ?? $classType }}: {{ $classUsers->count() }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <a href="{{ route('admin.registrants.index', array_filter(['program' => $programId, 'class_type' => $selectedClassType])) }}" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 hover:text-indigo-700">
                            Lihat program ini
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($classTypeGroups as $classType => $classUsers)
                        <section>
                            <div class="bg-slate-50 px-6 py-4">
                                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $classTypeStyles[$classType] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $classTypeLabels[$classType] ?? $classType }}
                                        </span>
                                        <p class="text-sm font-bold text-slate-700">{{ $classUsers->count() }} peserta</p>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500">Kelompok kelas {{ strtolower($classTypeLabels[$classType] ?? $classType) }}</p>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                                    <thead class="bg-white text-xs font-bold uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-6 py-4">Siswa</th>
                                            <th class="px-6 py-4">Kontak</th>
                                            <th class="px-6 py-4">Pembayaran</th>
                                            <th class="px-6 py-4">Bukti</th>
                                            <th class="px-6 py-4">Update</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($classUsers as $user)
                                            @php($paymentStatus = $user->payment_status ?: 'belum_upload')
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-6 py-4">
                                                    <p class="font-bold text-slate-950">{{ $user->name }}</p>
                                                    <p class="mt-1 text-xs font-medium text-slate-500">{{ $user->email }}</p>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <p class="font-semibold text-slate-700">{{ $user->whatsapp ?: '-' }}</p>
                                                    <p class="mt-1 max-w-xs truncate text-xs text-slate-500">{{ $user->address ?: '-' }}</p>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $paymentStyles[$paymentStatus] ?? 'bg-slate-100 text-slate-700' }}">
                                                        {{ $paymentLabels[$paymentStatus] ?? $paymentStatus }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    @if($user->payment_proof_path)
                                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($user->payment_proof_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-indigo-200 px-3 py-2 text-xs font-bold text-indigo-700 hover:border-indigo-600 hover:bg-indigo-50">
                                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                                            Lihat Bukti
                                                        </a>
                                                    @else
                                                        <span class="text-xs font-semibold text-slate-400">Belum upload</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-slate-500">{{ $user->updated_at?->format('d M Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endforeach
                </div>
            </article>
        @empty
            <section class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <p class="mt-4 font-bold text-slate-900">Tidak ada data pendaftar</p>
                @if($selectedProgram || $selectedClassType)
                    <p class="mt-1 text-sm text-slate-500">
                        Tidak ada siswa di {{ $selectedProgramName ?: 'program yang dipilih' }}{{ $selectedClassType ? ' untuk kelas ' . $selectedClassType : '' }}.
                    </p>
                @else
                    <p class="mt-1 text-sm text-slate-500">Data akan muncul setelah siswa memilih program.</p>
                @endif
            </section>
        @endforelse
    </section>
</div>
@endsection
