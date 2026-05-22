@extends('layouts.auth')

@section('title', 'Login - EduPremium')

@section('content')
<div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
    <!-- Left Side: Visual/Context -->
    <div class="hidden lg:flex flex-col gap-6">
        <div class="space-y-3">
            <h1 class="text-4xl font-bold text-on-surface leading-tight">Selamat Datang Kembali</h1>
            <p class="text-lg text-on-surface-variant max-w-md">Masukkan kredensial Anda untuk melanjutkan perjalanan akademik Anda bersama EduPremium.</p>
        </div>

        <!-- Feature List -->
        <div class="grid grid-cols-1 gap-3 mt-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-surface-container-highest flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-xl">security</span>
                </div>
                <p class="font-label-md text-on-surface">Akun Anda Aman & Terlindungi</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-surface-container-highest flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-xl">access_time</span>
                </div>
                <p class="font-label-md text-on-surface">Akses Kapan Saja, Di Mana Saja</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-surface-container-highest flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-xl">sync</span>
                </div>
                <p class="font-label-md text-on-surface">Sinkronisasi Semua Perangkat</p>
            </div>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="w-full">
        <div class="bg-surface-container-lowest rounded-3xl registration-card-shadow p-8 md:p-12 border border-outline-variant/30">
            <!-- Mobile Header -->
            <div class="lg:hidden mb-8 text-center">
                <h2 class="text-2xl md:text-3xl font-bold text-on-surface mb-2">Masuk ke Akun</h2>
                <p class="text-on-surface-variant text-sm">Gunakan email dan password Anda.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div class="space-y-2">
                    <label class="font-label-md text-on-surface-variant ml-1" for="email">Email</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">mail</span>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required 
                            autofocus
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-outline-variant bg-surface hover:border-outline focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none font-body-md"
                            placeholder="nama@email.com" 
                        />
                    </div>
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label class="font-label-md text-on-surface-variant ml-1" for="password">Password</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">lock</span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-outline-variant bg-surface hover:border-outline focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none font-body-md"
                            placeholder="••••••••" 
                        />
                    </div>
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary/20"
                    />
                    <label class="ml-2 font-label-md text-on-surface-variant" for="remember">Ingat saya</label>
                </div>

                <div class="pt-2">
                    <button 
                        type="submit"
                        class="w-full py-4 bg-primary text-on-primary font-label-md rounded-xl hover:bg-secondary transition-all duration-200 registration-card-shadow active:scale-[0.98]"
                    >
                        Masuk
                    </button>
                </div>

                <div class="text-center">
                    <p class="font-body-md text-on-surface-variant">
                        Belum punya akun? 
                        <a class="text-primary font-bold hover:underline" href="{{ route('register') }}">Daftar di sini</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
