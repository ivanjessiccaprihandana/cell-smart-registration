@extends('layouts.app')

@section('content')
@php
    $paymentStatus = $auth->payment_status ?: 'belum_upload';
    $paymentLabel = [
        'belum_upload' => 'Belum Upload',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'diterima' => 'Terverifikasi',
        'ditolak' => 'Ditolak',
    ][$paymentStatus] ?? $paymentStatus;

    $paymentStyle = match ($paymentStatus) {
        'diterima' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'verified', 'Pendaftaran Anda sudah terverifikasi. Silakan menunggu informasi jadwal belajar dari admin.'],
        'ditolak' => ['bg-rose-50 text-rose-700 border-rose-200', 'report', 'Bukti pembayaran ditolak. Upload ulang bukti yang lebih jelas atau hubungi admin.'],
        'menunggu_verifikasi' => ['bg-amber-50 text-amber-700 border-amber-200', 'schedule', 'Bukti pembayaran sedang diperiksa oleh admin. Estimasi verifikasi maksimal 1x24 jam.'],
        default => ['bg-slate-50 text-slate-700 border-slate-200', 'upload_file', 'Anda belum mengupload bukti pembayaran. Upload bukti untuk memulai verifikasi.'],
    };

    $priceText = $program ? $program->formattedPriceForClassType($auth->class_type) : '-';
    $classTypeText = trim(($auth->class_type ?: '') . ($auth->private_package ? ' - ' . $auth->private_package : ''));
    $requiresPlacementTest = $requiresPlacementTest ?? true;
    $canTakePlacement = $paymentStatus === 'diterima' && $requiresPlacementTest;
    $paymentDeadline = $paymentStatus === 'belum_upload' && $auth->registration_expires_at
        ? $auth->registration_expires_at->format('d M Y H:i')
        : null;
@endphp

<main class="min-h-[calc(100vh-4rem)] bg-slate-50 px-6 py-10 md:px-8">
    <div class="mx-auto max-w-6xl space-y-8">
        <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-indigo-600">Akun Siswa</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Status Saya</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Pantau status pendaftaran, pembayaran, dan hasil placement test Anda dari satu halaman.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 hover:border-indigo-500 hover:text-indigo-600">
                <span class="material-symbols-outlined text-[18px]">home</span>
                Beranda
            </a>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-500">Status Pembayaran</p>
                        <h2 class="mt-2 text-2xl font-extrabold text-slate-950">{{ $paymentLabel }}</h2>
                    </div>
                    <span class="inline-flex items-center gap-2 self-start rounded-full border px-4 py-2 text-xs font-extrabold {{ $paymentStyle[0] }}">
                        <span class="material-symbols-outlined text-[16px]">{{ $paymentStyle[1] }}</span>
                        {{ $paymentLabel }}
                    </span>
                </div>

                <div class="mt-6 rounded-xl border px-4 py-4 text-sm font-semibold leading-6 {{ $paymentStyle[0] }}">
                    {{ $paymentStyle[2] }}
                    @if($paymentDeadline)
                        <p class="mt-2 font-extrabold">Batas upload bukti: {{ $paymentDeadline }}</p>
                    @endif
                </div>

                <div class="mt-6 divide-y divide-slate-100 rounded-xl border border-slate-100">
                    <div class="grid gap-2 p-4 sm:grid-cols-[0.8fr_1.2fr]">
                        <p class="text-sm font-semibold text-slate-500">Program</p>
                        <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $program->name ?? 'Belum memilih program' }}</p>
                    </div>
                    @if($classTypeText)
                        <div class="grid gap-2 p-4 sm:grid-cols-[0.8fr_1.2fr]">
                            <p class="text-sm font-semibold text-slate-500">Jenis Kelas</p>
                            <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $classTypeText }}</p>
                        </div>
                    @endif
                    <div class="grid gap-2 p-4 sm:grid-cols-[0.8fr_1.2fr]">
                        <p class="text-sm font-semibold text-slate-500">Kategori</p>
                        <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $program->category ?? '-' }}</p>
                    </div>
                    <div class="grid gap-2 p-4 sm:grid-cols-[0.8fr_1.2fr]">
                        <p class="text-sm font-semibold text-slate-500">Total Biaya</p>
                        <p class="text-sm font-extrabold text-indigo-700 sm:text-right">{{ $priceText }}</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    @if(!$program)
                        <a href="{{ route('programs.index') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
                            <span class="material-symbols-outlined text-[18px]">school</span>
                            Pilih Program
                        </a>
                    @elseif(in_array($paymentStatus, ['belum_upload', 'ditolak'], true))
                        <a href="{{ route('programs.payment') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
                            <span class="material-symbols-outlined text-[18px]">upload_file</span>
                            {{ $paymentStatus === 'ditolak' ? 'Upload Ulang Bukti' : 'Upload Bukti' }}
                        </a>
                    @else
                        <a href="{{ route('programs.payment.success') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
                            <span class="material-symbols-outlined text-[18px]">fact_check</span>
                            Lihat Detail Status
                        </a>
                    @endif

                    @if($paymentStatus === 'diterima')
                        <a href="{{ route('programs.invoice') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg border border-emerald-300 bg-emerald-50 px-5 text-sm font-bold text-emerald-700 hover:border-emerald-500 hover:bg-emerald-100">
                            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                            Cetak Invoice
                        </a>
                    @else
                        <a href="{{ route('programs.change') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 hover:border-indigo-500 hover:text-indigo-600">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                            Ubah Program
                        </a>
                    @endif
                </div>

                @if($program)
                    <form method="POST" action="{{ route('programs.renew') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-5 text-sm font-bold text-indigo-700 hover:border-indigo-500 hover:bg-indigo-100">
                            <span class="material-symbols-outlined text-[18px]">autorenew</span>
                            Perpanjang Program
                        </button>
                    </form>
                @endif
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-500">Placement Test</p>
                        <h2 class="mt-2 text-2xl font-extrabold text-slate-950">{{ !$requiresPlacementTest ? 'Tidak Diperlukan' : ($latestPlacementAttempt?->level ?? 'Belum Dikerjakan') }}</h2>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                        <span class="material-symbols-outlined">quiz</span>
                    </div>
                </div>

                @if(!$requiresPlacementTest)
                    <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-5 text-sm font-semibold leading-6 text-emerald-800">
                        Program ini tidak memakai placement test. Setelah pembayaran disetujui, silakan lanjut konsultasi jadwal dengan admin.
                    </div>
                @elseif($latestPlacementAttempt)
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Skor</p>
                            <p class="mt-2 text-xl font-extrabold text-slate-950">{{ $latestPlacementAttempt->correct_answers }}/{{ $latestPlacementAttempt->total_questions }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Nilai</p>
                            <p class="mt-2 text-xl font-extrabold text-slate-950">{{ $latestPlacementAttempt->score_percentage }}%</p>
                        </div>
                        <div class="rounded-xl bg-indigo-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Level</p>
                            <p class="mt-2 text-lg font-extrabold text-indigo-700">{{ $latestPlacementAttempt->level }}</p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Rekomendasi Program</p>
                        <p class="mt-2 text-sm font-bold leading-6 text-slate-800">{{ $latestPlacementAttempt->recommended_program }}</p>
                    </div>
                    <a href="{{ route('student.schedule') }}" class="mt-6 inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
                        <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                        Konsultasi Jadwal
                    </a>
                @else
                    <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm font-semibold leading-6 text-slate-600">
                        @if($canTakePlacement)
                            Anda belum mengerjakan placement test. Kerjakan test untuk mendapatkan rekomendasi level belajar.
                        @else
                            Placement test akan terbuka setelah admin menyetujui bukti pembayaran Anda.
                        @endif
                    </div>
                @endif

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    @if(!$requiresPlacementTest && $paymentStatus === 'diterima')
                        <a href="{{ route('student.schedule') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
                            <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                            Konsultasi Jadwal
                        </a>
                        <span class="inline-flex h-12 items-center justify-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-5 text-sm font-bold text-emerald-700">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            Test Tidak Perlu
                        </span>
                    @elseif($canTakePlacement)
                        <div class="sm:col-span-2">
                            <a href="{{ route('placement-test') }}" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                {{ $latestPlacementAttempt ? 'Lihat Hasil' : 'Mulai Test' }}
                            </a>
                            <div class="mt-3 flex gap-2 rounded-lg bg-slate-50 px-4 py-3 text-xs font-semibold leading-5 text-slate-600">
                                <span class="material-symbols-outlined mt-0.5 text-[16px] text-indigo-600">info</span>
                                <p>Test hanya dapat dikerjakan satu kali. Jika ada kendala teknis, hubungi admin untuk membuka ulang akses test.</p>
                            </div>
                        </div>
                    @else
                        <a href="{{ $paymentStatus === 'menunggu_verifikasi' ? route('programs.payment.success') : route('programs.payment') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
                            <span class="material-symbols-outlined text-[18px]">{{ $paymentStatus === 'menunggu_verifikasi' ? 'schedule' : 'upload_file' }}</span>
                            {{ $paymentStatus === 'menunggu_verifikasi' ? 'Menunggu Verifikasi' : 'Upload Bukti Pembayaran' }}
                        </a>
                        <span class="inline-flex h-12 items-center justify-center gap-2 rounded-lg border border-slate-200 bg-slate-100 px-5 text-sm font-bold text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">lock</span>
                            Test Terkunci
                        </span>
                    @endif
                </div>
            </article>
        </section>
    </div>
</main>
@endsection
