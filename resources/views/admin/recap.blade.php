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
    .print-only { display: none; }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }

        aside, header, .print-hidden, .pdf-hidden, .screen-report-header { display: none !important; }
        .print-only { display: block !important; }
        html, body { background: white !important; color: #0f172a !important; font-size: 10px; }
        main { padding: 0 !important; }
        .report-container { max-width: none !important; margin: 0 !important; }
        .report-container > * + * { margin-top: 10px !important; }
        .pdf-header { border-bottom: 2px solid #312e81; padding-bottom: 8px; }
        .pdf-header h1 { font-size: 18px; line-height: 1.2; }
        .pdf-kpis { display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: 6px !important; }
        .pdf-kpi-card { border: 1px solid #cbd5e1 !important; border-radius: 4px !important; padding: 8px 10px !important; box-shadow: none !important; }
        .pdf-kpi-card .pdf-kpi-icon, .pdf-kpi-card .pdf-kpi-note { display: none !important; }
        .pdf-kpi-card p { margin: 0 !important; }
        .pdf-kpi-card p:nth-child(2) { margin-top: 3px !important; font-size: 18px !important; line-height: 1.1 !important; }
        .print-card { break-inside: avoid; border-radius: 4px !important; box-shadow: none !important; }
        .pdf-program-section table, .pdf-registration-section table { width: 100% !important; border-collapse: collapse !important; }
        .pdf-program-section th, .pdf-program-section td,
        .pdf-registration-section th, .pdf-registration-section td { border: 1px solid #cbd5e1 !important; padding: 5px 7px !important; }
        .pdf-program-section thead, .pdf-registration-section thead { background: #e2e8f0 !important; color: #0f172a !important; }
        .pdf-program-section { overflow: visible !important; border: 0 !important; break-inside: auto !important; }
        .pdf-program-section > div:first-child { padding: 0 0 6px !important; border: 0 !important; }
        .pdf-program-section > div:last-child { overflow: visible !important; }
        .pdf-program-section h3 { font-size: 13px !important; }
        .pdf-program-section thead, .pdf-registration-section thead { display: table-header-group; }
        .pdf-program-section tr, .pdf-registration-section tr { break-inside: avoid; }
        .pdf-registration-section { break-inside: auto !important; }
        .pdf-signature { display: grid !important; grid-template-columns: 1fr 220px; gap: 24px; margin-top: 14px !important; break-inside: avoid; }
    }
</style>

<div class="report-container mx-auto max-w-7xl space-y-6">
    <section class="screen-report-header flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
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

    <section class="print-only pdf-header">
        <h1 class="font-extrabold uppercase tracking-wide">Rekap Administrasi CELL</h1>
        <div class="mt-2 flex items-center justify-between gap-6 text-sm">
            <p><span class="font-bold">Periode:</span> {{ $periodLabel }}</p>
            <p><span class="font-bold">Dicetak:</span> {{ now()->format('d-m-Y H:i') }}</p>
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

    <section class="pdf-kpis grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="pdf-kpi-card print-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Total Pendaftaran</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['totalEnrollments'] ?? 0 }}</p>
                    <p class="pdf-kpi-note mt-1 text-xs font-medium text-slate-500">{{ $stats['uniqueStudents'] ?? 0 }} siswa unik</p>
                </div>
                <div class="pdf-kpi-icon flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                    <span class="material-symbols-outlined">how_to_reg</span>
                </div>
            </div>
        </article>

        <article class="pdf-kpi-card print-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Pembayaran Diterima</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['acceptedPayments'] ?? 0 }}</p>
                    <p class="pdf-kpi-note mt-1 text-xs font-medium text-slate-500">Dari siswa pada periode ini</p>
                </div>
                <div class="pdf-kpi-icon flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <span class="material-symbols-outlined">verified</span>
                </div>
            </div>
        </article>

        <article class="pdf-kpi-card print-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Placement Selesai</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['placementCompleted'] ?? 0 }}</p>
                    <p class="pdf-kpi-note mt-1 text-xs font-medium text-slate-500">Rata-rata nilai {{ $stats['averagePlacementScore'] ?? 0 }}%</p>
                </div>
                <div class="pdf-kpi-icon flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                    <span class="material-symbols-outlined">quiz</span>
                </div>
            </div>
        </article>

        <article class="pdf-kpi-card print-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Siswa Terjadwal</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['scheduledStudents'] ?? 0 }}</p>
                    <p class="pdf-kpi-note mt-1 text-xs font-medium text-slate-500">{{ $stats['classSessions'] ?? 0 }} sesi kelas</p>
                </div>
                <div class="pdf-kpi-icon flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <span class="material-symbols-outlined">calendar_month</span>
                </div>
            </div>
        </article>
    </section>

    <section class="pdf-hidden grid grid-cols-1 gap-4 md:grid-cols-3">
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

    <section class="pdf-hidden grid grid-cols-1 gap-6 xl:grid-cols-3">
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

    <section class="pdf-program-section print-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <h3 class="text-lg font-bold text-slate-950">Rekap per Program</h3>
            <p class="pdf-hidden mt-1 text-sm text-slate-500">Ringkasan jumlah siswa dari pendaftaran sampai penjadwalan. Data nama siswa tersedia pada sheet Data Siswa di file Excel.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Program</th>
                        <th class="px-4 py-4 text-center">Pendaftaran</th>
                        <th class="px-4 py-4 text-center">Siswa Unik</th>
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
                            <td class="px-4 py-4 text-center font-semibold text-slate-700">{{ $program['enrollments'] }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-700">{{ $program['students'] }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-emerald-700">{{ $program['payments_accepted'] }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-violet-700">{{ $program['placement_completed'] }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-sky-700">{{ $program['scheduled_students'] }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-700">{{ $program['sessions'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm font-semibold text-slate-500">Belum ada program tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="print-only pdf-registration-section">
        <h2 class="mb-2 text-sm font-extrabold uppercase tracking-wide">Data Pendaftaran</h2>
        <table class="text-left">
            <thead>
                <tr>
                    <th class="text-center">No.</th>
                    <th>Tanggal</th>
                    <th>Nama Siswa</th>
                    <th>Program</th>
                    <th>Status Pendaftaran</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $enrollment)
                    @php
                        $paymentStatus = $enrollment->user?->payment_status ?: 'belum_upload';
                        $enrollmentStatus = $enrollment->status ?: 'pending';
                        $registeredAt = $enrollment->enrolled_at ?? $enrollment->created_at;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="whitespace-nowrap">{{ $registeredAt?->format('d-m-Y') ?? '-' }}</td>
                        <td class="font-semibold">{{ $enrollment->user?->name ?? 'User terhapus' }}</td>
                        <td>{{ $enrollment->program?->name ?? 'Program terhapus' }}</td>
                        <td>{{ $enrollmentStatusLabels[$enrollmentStatus] ?? ucfirst($enrollmentStatus) }}</td>
                        <td>{{ $paymentStatusLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center">Tidak ada pendaftaran pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="pdf-hidden print-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
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

    <section class="print-only pdf-signature">
        <p class="text-xs text-slate-600">Catatan: rincian nama siswa, hasil placement test, dan jadwal kelas tersedia pada file Excel rekap.</p>
        <div class="text-center text-sm">
            <p>Admin CELL,</p>
            <div style="height: 44px"></div>
            <p class="border-t border-slate-400 pt-1">(................................)</p>
        </div>
    </section>
</div>
@endsection
