@extends('layouts.app')

@section('content')
@php
    $paymentStatus = $auth->payment_status ?? 'belum_upload';
    $statusLabel = [
        'belum_upload' => 'Belum upload',
        'menunggu_verifikasi' => 'Menunggu verifikasi',
        'diterima' => 'Pembayaran diterima',
        'ditolak' => 'Perlu upload ulang',
    ][$paymentStatus] ?? $paymentStatus;
    $statusHint = match ($paymentStatus) {
        'diterima' => 'Pembayaran Anda sudah diverifikasi admin.',
        'ditolak' => 'Bukti sebelumnya ditolak. Silakan upload ulang bukti yang lebih jelas.',
        'menunggu_verifikasi' => 'Bukti pembayaran sedang dicek oleh admin.',
        default => 'Silakan upload bukti pembayaran untuk melanjutkan verifikasi.',
    };
    $statusHintClass = match ($paymentStatus) {
        'diterima' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'ditolak' => 'border-rose-200 bg-rose-50 text-rose-700',
        'menunggu_verifikasi' => 'border-amber-200 bg-amber-50 text-amber-700',
        default => 'border-slate-200 bg-slate-50 text-slate-600',
    };
    $proofUrl = $auth->payment_proof_path ? \Illuminate\Support\Facades\Storage::url($auth->payment_proof_path) : null;
    $priceText = $program->formattedPriceForClassType($auth->class_type, 'Hubungi Admin');
    $programPerks = [
        'BIMBEL' => ['Pendampingan tugas sekolah', 'Latihan soal sesuai jenjang', 'Monitoring perkembangan siswa'],
        'Test Preparation' => ['Simulasi tes terarah', 'Strategi pengerjaan soal', 'Pembahasan hasil latihan'],
        'Private' => ['Pendampingan personal', 'Materi sesuai kebutuhan siswa', 'Jadwal dikonsultasikan bersama admin'],
        'Bahasa Inggris' => ['Latihan percakapan aktif', 'Materi sesuai level siswa', 'Tutor berpengalaman'],
    ];
    $selectedPerks = $programPerks[$program->category] ?? ['Kelas terarah', 'Materi mudah diikuti', 'Pendampingan tutor'];
@endphp

<style>
    .payment-dropzone.is-dragging {
        border-color: #4f46e5;
        background: #eef2ff;
        transform: translateY(-2px);
    }
    .program-preview-board {
        background:
            linear-gradient(90deg, rgba(255,255,255,.08) 1px, transparent 1px),
            linear-gradient(0deg, rgba(255,255,255,.08) 1px, transparent 1px),
            linear-gradient(135deg, #0f3b45, #08232d);
        background-size: 18px 18px, 18px 18px, auto;
    }
</style>

<main class="min-h-[calc(100vh-4rem)] bg-slate-50 px-6 py-12 md:px-8">
    <div class="mx-auto w-full max-w-5xl">
        <div class="mb-10 rounded-[2rem] border border-slate-200 bg-white px-6 py-6 shadow-sm md:px-10">
            <div class="relative flex items-start justify-between">
                <div class="absolute left-8 right-8 top-5 h-[2px] bg-indigo-100"></div>
                <div class="absolute left-8 right-8 top-5 h-[2px] bg-indigo-600"></div>

                @foreach([['1', 'Data Diri'], ['2', 'Pilih Program'], ['3', 'Pembayaran']] as [$number, $label])
                    <div class="relative z-10 flex flex-col items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white shadow-md {{ $number === '3' ? 'ring-4 ring-indigo-100' : '' }}">
                            {{ $number }}
                        </div>
                        <span class="text-xs font-bold text-indigo-600">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-10 text-center">
            <span class="inline-flex rounded-full bg-indigo-100 px-4 py-2 text-xs font-bold uppercase tracking-wide text-indigo-700">
                Step 3
            </span>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-950 md:text-4xl">Konfirmasi Pembayaran</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Silakan selesaikan pembayaran. Setelah bukti dikirim, admin akan memverifikasi pembayaran Anda.
            </p>
        </div>

        @if(session('success'))
            <div class="mx-auto mb-6 max-w-3xl rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mx-auto mb-6 max-w-3xl rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1fr_0.72fr]">
            <div class="space-y-6">
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="grid gap-5 md:grid-cols-[0.95fr_1.05fr] md:items-center">
                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-950 p-3 shadow-sm">
                            <div class="program-preview-board relative flex aspect-[4/2.35] items-center justify-center overflow-hidden rounded-lg">
                                <div class="absolute left-4 bottom-4 h-12 w-8 rounded-full border border-emerald-200/60"></div>
                                <div class="absolute right-5 bottom-4 h-14 w-10 rounded-full border border-emerald-200/60"></div>
                                <div class="grid w-3/4 gap-2 text-[8px] font-semibold uppercase tracking-widest text-cyan-100/80">
                                    <div class="h-2 rounded-full bg-cyan-100/50"></div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <span class="h-2 rounded-full bg-cyan-100/25"></span>
                                        <span class="h-2 rounded-full bg-cyan-100/40"></span>
                                        <span class="h-2 rounded-full bg-cyan-100/20"></span>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2">
                                        <span class="h-2 rounded-full bg-cyan-100/35"></span>
                                        <span class="h-2 rounded-full bg-cyan-100/20"></span>
                                        <span class="h-2 rounded-full bg-cyan-100/45"></span>
                                        <span class="h-2 rounded-full bg-cyan-100/25"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">Pilihan Program</p>
                            <h2 class="mt-2 text-2xl font-bold text-slate-950">{{ $program->name }}</h2>
                            @if($auth->class_type)
                                <p class="mt-2 inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">{{ $auth->class_type }}</p>
                            @endif
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $program->description }}</p>

                            <div class="mt-5 space-y-2 border-t border-slate-100 pt-4">
                                @foreach($selectedPerks as $perk)
                                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                        <span class="material-symbols-outlined text-[18px] text-indigo-600">check_circle</span>
                                        {{ $perk }}
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-5 flex items-center justify-between rounded-xl bg-indigo-50 px-4 py-3">
                                <span class="text-xs font-bold text-indigo-500">Total Biaya</span>
                                <span class="text-xl font-extrabold text-indigo-700">{{ $priceText }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-950">Pilih Metode Pembayaran</h2>

                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl border-2 border-indigo-500 bg-indigo-50/40 p-4">
                            <div class="flex items-center justify-between text-sm font-bold text-indigo-700">
                                <span>QRIS</span>
                                <span class="material-symbols-outlined text-[20px]">qr_code_2</span>
                            </div>
                            <div class="mt-4 flex aspect-square items-center justify-center rounded-lg bg-white p-6 shadow-inner">
                                <div class="grid h-36 w-36 grid-cols-5 gap-1 rounded-lg bg-slate-950 p-3">
                                    @foreach(range(1, 25) as $index)
                                        <span class="{{ in_array($index, [1,2,3,6,11,13,15,16,17,21,22,23,25]) ? 'bg-white' : 'bg-slate-950' }} rounded-sm"></span>
                                    @endforeach
                                </div>
                            </div>
                            <p class="mt-4 text-center text-xs font-medium leading-5 text-slate-500">
                                Scan QRIS melalui aplikasi mobile banking atau e-wallet Anda.
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between text-sm font-bold text-slate-700">
                                <span>Transfer Bank</span>
                                <span class="material-symbols-outlined text-[20px] text-slate-400">account_balance</span>
                            </div>

                            <div class="mt-4 space-y-3">
                                <div class="rounded-lg bg-slate-100 p-4">
                                    <p class="text-xs font-semibold text-slate-500">Nama Bank</p>
                                    <p class="mt-1 text-lg font-bold text-slate-950">Bank Central Asia (BCA)</p>
                                </div>
                                <div class="rounded-lg bg-slate-100 p-4">
                                    <p class="text-xs font-semibold text-slate-500">Nomor Rekening</p>
                                    <div class="mt-1 flex items-center justify-between gap-3">
                                        <p id="accountNumber" class="text-lg font-bold text-slate-950">8830 1234 5678</p>
                                        <button type="button" id="copyAccountButton" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm hover:bg-indigo-50" aria-label="Salin nomor rekening">
                                            <span class="material-symbols-outlined text-[20px]">content_copy</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="rounded-lg bg-slate-100 p-4">
                                    <p class="text-xs font-semibold text-slate-500">Atas Nama</p>
                                    <p class="mt-1 text-lg font-bold text-slate-950">CELL English Course</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-950">Unggah Bukti Transfer</h2>

                    <form method="POST" action="{{ route('programs.payment.store') }}" enctype="multipart/form-data" class="mt-5 space-y-5">
                        @csrf

                        <label id="dropzone" for="paymentProof" class="payment-dropzone flex min-h-48 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-indigo-200 bg-slate-50 px-5 py-8 text-center transition">
                            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                <span class="material-symbols-outlined">cloud_upload</span>
                            </span>
                            <span class="mt-4 text-base font-bold text-slate-950">Klik atau seret file ke sini</span>
                            <span class="mt-1 text-xs font-medium leading-5 text-slate-500">Dukung format JPG, PNG, atau PDF. Maksimal 5MB.</span>
                            <input id="paymentProof" name="payment_proof" type="file" accept=".jpg,.jpeg,.png,.pdf" class="sr-only" required>
                        </label>

                        <div id="filePreview" class="{{ $proofUrl ? '' : 'hidden' }} rounded-xl bg-slate-100 p-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white text-indigo-600">
                                    <img id="previewImage" src="{{ $proofUrl && !\Illuminate\Support\Str::endsWith($proofUrl, '.pdf') ? $proofUrl : '' }}" alt="Preview bukti transfer" class="{{ $proofUrl && !\Illuminate\Support\Str::endsWith($proofUrl, '.pdf') ? '' : 'hidden' }} h-full w-full object-cover">
                                    <span id="previewIcon" class="material-symbols-outlined {{ $proofUrl && !\Illuminate\Support\Str::endsWith($proofUrl, '.pdf') ? 'hidden' : '' }}">description</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p id="fileName" class="truncate text-sm font-bold text-slate-950">
                                        {{ $auth->payment_proof_path ? basename($auth->payment_proof_path) : 'Belum ada file dipilih' }}
                                    </p>
                                    <p id="fileMeta" class="mt-1 text-xs font-semibold text-slate-500">
                                        {{ $auth->payment_proof_path ? 'Bukti terakhir berhasil diupload' : 'File siap diupload' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[20px] text-indigo-600">info</span>
                                <div class="text-xs font-medium leading-5 text-indigo-900">
                                    <p class="font-bold">Catatan Penting:</p>
                                    <ul class="mt-1 list-disc pl-4">
                                        <li>Verifikasi pembayaran dilakukan admin maksimal 1x24 jam hari kerja.</li>
                                        <li>Pastikan nominal transfer sesuai arahan admin.</li>
                                        <li>Placement test akan terbuka setelah pembayaran disetujui admin.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="flex h-14 w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 transition hover:bg-indigo-700 active:scale-[0.98]">
                            Upload & Konfirmasi
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </button>
                    </form>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-950">Ringkasan Pendaftaran</h2>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-xl border px-4 py-3 text-sm font-bold {{ $statusHintClass }}">
                            {{ $statusHint }}
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold text-slate-500">Program Dipilih</p>
                            <p class="mt-1 text-xl font-bold text-slate-950">{{ $program->name }}</p>
                            <p class="mt-1 text-sm font-semibold text-indigo-600">{{ $program->category }}</p>
                            @if($auth->class_type)
                                <p class="mt-2 inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">{{ $auth->class_type }}</p>
                            @endif
                        </div>
                        <div class="rounded-xl bg-indigo-50 p-4">
                            <p class="text-xs font-semibold text-indigo-500">Total Biaya</p>
                            <p class="mt-1 text-xl font-extrabold text-indigo-700">{{ $priceText }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold text-slate-500">Nama</p>
                                <p class="mt-1 truncate text-sm font-bold text-slate-950">{{ $auth->name }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold text-slate-500">Status</p>
                                <p class="mt-1 text-sm font-bold text-indigo-600">{{ $statusLabel }}</p>
                            </div>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold text-slate-500">WhatsApp</p>
                            <p class="mt-1 text-sm font-bold text-slate-950">{{ $auth->whatsapp ?: '-' }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-indigo-100 bg-indigo-600 p-6 text-white shadow-xl shadow-indigo-600/20">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/15">
                        <span class="material-symbols-outlined">verified</span>
                    </div>
                    <h2 class="mt-5 text-xl font-bold">Hampir selesai</h2>
                    <p class="mt-2 text-sm leading-6 text-indigo-100">
                        Setelah bukti pembayaran dikirim, admin akan mengecek pembayaran. Placement test akan muncul setelah pembayaran disetujui.
                    </p>
                    <a href="{{ route('programs.index') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-white px-5 py-3 text-sm font-bold text-indigo-700 hover:bg-indigo-50">
                        Ubah Program
                    </a>
                </section>
            </aside>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('paymentProof');
        const dropzone = document.getElementById('dropzone');
        const preview = document.getElementById('filePreview');
        const previewImage = document.getElementById('previewImage');
        const previewIcon = document.getElementById('previewIcon');
        const fileName = document.getElementById('fileName');
        const fileMeta = document.getElementById('fileMeta');
        const copyButton = document.getElementById('copyAccountButton');
        const accountNumber = document.getElementById('accountNumber');

        function showFile(file) {
            if (!file) return;

            preview.classList.remove('hidden');
            fileName.textContent = file.name;
            fileMeta.textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB - siap diupload`;

            if (file.type.startsWith('image/')) {
                previewImage.src = URL.createObjectURL(file);
                previewImage.classList.remove('hidden');
                previewIcon.classList.add('hidden');
            } else {
                previewImage.classList.add('hidden');
                previewIcon.classList.remove('hidden');
            }
        }

        input?.addEventListener('change', function () {
            showFile(input.files[0]);
        });

        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropzone?.addEventListener(eventName, function (event) {
                event.preventDefault();
                dropzone.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            dropzone?.addEventListener(eventName, function (event) {
                event.preventDefault();
                dropzone.classList.remove('is-dragging');
            });
        });

        dropzone?.addEventListener('drop', function (event) {
            const file = event.dataTransfer.files[0];
            if (!file) return;

            input.files = event.dataTransfer.files;
            showFile(file);
        });

        copyButton?.addEventListener('click', async function () {
            await navigator.clipboard.writeText(accountNumber.textContent.trim());
            copyButton.classList.add('bg-emerald-50', 'text-emerald-600');
            setTimeout(function () {
                copyButton.classList.remove('bg-emerald-50', 'text-emerald-600');
            }, 1200);
        });
    });
</script>
@endsection
