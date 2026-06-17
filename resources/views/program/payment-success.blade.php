@extends('layouts.app')

@section('content')
@php
    $status = $auth->payment_status ?: 'menunggu_verifikasi';
    $priceText = $program->formattedPriceForClassType($auth->class_type, 'Menunggu konfirmasi admin');
    $orderId = '#CELL-' . now()->format('Y') . str_pad((string) $auth->id, 4, '0', STR_PAD_LEFT);
    $transactionTime = ($auth->updated_at ?? now())->format('d M Y, H:i') . ' WIB';
    $statusLabel = [
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'diterima' => 'Terverifikasi',
        'ditolak' => 'Perlu Upload Ulang',
        'belum_upload' => 'Belum Upload',
    ][$status] ?? 'Menunggu Verifikasi';
    $statusClass = match ($status) {
        'diterima' => 'bg-emerald-50 text-emerald-700',
        'ditolak' => 'bg-rose-50 text-rose-700',
        'belum_upload' => 'bg-slate-100 text-slate-700',
        default => 'bg-amber-50 text-amber-700',
    };
    $heroIcon = match ($status) {
        'diterima' => 'check_circle',
        'ditolak' => 'cancel',
        'belum_upload' => 'upload_file',
        default => 'pending',
    };
    $heroIconClass = match ($status) {
        'diterima' => 'bg-emerald-100 text-emerald-700 shadow-emerald-200/70',
        'ditolak' => 'bg-rose-100 text-rose-700 shadow-rose-200/70',
        'belum_upload' => 'bg-slate-100 text-slate-700 shadow-slate-200/70',
        default => 'bg-amber-100 text-amber-700 shadow-amber-200/70',
    };
    $heroTitle = match ($status) {
        'diterima' => 'Pendaftaran Terverifikasi',
        'ditolak' => 'Pembayaran Perlu Diperbaiki',
        'belum_upload' => 'Bukti Pembayaran Belum Diupload',
        default => 'Pembayaran Berhasil Dikirim',
    };
    $heroDescription = match ($status) {
        'diterima' => 'Selamat. Pembayaran Anda sudah diterima admin dan pendaftaran kelas telah terverifikasi.',
        'ditolak' => 'Bukti pembayaran belum dapat diverifikasi. Silakan upload ulang bukti transfer yang lebih jelas atau hubungi admin.',
        'belum_upload' => 'Silakan upload bukti transfer terlebih dahulu agar admin dapat memverifikasi pendaftaran Anda.',
        default => 'Terima kasih. Bukti pembayaran Anda telah kami terima dan sedang dalam proses verifikasi oleh admin CELL English Course. Estimasi verifikasi maksimal 1x24 jam.',
    };
    $noticeClass = match ($status) {
        'diterima' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'ditolak' => 'border-rose-200 bg-rose-50 text-rose-800',
        'belum_upload' => 'border-slate-200 bg-slate-50 text-slate-700',
        default => 'border-amber-200 bg-amber-50 text-amber-800',
    };
    $noticeIcon = match ($status) {
        'diterima' => 'verified',
        'ditolak' => 'report',
        'belum_upload' => 'upload_file',
        default => 'schedule',
    };
    $noticeText = match ($status) {
        'diterima' => 'Status Anda sudah terverifikasi. Silakan menunggu informasi jadwal kelas dari admin atau hubungi admin jika membutuhkan detail kelas.',
        'ditolak' => 'Status Anda ditolak. Penyebab paling umum: foto bukti kurang jelas, nominal tidak sesuai, atau rekening tujuan belum terlihat.',
        'belum_upload' => 'Anda belum mengirim bukti pembayaran. Upload bukti transfer untuk melanjutkan proses verifikasi.',
        default => 'Status akan berubah otomatis setelah admin menekan tombol Terima atau Tolak di halaman verifikasi pembayaran.',
    };
    $primaryHref = match ($status) {
        'diterima' => route('placement-test'),
        'ditolak', 'belum_upload' => route('programs.payment'),
        default => route('student.status'),
    };
    $primaryLabel = match ($status) {
        'diterima' => 'Lanjut Placement Test',
        'ditolak' => 'Upload Ulang Bukti',
        'belum_upload' => 'Upload Bukti Pembayaran',
        default => 'Lihat Status Saya',
    };
    $primaryIcon = match ($status) {
        'diterima' => 'quiz',
        'ditolak', 'belum_upload' => 'upload_file',
        default => 'assignment_ind',
    };
    $footerStatusText = match ($status) {
        'diterima' => 'Pembayaran sudah terverifikasi',
        'ditolak' => 'Bukti perlu diupload ulang',
        'belum_upload' => 'Menunggu upload bukti',
        default => 'Menunggu verifikasi admin',
    };
    $adminWhatsappUrl = 'https://wa.me/6281292538501?text=' . rawurlencode('Halo admin CELL English Course, saya butuh bantuan terkait pendaftaran/pembayaran.');
@endphp

<main class="min-h-[calc(100vh-4rem)] bg-slate-50">
    <section class="px-6 py-14 md:px-8">
        <div class="mx-auto max-w-3xl">
            <div class="text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full shadow-lg {{ $heroIconClass }}">
                    <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">{{ $heroIcon }}</span>
                </div>
                <h1 class="mt-6 text-3xl font-extrabold tracking-tight text-slate-950 md:text-4xl">
                    {{ $heroTitle }}
                </h1>
                <p class="mx-auto mt-3 max-w-lg text-sm leading-6 text-slate-600">
                    {{ $heroDescription }}
                </p>
            </div>

            @if(session('success'))
                <div class="mx-auto mt-8 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-center text-sm font-semibold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-10 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
                <div class="h-1.5 bg-gradient-to-r from-indigo-600 via-sky-500 to-emerald-500"></div>

                <div class="p-6 md:p-8">
                    <div class="flex flex-col gap-4 border-b border-slate-100 pb-6 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Order ID</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-950">{{ $orderId }}</p>
                        </div>
                        <span class="inline-flex items-center gap-2 self-start rounded-full px-4 py-2 text-xs font-bold {{ $statusClass }}">
                            <span class="material-symbols-outlined text-[16px]">{{ $noticeIcon }}</span>
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <div class="grid gap-2 py-5 sm:grid-cols-[0.8fr_1.2fr] sm:items-center">
                            <p class="text-sm font-semibold text-slate-500">Program Kursus</p>
                            <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $program->name }}</p>
                        </div>
                        @if($auth->class_type)
                            <div class="grid gap-2 py-5 sm:grid-cols-[0.8fr_1.2fr] sm:items-center">
                                <p class="text-sm font-semibold text-slate-500">Jenis Kelas</p>
                                <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $auth->class_type }}</p>
                            </div>
                        @endif
                        <div class="grid gap-2 py-5 sm:grid-cols-[0.8fr_1.2fr] sm:items-center">
                            <p class="text-sm font-semibold text-slate-500">Kategori</p>
                            <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $program->category ?: '-' }}</p>
                        </div>
                        <div class="grid gap-2 py-5 sm:grid-cols-[0.8fr_1.2fr] sm:items-center">
                            <p class="text-sm font-semibold text-slate-500">Metode Pembayaran</p>
                            <p class="text-sm font-bold text-slate-950 sm:text-right">Bank Transfer - BCA</p>
                        </div>
                        <div class="grid gap-2 py-5 sm:grid-cols-[0.8fr_1.2fr] sm:items-center">
                            <p class="text-sm font-semibold text-slate-500">Waktu Upload</p>
                            <p class="text-sm font-bold text-slate-950 sm:text-right">{{ $transactionTime }}</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl border px-4 py-4 text-sm font-semibold leading-6 {{ $noticeClass }}">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[22px]">{{ $noticeIcon }}</span>
                            <p>{{ $noticeText }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-5 md:px-8">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-slate-600">Total Bayar</p>
                        <p class="text-xl font-extrabold text-indigo-700">{{ $priceText }}</p>
                    </div>
                </div>

                <div class="grid gap-3 p-6 md:grid-cols-2 md:p-8">
                    <a href="{{ $primaryHref }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
                        <span class="material-symbols-outlined text-[20px]">{{ $primaryIcon }}</span>
                        {{ $primaryLabel }}
                    </a>
                    <a href="{{ route('student.status') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 transition hover:border-indigo-500 hover:text-indigo-600">
                        <span class="material-symbols-outlined text-[20px]">assignment_ind</span>
                        Lihat Status Saya
                    </a>
                </div>
            </div>

            <div class="mt-8 rounded-xl border border-indigo-100 bg-indigo-50 px-5 py-4 text-sm font-medium text-slate-700">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[20px] text-indigo-600">help</span>
                    <p>
                        Butuh bantuan?
                        <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener" class="font-bold text-emerald-700 hover:text-emerald-900">Hubungi support CELL English Course via WhatsApp</a>
                        agar proses pendaftaran Anda bisa dibantu lebih cepat.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-6 py-10 text-sm md:grid-cols-4 md:px-8">
            <div>
                <h2 class="text-xl font-extrabold text-indigo-600">CELL English Course</h2>
                <p class="mt-3 leading-6 text-slate-500">CELL FUN N EASY ENGLISH.</p>
            </div>
            <div>
                <h3 class="font-bold text-slate-950">Program</h3>
                <div class="mt-3 space-y-2 text-slate-500">
                    <p>Bahasa Inggris</p>
                    <p>BIMBEL TK-SMA</p>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-slate-950">Bantuan</h3>
                <div class="mt-3 space-y-2 text-slate-500">
                    <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 font-semibold text-emerald-700 hover:text-emerald-900">
                        <span>WhatsApp Admin</span>
                    </a>
                    <p>Verifikasi Pembayaran</p>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-slate-950">Status</h3>
                <div class="mt-3 space-y-2 text-slate-500">
                    <p>{{ $footerStatusText }}</p>
                    <p>Estimasi 1x24 jam</p>
                </div>
            </div>
        </div>
    </footer>
</main>
@endsection
