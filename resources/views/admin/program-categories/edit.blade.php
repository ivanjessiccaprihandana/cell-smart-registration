@extends('layouts.admin')

@php
    $pageTitle = 'Edit Kategori';
@endphp

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Kategori Tampilan</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Edit Kategori</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Perbarui kategori {{ $category->name }}.</p>
        </div>
        <a href="{{ route('admin.program-categories.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Kembali
        </a>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
        <form method="POST" action="{{ route('admin.program-categories.update', $category) }}">
            @method('PUT')
            @include('admin.program-categories._form')
        </form>
    </section>
</div>
@endsection
