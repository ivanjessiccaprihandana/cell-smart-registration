@extends('layouts.admin')

@php($pageTitle = 'Tambah Ruang Kelas')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.class-rooms.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 hover:text-indigo-600" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h2 class="text-xl font-extrabold text-slate-950">Tambah Ruang Kelas</h2>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.class-rooms.store') }}">
            @include('admin.class-rooms._form')
        </form>
    </section>
</div>
@endsection
