@extends('layouts.admin')

@php
    $pageTitle = 'Pembayaran';
    $statusStyles = [
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
            <p class="text-sm font-semibold text-indigo-600">Verifikasi Pembayaran</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Bukti Pembayaran Siswa</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Lihat bukti transfer siswa, lalu ubah status menjadi diterima atau ditolak.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            Dashboard
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-bold {{ !$selectedStatus ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua
            </a>
            @foreach($paymentStatuses as $status => $label)
                <a href="{{ route('admin.payments.index', ['status' => $status]) }}" class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-bold {{ $selectedStatus === $status ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Daftar Pembayaran</h3>
                    <p class="text-sm text-slate-500">Total {{ $users->total() }} pendaftar ditemukan.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Program</th>
                        <th class="px-6 py-4">Bukti</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Update</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        @php($status = $user->payment_status ?: 'belum_upload')
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-950">{{ $user->name }}</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">{{ $user->email }}</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">{{ $user->whatsapp ?: '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $programLabels[(string) $user->program] ?? $user->program }}</p>
                                @if($user->class_type)
                                    <p class="mt-1 inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $user->class_type }}</p>
                                @endif
                                <p class="mt-1 max-w-xs truncate text-xs text-slate-500">{{ $user->address ?: '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->payment_proof_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($user->payment_proof_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-indigo-200 px-3 py-2 text-xs font-bold text-indigo-700 hover:border-indigo-600 hover:bg-indigo-50">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Lihat Bukti
                                    </a>
                                    <p class="mt-2 max-w-[12rem] truncate text-xs text-slate-400">{{ basename($user->payment_proof_path) }}</p>
                                @else
                                    <span class="inline-flex rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-500">Belum upload</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusStyles[$status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $paymentStatuses[$status] ?? $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $user->updated_at?->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.payments.update', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status" value="diterima">
                                        <button type="submit" class="inline-flex h-9 items-center justify-center gap-1 rounded-lg bg-emerald-600 px-3 text-xs font-bold text-white hover:bg-emerald-700" @disabled(!$user->payment_proof_path)>
                                            <span class="material-symbols-outlined text-[16px]">check</span>
                                            Terima
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.payments.update', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status" value="ditolak">
                                        <button type="submit" class="inline-flex h-9 items-center justify-center gap-1 rounded-lg border border-rose-200 px-3 text-xs font-bold text-rose-700 hover:border-rose-500 hover:bg-rose-50" @disabled(!$user->payment_proof_path)>
                                            <span class="material-symbols-outlined text-[16px]">close</span>
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                    <span class="material-symbols-outlined">payments</span>
                                </div>
                                <p class="mt-4 font-bold text-slate-900">Belum ada data pembayaran</p>
                                <p class="mt-1 text-sm text-slate-500">Data akan muncul setelah siswa memilih program.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
