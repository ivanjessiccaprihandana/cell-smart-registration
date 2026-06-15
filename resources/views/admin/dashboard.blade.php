@extends('layouts.admin')

@php
    $pageTitle = 'Dashboard';
    $stats = $stats ?? [];
    $programs = $programs ?? collect();
    $registeredUsers = $registeredUsers ?? collect();
    $recentRegistrants = $recentRegistrants ?? collect();
    $programCounts = $programCounts ?? collect();
    $paymentCounts = $paymentCounts ?? collect();
    $weeklyRegistrants = $weeklyRegistrants ?? collect();
    $maxWeeklyRegistrants = $maxWeeklyRegistrants ?? 1;
    $quotaPercent = ($stats['totalQuota'] ?? 0) > 0
        ? min(100, round((($stats['usedQuota'] ?? 0) / $stats['totalQuota']) * 100))
        : 0;
    $paymentLabels = [
        'belum_upload' => 'Belum Upload',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'diterima' => 'Diterima',
        'ditolak' => 'Ditolak',
    ];
    $paymentStyles = [
        'belum_upload' => 'bg-slate-100 text-slate-700',
        'menunggu_verifikasi' => 'bg-amber-50 text-amber-700',
        'diterima' => 'bg-emerald-50 text-emerald-700',
        'ditolak' => 'bg-rose-50 text-rose-700',
    ];
@endphp

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Ringkasan Admin</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Dashboard Program CELL</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Pantau program aktif, kuota, pendaftar, dan pembayaran dari data aplikasi.</p>
        </div>
        <a href="{{ route('admin.programs.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Program
        </a>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Program Aktif</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['activePrograms'] ?? 0 }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">Total {{ $stats['totalPrograms'] ?? 0 }} program tersimpan</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                    <span class="material-symbols-outlined">school</span>
                </div>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Pendaftar Program</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['totalRegistrants'] ?? 0 }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">{{ $stats['todayRegistrants'] ?? 0 }} pendaftar diperbarui hari ini</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <span class="material-symbols-outlined">group</span>
                </div>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Kuota Terpakai</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['usedQuota'] ?? 0 }} / {{ $stats['totalQuota'] ?? 0 }}</p>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-indigo-600" style="width: {{ $quotaPercent }}%"></div>
                    </div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
            </div>
        </article>

        <a href="{{ route('admin.payments.index', ['status' => 'menunggu_verifikasi']) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Menunggu Pembayaran</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $stats['pendingPayments'] ?? 0 }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">Perlu dicek dari bukti upload siswa</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                    <span class="material-symbols-outlined">payments</span>
                </div>
            </div>
        </a>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-950">Pendaftar 7 Hari Terakhir</h3>
                    <p class="text-sm text-slate-500">Berdasarkan data terakhir pendaftaran program.</p>
                </div>
            </div>
            <div class="mt-6 flex h-64 items-end gap-3">
                @foreach($weeklyRegistrants as $day)
                    @php($height = max(8, round(($day['count'] / $maxWeeklyRegistrants) * 100)))
                    <div class="flex min-w-0 flex-1 flex-col items-center gap-2">
                        <div class="flex h-52 w-full items-end rounded-lg bg-slate-50 px-2">
                            <div class="w-full rounded-t-lg bg-indigo-600" style="height: {{ $height }}%"></div>
                        </div>
                        <p class="text-xs font-bold text-slate-900">{{ $day['count'] }}</p>
                        <p class="text-[11px] font-semibold text-slate-500">{{ $day['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Status Pembayaran</h3>
            <p class="mt-1 text-sm text-slate-500">Ringkasan siswa yang sudah memilih program.</p>
            <div class="mt-5 space-y-3">
                @foreach($paymentLabels as $status => $label)
                    <div class="flex items-center justify-between rounded-xl border border-slate-100 px-4 py-3">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $paymentStyles[$status] }}">{{ $label }}</span>
                        <span class="text-lg font-extrabold text-slate-950">{{ $paymentCounts->get($status, 0) }}</span>
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-950">Kuota per Program</h3>
                    <p class="mt-1 text-sm text-slate-500">Program aktif dan nonaktif tetap terlihat agar mudah dipantau.</p>
                </div>
                <a href="{{ route('admin.programs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">
                    Kelola
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                    <thead class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="py-3 pr-4">Program</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Pendaftar</th>
                            <th class="py-3 pl-4 text-right">Sisa Kuota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($programs as $program)
                            <tr>
                                <td class="py-4 pr-4 font-bold text-slate-950">{{ $program->name }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $program->category ?: '-' }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $program->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($program->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 font-bold text-slate-950">{{ $program->registered_users_count }}</td>
                                <td class="py-4 pl-4 text-right font-bold {{ $program->quota && $program->remaining_quota <= 0 ? 'text-rose-600' : 'text-slate-950' }}">
                                    {{ $program->quota ? $program->remaining_quota : 'Tidak dibatasi' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-sm font-semibold text-slate-500">Belum ada program. Tambahkan program dari menu Program.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Program Terpopuler</h3>
            <p class="mt-1 text-sm text-slate-500">Urutan berdasarkan jumlah pendaftar.</p>
            <div class="mt-5 space-y-3">
                @forelse($programCounts->take(6) as $program)
                    <div class="rounded-xl border border-slate-100 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-bold text-slate-950">{{ $program['name'] }}</p>
                            <p class="text-sm font-extrabold text-indigo-700">{{ $program['count'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 px-4 py-5 text-sm font-semibold text-slate-500">Belum ada pendaftar program.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section id="pendaftar" class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <h3 class="text-lg font-bold text-slate-950">Pendaftar Terbaru</h3>
            <p class="mt-1 text-sm text-slate-500">Data siswa yang sudah memilih program.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Program</th>
                        <th class="px-6 py-4">WhatsApp</th>
                        <th class="px-6 py-4">Pembayaran</th>
                        <th class="px-6 py-4">Bukti</th>
                        <th class="px-6 py-4">Update</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentRegistrants as $user)
                        @php($paymentStatus = $user->payment_status ?: 'belum_upload')
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-950">{{ $user->name }}</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">{{ $user->email }}</p>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $programLabels[(string) $user->program] ?? $user->program }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $user->whatsapp ?: '-' }}</td>
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
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm font-semibold text-slate-500">Belum ada pendaftar program.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
