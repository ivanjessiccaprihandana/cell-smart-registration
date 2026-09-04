@extends('layouts.admin')

@php
    $pageTitle = 'Rekap Keseluruhan';
    $stats = $stats ?? [];
    $enrollmentStatusLabels = [
        'pending' => 'Menunggu',
        'active' => 'Aktif',
        'completed' => 'Selesai',
        'rejected' => 'Ditolak',
    ];
    $enrollmentStatusStyles = [
        'pending' => 'bg-amber-50 text-amber-700',
        'active' => 'bg-emerald-50 text-emerald-700',
        'completed' => 'bg-sky-50 text-sky-700',
        'rejected' => 'bg-rose-50 text-rose-700',
    ];
    $paymentStatusLabels = [
        'belum_upload' => 'Belum Upload',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'diterima' => 'Diterima',
        'ditolak' => 'Ditolak',
    ];
    $paymentStatusStyles = [
        'belum_upload' => 'bg-slate-100 text-slate-700',
        'menunggu_verifikasi' => 'bg-amber-50 text-amber-700',
        'diterima' => 'bg-emerald-50 text-emerald-700',
        'ditolak' => 'bg-rose-50 text-rose-700',
    ];
    $periodLabel = $from || $to
        ? (($from?->translatedFormat('d M Y') ?? 'Awal') . ' – ' . ($to?->translatedFormat('d M Y') ?? 'Sekarang'))
        : 'Semua waktu';
@endphp

@section('content')
<style>
    @media print {
        aside, header, .print-hidden { display: none !important; }
        body { background: white !important; }
        main { padding: 0 !important; }
        .print-card { break-inside: avoid; box-shadow: none !important; }
    }
</style>

<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Laporan Admin</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Rekap Keseluruhan</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Ringkasan pendaftaran, pembayaran, placement test, jadwal kelas, dan sumber daya CELL dalam satu halaman.
            </p>
            <p class="mt-2 text-xs font-bold uppercase tracking-wide text-slate-400">Periode: {{ $periodLabel }}</p>
        </div>
        <div class="print-hidden flex flex-wrap gap-2">
            <a href="{{ route('admin.recap.export', array_filter(['from' => $from?->toDateString(), 'to' => $to?->toDateString()])) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-700">
                <span class="material-symbols-outlined text-[20px]">download</span>
                Export Excel
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
                <span class="material-symbols-outlined text-[20px]">print</span>
                Cetak / Simpan PDF
            </button>
        </div>
    </section>

    <form method="GET" action="{{ route('admin.recap') }}" class="print-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-[1fr_1fr_auto] md:items-end">
            <label class="block">
                <span class="text-sm font-bold text-slate-700">Dari tanggal</span>
                <input type="date" name="from" value="{{ $from?->toDateString() }}" class="mt-2 h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
            </label>
            <label class="block">
                <span class="text-sm font-bold text-slate-700">Sampai tanggal</span>
                <input type="date" name="to" value="{{ $to?->toDateString() }}" class="mt-2 h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
            </label>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-slate-900 px-5 text-sm font-bold text-white hover:bg-slate-800">
                    <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                    Terapkan
                </button>
                <a href="{{ route('admin.recap') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-300 px-4 text-sm font-bold text-slate-600 hover:border-indigo-500 hover:text-indigo-600">Reset</a>
            </div>
        </div>
        @error('to')
            <p class="mt-3 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </form>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="print-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Total Pendaftaran</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['totalEnrollments'] ?? 0 }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">{{ $stats['uniqueStudents'] ?? 0 }} siswa unik</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                    <span class="material-symbols-outlined">how_to_reg</span>
                </div>
            </div>
        </article>

        <article class="print-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Pembayaran Diterima</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['acceptedPayments'] ?? 0 }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">Dari siswa pada periode ini</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <span class="material-symbols-outlined">verified</span>
                </div>
            </div>
        </article>

        <article class="print-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Placement Selesai</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['placementCompleted'] ?? 0 }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">Rata-rata nilai {{ $stats['averagePlacementScore'] ?? 0 }}%</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                    <span class="material-symbols-outlined">quiz</span>
                </div>
            </div>
        </article>

        <article class="print-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Siswa Terjadwal</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['scheduledStudents'] ?? 0 }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">{{ $stats['classSessions'] ?? 0 }} sesi kelas</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <span class="material-symbols-outlined">calendar_month</span>
                </div>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <article class="print-card rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">Program Aktif</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-950">{{ $stats['activePrograms'] ?? 0 }}</p>
        </article>
        <article class="print-card rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">Tutor Aktif</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-950">{{ $stats['activeTutors'] ?? 0 }}</p>
        </article>
        <article class="print-card rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">Ruang Aktif</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-950">{{ $stats['activeRooms'] ?? 0 }}</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <article class="print-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Status Pendaftaran</h3>
            <p class="mt-1 text-sm text-slate-500">Semua pengajuan program pada periode.</p>
            <div class="mt-5 space-y-3">
                @foreach($enrollmentStatusLabels as $status => $label)
                    <div class="flex items-center justify-between rounded-xl border border-slate-100 px-4 py-3">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $enrollmentStatusStyles[$status] }}">{{ $label }}</span>
                        <span class="text-lg font-extrabold text-slate-950">{{ $enrollmentStatusCounts->get($status, 0) }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="print-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Status Pembayaran</h3>
            <p class="mt-1 text-sm text-slate-500">Status terbaru setiap siswa unik.</p>
            <div class="mt-5 space-y-3">
                @foreach($paymentStatusLabels as $status => $label)
                    <div class="flex items-center justify-between rounded-xl border border-slate-100 px-4 py-3">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $paymentStatusStyles[$status] }}">{{ $label }}</span>
                        <span class="text-lg font-extrabold text-slate-950">{{ $paymentStatusCounts->get($status, 0) }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="print-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Level Placement Test</h3>
            <p class="mt-1 text-sm text-slate-500">Distribusi hasil placement pada periode.</p>
            <div class="mt-5 space-y-3">
                @forelse($placementLevelCounts as $level => $count)
                    <div class="flex items-center justify-between rounded-xl border border-slate-100 px-4 py-3">
                        <span class="text-sm font-bold text-slate-700">{{ $level ?: 'Belum ditentukan' }}</span>
                        <span class="text-lg font-extrabold text-indigo-700">{{ $count }}</span>
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 px-4 py-5 text-sm font-semibold text-slate-500">Belum ada hasil placement test pada periode ini.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="print-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <h3 class="text-lg font-bold text-slate-950">Rekap per Program</h3>
            <p class="mt-1 text-sm text-slate-500">Perbandingan alur siswa dari pendaftaran sampai penjadwalan.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Program</th>
                        <th class="px-4 py-4">Nama Siswa</th>
                        <th class="px-4 py-4 text-center">Pendaftaran</th>
                        <th class="px-4 py-4 text-center">Siswa</th>
                        <th class="px-4 py-4 text-center">Pembayaran</th>
                        <th class="px-4 py-4 text-center">Placement</th>
                        <th class="px-4 py-4 text-center">Terjadwal</th>
                        <th class="px-6 py-4 text-center">Sesi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($programSummaries as $program)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-950">{{ $program['name'] }}</td>
                            <td class="px-4 py-4">
                                @if($program['student_names']->isNotEmpty())
                                    <div class="flex min-w-48 flex-wrap gap-1.5">
                                        @foreach($program['student_names'] as $studentName)
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $studentName }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-700">{{ $program['enrollments'] }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-700">{{ $program['students'] }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-emerald-700">{{ $program['payments_accepted'] }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-violet-700">{{ $program['placement_completed'] }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-sky-700">{{ $program['scheduled_students'] }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-700">{{ $program['sessions'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm font-semibold text-slate-500">Belum ada program tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="print-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <h3 class="text-lg font-bold text-slate-950">Pendaftaran Terbaru</h3>
            <p class="mt-1 text-sm text-slate-500">Sepuluh pengajuan program terbaru pada periode laporan.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-4 py-4">Siswa</th>
                        <th class="px-4 py-4">Program</th>
                        <th class="px-4 py-4">Pendaftaran</th>
                        <th class="px-6 py-4">Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($enrollments->take(10) as $enrollment)
                        @php
                            $paymentStatus = $enrollment->user?->payment_status ?: 'belum_upload';
                            $enrollmentStatus = $enrollment->status ?: 'pending';
                            $registeredAt = $enrollment->enrolled_at ?? $enrollment->created_at;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-slate-600">{{ $registeredAt?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-slate-950">{{ $enrollment->user?->name ?? 'User terhapus' }}</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">{{ $enrollment->user?->email ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-4 font-semibold text-slate-700">{{ $enrollment->program?->name ?? 'Program terhapus' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $enrollmentStatusStyles[$enrollmentStatus] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $enrollmentStatusLabels[$enrollmentStatus] ?? ucfirst($enrollmentStatus) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $paymentStatusStyles[$paymentStatus] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $paymentStatusLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm font-semibold text-slate-500">Tidak ada pendaftaran pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
