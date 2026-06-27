<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice {{ $invoiceNumber }}</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <style>
        @media print {
            @page { margin: 14mm; size: A4; }
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .invoice-sheet { box-shadow: none !important; border: 0 !important; margin: 0 !important; max-width: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-950 antialiased">
@php
    $amount = $program->priceForClassType($auth->class_type);
    $amountText = $amount !== null ? 'Rp ' . number_format($amount, 0, ',', '.') : 'Menunggu konfirmasi admin';
    $classType = trim(($auth->class_type ?: '-') . ($auth->private_package ? ' - ' . $auth->private_package : ''));
    $periodText = $latestEnrollment
        ? $latestEnrollment->start_date?->format('d M Y') . ' - ' . $latestEnrollment->end_date?->format('d M Y')
        : '-';
@endphp

<main class="px-5 py-8">
    <div class="no-print mx-auto mb-5 flex max-w-4xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('student.status') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
        <button type="button" onclick="window.print()" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[18px]">print</span>
            Cetak / Simpan PDF
        </button>
    </div>

    <section class="invoice-sheet mx-auto max-w-4xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
        <div class="border-b border-slate-200 bg-slate-950 px-8 py-8 text-white">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-indigo-200">CELL English Course</p>
                    <h1 class="mt-2 text-3xl font-extrabold">Invoice Pembayaran</h1>
                    <p class="mt-2 text-sm font-semibold text-slate-300">CELL FUN N EASY ENGLISH</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">No. Invoice</p>
                    <p class="mt-1 text-lg font-extrabold">{{ $invoiceNumber }}</p>
                    <span class="mt-3 inline-flex rounded-full bg-emerald-500 px-4 py-1.5 text-xs font-extrabold text-white">LUNAS</span>
                </div>
            </div>
        </div>

        <div class="grid gap-6 p-8 md:grid-cols-2">
            <article class="rounded-2xl border border-slate-200 p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Ditagihkan Kepada</p>
                <h2 class="mt-3 text-xl font-extrabold text-slate-950">{{ $auth->name }}</h2>
                <div class="mt-4 space-y-2 text-sm font-semibold text-slate-600">
                    <p>{{ $auth->email }}</p>
                    <p>WhatsApp: {{ $auth->whatsapp ?: '-' }}</p>
                    <p>Alamat: {{ $auth->address ?: '-' }}</p>
                </div>
            </article>

            <article class="rounded-2xl border border-slate-200 p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Detail Pembayaran</p>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="font-semibold text-slate-500">Tanggal Verifikasi</span>
                        <span class="font-bold text-slate-950">{{ $paidAt->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="font-semibold text-slate-500">Metode</span>
                        <span class="font-bold text-slate-950">Bank Transfer</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="font-semibold text-slate-500">Status</span>
                        <span class="font-bold text-emerald-700">Diterima Admin</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="font-semibold text-slate-500">Periode</span>
                        <span class="font-bold text-slate-950">{{ $periodText }}</span>
                    </div>
                </div>
            </article>
        </div>

        <div class="px-8 pb-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Deskripsi</th>
                            <th class="px-5 py-4">Jenis Kelas</th>
                            <th class="px-5 py-4 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-5 py-5">
                                <p class="font-extrabold text-slate-950">{{ $program->name }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ $program->category ?: 'Program CELL English Course' }}</p>
                            </td>
                            <td class="px-5 py-5 font-semibold text-slate-700">{{ $classType }}</td>
                            <td class="px-5 py-5 text-right font-extrabold text-slate-950">{{ $amountText }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="2" class="px-5 py-4 text-right text-sm font-bold text-slate-600">Total Dibayar</td>
                            <td class="px-5 py-4 text-right text-xl font-extrabold text-indigo-700">{{ $amountText }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-[1fr_260px] md:items-end">
                <div class="rounded-2xl bg-slate-50 p-5 text-sm font-semibold leading-6 text-slate-600">
                    Invoice ini diterbitkan otomatis oleh sistem setelah admin menyetujui pembayaran. Simpan invoice ini sebagai bukti pembayaran program.
                </div>
                <div class="text-center">
                    <div class="border-b border-slate-300 pb-16"></div>
                    <p class="mt-3 text-sm font-extrabold text-slate-950">Admin CELL English Course</p>
                </div>
            </div>
        </div>
    </section>
</main>
</body>
</html>
