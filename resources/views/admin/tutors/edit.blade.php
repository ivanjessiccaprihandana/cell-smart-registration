@extends('layouts.admin')

@php($pageTitle = 'Edit Tutor')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <section class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Tutor</p>
            <h2 class="mt-1 text-2xl font-extrabold text-slate-950">Edit Tutor</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $tutor->name }}</p>
        </div>
        <a href="{{ route('admin.tutors.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:border-indigo-600 hover:text-indigo-600">Kembali</a>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.tutors.update', $tutor) }}">
            @method('PUT')
            @include('admin.tutors._form')
        </form>
    </section>
</div>
@endsection
