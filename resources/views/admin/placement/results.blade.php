@extends('layouts.admin')

@php($pageTitle = 'Hasil Tes')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Placement Test</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Hasil Tes Siswa</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Lihat skor, level rekomendasi, waktu pengerjaan, dan detail jawaban siswa.</p>
        </div>
        <a href="{{ route('admin.placement.questions.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[20px]">quiz</span>
            Kelola Soal
        </a>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Skor</th>
                        <th class="px-6 py-4">Level</th>
                        <th class="px-6 py-4">Rekomendasi</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attempts as $attempt)
                        <tr class="align-top hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-950">{{ $attempt->user?->name ?? 'User terhapus' }}</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">{{ $attempt->user?->email ?? '-' }}</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">{{ $attempt->created_at->format('d M Y H:i') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xl font-extrabold text-slate-950">{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</p>
                                <p class="mt-1 text-xs font-bold text-indigo-700">{{ $attempt->score_percentage }}%</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">{{ $attempt->level }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $attempt->recommended_program }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($attempt->duration_seconds !== null)
                                    {{ floor($attempt->duration_seconds / 60) }} menit {{ $attempt->duration_seconds % 60 }} detik
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <details class="group">
                                    <summary class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Lihat Jawaban
                                    </summary>
                                    <div class="mt-4 max-h-96 w-[min(70vw,48rem)] overflow-auto rounded-xl border border-slate-200 bg-white p-4 shadow-lg">
                                        <div class="space-y-3">
                                            @foreach($attempt->answers as $index => $answer)
                                                <div class="rounded-lg border {{ $answer['is_correct'] ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }} p-3">
                                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Soal {{ $index + 1 }} - {{ $answer['level'] }}</p>
                                                    <p class="mt-1 text-sm font-bold text-slate-950">{{ $answer['question_text'] }}</p>
                                                    <div class="mt-2 grid gap-2 text-xs font-semibold text-slate-700 md:grid-cols-2">
                                                        <p>
                                                            Jawaban siswa:
                                                            @if(($answer['selected_option'] ?? null) !== null)
                                                                {{ chr(65 + $answer['selected_option']) }}. {{ ($answer['options'] ?? [])[$answer['selected_option']] ?? '-' }}
                                                            @else
                                                                Tidak dijawab
                                                            @endif
                                                        </p>
                                                        <p>
                                                            Jawaban benar:
                                                            {{ chr(65 + $answer['correct_option']) }}. {{ ($answer['options'] ?? [])[$answer['correct_option']] ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                    <span class="material-symbols-outlined">assignment_turned_in</span>
                                </div>
                                <p class="mt-4 font-bold text-slate-900">Belum ada hasil tes</p>
                                <p class="mt-1 text-sm text-slate-500">Data akan muncul setelah siswa mengirim placement test.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">{{ $attempts->links() }}</div>
        @endif
    </section>
</div>
@endsection
