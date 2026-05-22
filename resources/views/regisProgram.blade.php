@extends('layouts.app')

@section('content')
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>

<main class="min-h-[calc(100vh-4rem)] bg-slate-50 px-6 py-12 md:px-8">
    <div class="mx-auto w-full max-w-2xl">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
            <div class="border-b border-slate-200 bg-slate-50 px-6 pb-6 pt-8 md:px-10">
                <div class="relative mb-4 flex items-start justify-between">
                    <div class="absolute left-6 right-6 top-5 h-[2px] bg-slate-200"></div>
                    <div class="absolute left-6 top-5 h-[2px] w-1/3 bg-indigo-600"></div>

                    <div class="relative z-10 flex flex-col items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white shadow-md">1</div>
                        <span class="text-xs font-semibold text-indigo-600">Data Diri</span>
                    </div>

                    <div class="relative z-10 flex flex-col items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-500">2</div>
                        <span class="text-xs font-semibold text-slate-400">Pilih Program</span>
                    </div>

                    <div class="relative z-10 flex flex-col items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-500">3</div>
                        <span class="text-xs font-semibold text-slate-400">Konfirmasi</span>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-10">
                <div class="mb-8">
                    <span class="mb-4 inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Join Bimbel</span>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Mulai Perjalanan Akademikmu</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Lengkapi data diri untuk membuat akun dan memulai pendaftaran program bimbel Cell EduPremium.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-800">Nama Lengkap</label>
                        <div class="relative">
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 pr-11 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                                placeholder="Masukkan nama sesuai identitas" />
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-[20px] text-indigo-600">check_circle</span>
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-800">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                                placeholder="contoh@email.com" />
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="whatsapp" class="mb-2 block text-sm font-semibold text-slate-800">Nomor WhatsApp</label>
                            <div class="flex">
                                <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-100 px-4 text-sm font-semibold text-slate-600">+62</span>
                                <input id="whatsapp" name="whatsapp" type="tel" value="{{ old('whatsapp') }}" required
                                    class="w-full rounded-r-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="812345678" />
                            </div>
                            @error('whatsapp')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="address" class="mb-2 block text-sm font-semibold text-slate-800">Alamat Lengkap</label>
                        <textarea id="address" name="address" rows="3" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Jl. Akademika No. 42, Jakarta Selatan">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <label for="program" class="mb-2 block text-sm font-semibold text-slate-800">Pilih Program Bimbel</label>
                        <div class="relative">
                            <select id="program" name="program" required
                                class="w-full appearance-none rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                                <option value="" disabled @selected(!old('program'))>Pilih program bimbingan...</option>
                                <option value="english" @selected(old('program') === 'english')>Kursus Inggris (TOEFL/IELTS Prep)</option>
                                <option value="bimbel" @selected(old('program') === 'bimbel')>Bimbel Akademik (UTBK/Mandiri)</option>
                                <option value="private" @selected(old('program') === 'private')>Privat 1-on-1</option>
                            </select>
                            <span class="material-symbols-outlined pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-500">expand_more</span>
                        </div>
                        @error('program')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-800">Password Akun</label>
                            <input id="password" name="password" type="password" required
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                                placeholder="Minimal 8 karakter" />
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-800">Konfirmasi Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                                placeholder="Ulangi password" />
                        </div>
                    </div>

                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 active:scale-[0.98]">
                        Lanjut Join Bimbel
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </button>
                </form>
            </div>

            <div class="border-t border-slate-200 bg-white px-6 py-5 text-center">
                <p class="text-xs font-semibold text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Masuk di sini</a></p>
            </div>
        </div>

        <div class="mt-10 flex flex-wrap justify-center gap-8 text-sm font-semibold text-slate-500">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined">verified_user</span>
                Pembayaran Aman
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined">workspace_premium</span>
                Tutor Bersertifikat
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined">support_agent</span>
                Dukungan 24/7
            </div>
        </div>
    </div>
</main>
@endsection
