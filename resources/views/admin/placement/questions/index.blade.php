@extends('layouts.admin')

@php($pageTitle = 'Kelola Soal')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Placement Test</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Kelola Soal</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Tambah, edit, aktifkan, atau nonaktifkan soal placement test.</p>
        </div>
        <a href="{{ route('admin.placement.questions.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Soal
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Soal</th>
                        <th class="px-6 py-4">Level</th>
                        <th class="px-6 py-4">Jawaban</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($questions as $question)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-950">{{ $question->question_text }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ $question->sort_order }} - {{ $question->section }}</p>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $question->level }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-lg bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700">
                                    {{ chr(65 + $question->correct_option) }}. {{ $question->options[$question->correct_option] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $question->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.placement.questions.edit', $question) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-indigo-600 hover:text-indigo-600" aria-label="Edit soal">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.placement.questions.destroy', $question) }}" onsubmit="return confirm('Hapus soal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-red-500 hover:text-red-600" aria-label="Hapus soal">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm font-semibold text-slate-500">Belum ada soal placement test.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($questions->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">{{ $questions->links() }}</div>
        @endif
    </section>
</div>
@endsection
