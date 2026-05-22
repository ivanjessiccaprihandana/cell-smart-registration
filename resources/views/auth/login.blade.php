@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-2xl px-6 py-24">
    <div class="rounded-3xl border border-slate-200 bg-white p-10 shadow-lg shadow-slate-900/5">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-slate-900">Masuk ke Akun Anda</h1>
            <p class="mt-2 text-sm text-slate-500">Gunakan email dan password yang terdaftar.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input id="password" name="password" type="password" required
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-sm text-slate-500">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                    Ingat saya
                </label>
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Belum punya akun?</a>
            </div>

            <button type="submit" class="w-full rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/15 transition hover:bg-indigo-700">
                Masuk
            </button>
        </form>
    </div>
</section>
@endsection
