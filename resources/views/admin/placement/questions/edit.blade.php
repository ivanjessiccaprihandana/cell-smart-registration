@extends('layouts.admin')

@php($pageTitle = 'Edit Soal')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Placement Test</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Edit Soal</h2>
        </div>
        <a href="{{ route('admin.placement.questions.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Kembali</a>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.placement.questions.update', $question) }}">
            @method('PUT')
            @include('admin.placement.questions._form')
        </form>
    </section>
</div>
@endsection
