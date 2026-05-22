@extends('layouts.auth')

@section('title', 'Register - EduPremium')

@section('content')
<div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

    <!-- Left Side -->
    <div class="hidden lg:flex flex-col gap-6">
        <div class="space-y-3">
            <h1 class="text-4xl font-bold text-on-surface leading-tight">
                Buat Akun Baru
            </h1>
            <p class="text-lg text-on-surface-variant max-w-md">
                Daftar sekarang untuk mulai menggunakan layanan akademik EduPremium dengan mudah dan aman.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-3 mt-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-surface-container-highest flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-xl">person_add</span>
                </div>
                <p class="font-label-md text-on-surface">Pendaftaran Cepat & Mudah</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-surface-container-highest flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-xl">verified_user</span>
                </div>
                <p class="font-label-md text-on-surface">Data Anda Aman</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-surface-container-highest flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-xl">school</span>
                </div>
                <p class="font-label-md text-on-surface">Akses Layanan Pembelajaran</p>
            </div>
        </div>
    </div>

    <!-- Right Side: Register Form -->
    <div class="w-full">
        <div class="bg-surface-container-lowest rounded-3xl registration-card-shadow p-8 md:p-12 border border-outline-variant/30">

            <div class="mb-8 text-center">
                <h2 class="text-2xl md:text-3xl font-bold text-on-surface mb-2">
                    Daftar Akun
                </h2>
                <p class="text-on-surface-variant text-sm">
                    Lengkapi data berikut untuk membuat akun baru.
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div class="space-y-2">
                    <label for="name" class="font-label-md text-on-surface-variant ml-1">
                        Nama Lengkap
                    </label>

                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">
                            person
                        </span>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-outline-variant bg-surface hover:border-outline focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none font-body-md"
                            placeholder="Masukkan nama lengkap"
                        />
                    </div>

                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="font-label-md text-on-surface-variant ml-1">
                        Email
                    </label>

                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">
                            mail
                        </span>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-outline-variant bg-surface hover:border-outline focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none font-body-md"
                            placeholder="nama@email.com"
                        />
                    </div>

                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="font-label-md text-on-surface-variant ml-1">
                        Password
                    </label>

                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">
                            lock
                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-outline-variant bg-surface hover:border-outline focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none font-body-md"
                            placeholder="Minimal 8 karakter"
                        />
                    </div>

                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="font-label-md text-on-surface-variant ml-1">
                        Konfirmasi Password
                    </label>

                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">
                            lock_reset
                        </span>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-outline-variant bg-surface hover:border-outline focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none font-body-md"
                            placeholder="Ulangi password"
                        />
                    </div>
                </div>

                <!-- Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-4 bg-primary text-on-primary font-label-md rounded-xl hover:bg-secondary transition-all duration-200 registration-card-shadow active:scale-[0.98]"
                    >
                        Daftar Sekarang
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="font-body-md text-on-surface-variant">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">
                            Masuk di sini
                        </a>
                    </p>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection