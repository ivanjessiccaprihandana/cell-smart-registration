@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section id="home" class="mx-auto max-w-7xl px-6 py-20 md:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="flex flex-col gap-6">
                <div class="inline-flex items-center gap-2 w-fit">
                    <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">Premium Academic Experience</span>
                </div>
                
                <div>
                    <h1 class="text-5xl md:text-6xl font-bold text-slate-900 leading-tight mb-4">
                        Raih Masa Depan Akademikmu Bersama <span class="text-indigo-600">Cell English Course and Learning.</span>
                    </h1>
                </div>

                <p class="text-lg text-slate-600 leading-relaxed">
                    Akses bimbingan belajar eksklusif dengan kurikulum terstandarisasi dan pengajar ahli. Mulai perjalanan sukses Anda dengan platform belajar yang didisain untuk kesuksesan maksimal.
                </p>

                <div class="flex gap-4 pt-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg px-8 py-3 text-sm font-semibold text-white shadow-lg bg-primary hover:bg-primary-hover transition-all active:scale-95">Daftar Sekarang</a>
                    <button class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-8 py-3 text-sm font-semibold text-slate-900 transition duration-200 ease-out hover:border-indigo-600 hover:text-indigo-600 active:scale-95 gap-2">
                        <span class="material-symbols-outlined text-xl">play_circle</span>
                        Lihat Demo
                    </button>
                </div>
            </div>

            <!-- Right Image -->
            <div class="flex justify-center">
                <div class="relative w-full max-w-md">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 to-blue-500/20 rounded-3xl blur-2xl"></div>
                    <img src="{{ asset('images/student-learning.svg') }}" alt="Student Learning" class="relative w-full h-auto rounded-3xl shadow-2xl object-cover">
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="statistics" class="bg-gradient-to-r from-slate-50 to-slate-100 py-16 md:py-20">
        <div class="mx-auto max-w-7xl px-6 md:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <!-- Stat 1 -->
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-indigo-600 mb-2">10,000+</div>
                    <div class="text-sm md:text-base font-medium text-slate-600">Siswa Aktif</div>
                </div>
                <!-- Stat 2 -->
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-indigo-600 mb-2">500+</div>
                    <div class="text-sm md:text-base font-medium text-slate-600">Expert Tutors</div>
                </div>
                <!-- Stat 3 -->
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-indigo-600 mb-2">150+</div>
                    <div class="text-sm md:text-base font-medium text-slate-600">Modul Intensif</div>
                </div>
                <!-- Stat 4 -->
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-indigo-600 mb-2">98%</div>
                    <div class="text-sm md:text-base font-medium text-slate-600">Tingkat Kepuasan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Programs Section -->
    <section id="programs" class="mx-auto max-w-7xl px-6 py-20 md:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">Program Unggulan Kami</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                Pilih jenis belajar yang sesuai dengan target akademikmu
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Program 1: Kursus Bahasa Inggris -->
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-8 hover:shadow-xl transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100">
                            <span class="material-symbols-outlined text-indigo-600 text-2xl">language</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">Kursus Bahasa Inggris</h3>
                    </div>
                    
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Kuasai bahasa Inggris global dengan kursus komunikatif. Tersedia untuk semua level - dari General English, TOEFL/IELTS Preparation, dan Business English.
                    </p>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <span class="material-symbols-outlined text-indigo-600 text-lg">schedule</span>
                            12 Sesi / Bulan
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <span class="material-symbols-outlined text-indigo-600 text-lg">person</span>
                            Instruktur Bersertifikat
                        </div>
                    </div>

                    <button class="w-full inline-flex items-center justify-center rounded-lg px-6 py-3 text-sm font-semibold text-white shadow-lg bg-primary hover:bg-primary-hover transition-all active:scale-95">
                        Pelajari Lebih Lanjut
                    </button>
                </div>
            </div>

            <!-- Program 2: Bimbingan Belajar -->
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-8 hover:shadow-xl transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100">
                            <span class="material-symbols-outlined text-indigo-600 text-2xl">school</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">Bimbingan Belajar (Bimbel)</h3>
                    </div>
                    
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Persiapan matang untuk SMP & SMA. Kami menyediakan program akademik yang komprehensif untuk semua mata pelajaran dengan target nilai tertinggi.
                    </p>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <span class="material-symbols-outlined text-indigo-600 text-lg">checklist</span>
                            Jk. Maksimum 5 Siswea/Kelas
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <span class="material-symbols-outlined text-indigo-600 text-lg">grade</span>
                            Ujian Simulasi Berkala
                        </div>
                    </div>

                    <button class="w-full inline-flex items-center justify-center rounded-lg px-6 py-3 text-sm font-semibold text-white shadow-lg bg-primary hover:bg-primary-hover transition-all active:scale-95">
                        Pelajari Lebih Lanjut
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="cta" class="mx-auto max-w-7xl px-6 py-16 md:px-8 text-center">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-2xl p-12 md:p-16">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Siap Memulai?</h2>
            <p class="text-lg text-indigo-100 mb-8 max-w-2xl mx-auto">
                Bergabunglah dengan ribuan siswa yang telah merasakan transformasi akademik mereka bersama Cell English Course and Learning.
            </p>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl px-8 py-4 text-base font-semibold text-white shadow-xl bg-primary hover:bg-primary-hover transition duration-200 ease-out active:scale-95">
                Daftar Gratis Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-slate-50 py-12">
        <div class="mx-auto max-w-7xl px-6 md:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="flex flex-col gap-3">
                    <h4 class="text-lg font-bold text-indigo-600">Cell English Course and Learning.</h4>
                    <p class="text-sm text-slate-600">Platform pendidikan terpercaya untuk masa depan cemerlang.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <h4 class="font-semibold text-slate-900">Layanan</h4>
                    <a href="#" class="text-sm text-slate-600 hover:text-indigo-600 transition-colors">Program Bimbel</a>
                    <a href="#" class="text-sm text-slate-600 hover:text-indigo-600 transition-colors">Kursus Bahasa</a>
                    <a href="#" class="text-sm text-slate-600 hover:text-indigo-600 transition-colors">Private Session</a>
                </div>
                <div class="flex flex-col gap-3">
                    <h4 class="font-semibold text-slate-900">Bantuan</h4>
                    <a href="#" class="text-sm text-slate-600 hover:text-indigo-600 transition-colors">Hubungi Kami</a>
                    <a href="#" class="text-sm text-slate-600 hover:text-indigo-600 transition-colors">FAQ</a>
                    <a href="#" class="text-sm text-slate-600 hover:text-indigo-600 transition-colors">Kebijakan Privasi</a>
                </div>
                <div class="flex flex-col gap-3">
                    <h4 class="font-semibold text-slate-900">Ikuti Kami</h4>
                    <div class="flex gap-4">
                        <a href="#" class="text-slate-600 hover:text-indigo-600 transition-colors">
                            <span class="material-symbols-outlined">public</span>
                        </a>
                        <a href="#" class="text-slate-600 hover:text-indigo-600 transition-colors">
                            <span class="material-symbols-outlined">groups</span>
                        </a>
                        <a href="#" class="text-slate-600 hover:text-indigo-600 transition-colors">
                            <span class="material-symbols-outlined">chat</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-200 pt-8 text-center">
                <p class="text-sm text-slate-600">© 2024 EduPremium. All rights reserved. Memberdayakan keunggulan akademik.</p>
            </div>
        </div>
    </footer>
@endsection
