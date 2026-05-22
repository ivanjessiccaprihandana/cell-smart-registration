@extends('layouts.app')

@section('content')
<style>
    html { scroll-behavior: smooth; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .glass-card { background: rgba(255,255,255,0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(229,231,235,0.5); }
    section[id] { scroll-margin-top: 5rem; }
    .section-reveal { opacity: 0; transform: translateY(24px); transition: opacity 700ms ease, transform 700ms ease; }
    .section-reveal.is-visible { opacity: 1; transform: translateY(0); }
    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        .section-reveal { opacity: 1; transform: none; transition: none; }
    }
</style>

<main>
    <!-- Hero -->
    <section id="home" class="section-reveal relative pt-20 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 md:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="z-10">
                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full mb-6">Premium Academic Experience</span>
                <h1 class="text-5xl md:text-6xl font-bold text-slate-900 mb-6 leading-tight">Raih Masa Depan Akademikmu Bersama <span class="text-indigo-600">EduPremium</span></h1>
                <p class="text-lg text-slate-600 mb-8 max-w-xl">Akses bimbingan belajar eksklusif dengan kurikulum terstandarisasi dan pengajar ahli. Mulai perjalanan suksesmu hari ini dengan platform belajar yang didesain untuk kenyamanan dan prestasi maksimal.</p>

                <div class="flex gap-4 flex-wrap">
                    <a href="#" class="inline-flex items-center justify-center rounded-lg px-8 py-3 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow">Daftar Sekarang</a>
                    <button class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-8 py-3 text-sm font-semibold text-slate-900 hover:border-indigo-600 hover:text-indigo-600">
                        <span class="material-symbols-outlined mr-2">play_circle</span>Lihat Demo
                    </button>
                </div>
            </div>

            <div class="relative">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl z-10 border border-white/10">
                    <img src="{{ asset('images/student-learning.svg') }}" alt="Student studying" class="w-full h-auto object-cover">
                </div>
                <div class="absolute -top-8 -right-8 w-32 h-32 bg-indigo-100 rounded-full blur-3xl opacity-40"></div>
                <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-30"></div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section id="stats" class="section-reveal py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 md:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-indigo-600">10,000+</div>
                    <div class="text-sm text-slate-600">Siswa Aktif</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-indigo-600">500+</div>
                    <div class="text-sm text-slate-600">Expert Tutors</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-indigo-600">150+</div>
                    <div class="text-sm text-slate-600">Modul Interaktif</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-indigo-600">98%</div>
                    <div class="text-sm text-slate-600">Tingkat Kelulusan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs -->
    <section id="programs" class="section-reveal py-16">
        <div class="max-w-7xl mx-auto px-6 md:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Program Unggulan Kami</h2>
                    <p class="text-slate-600">Pilih jalur belajar yang paling sesuai dengan target akademikmu.</p>
                </div>
                <button class="text-indigo-600 font-semibold flex items-center gap-2">Lihat Semua Program <span class="material-symbols-outlined">arrow_forward</span></button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-lg transition">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-600"><span class="material-symbols-outlined">translate</span></div>
                        <h3 class="text-xl font-bold">Kursus Bahasa Inggris</h3>
                    </div>
                    <p class="text-slate-600 mb-6">Kuasai bahasa global dengan metode komunikatif. Tersedia kelas General English, TOEFL/IELTS dan Business English.</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 flex items-center gap-2"><span class="material-symbols-outlined">schedule</span>12 Sesi / Bulan</span>
                        <button class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg">Pelajari Lebih Lanjut</button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-lg transition">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><span class="material-symbols-outlined">school</span></div>
                        <h3 class="text-xl font-bold">Bimbingan Belajar (Bimbel)</h3>
                    </div>
                    <p class="text-slate-600 mb-6">Pendalaman materi sekolah untuk SMP &amp; SMA. Fokus pada persiapan ujian dan seleksi masuk perguruan tinggi.</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 flex items-center gap-2"><span class="material-symbols-outlined">groups</span>Maksimal 5 Siswa/Kelas</span>
                        <button class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg">Pelajari Lebih Lanjut</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tutors -->
    <section id="tutors" class="section-reveal py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 md:px-8">
            <div class="max-w-2xl mb-10">
                <h2 class="text-3xl font-bold text-slate-900">Tutor Berpengalaman</h2>
                <p class="text-slate-600">Belajar bersama pengajar pilihan yang memahami kebutuhan akademik, ujian, dan pengembangan bahasa.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="h-14 w-14 flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 mb-5">
                        <span class="material-symbols-outlined">psychology</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Metode Personal</h3>
                    <p class="text-sm text-slate-600">Tutor menyesuaikan ritme belajar, latihan, dan evaluasi sesuai target setiap siswa.</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="h-14 w-14 flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 mb-5">
                        <span class="material-symbols-outlined">verified</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Tersertifikasi</h3>
                    <p class="text-sm text-slate-600">Pengajar melewati seleksi kurikulum, micro-teaching, dan evaluasi kualitas berkala.</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="h-14 w-14 flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 mb-5">
                        <span class="material-symbols-outlined">support_agent</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Pendampingan Aktif</h3>
                    <p class="text-sm text-slate-600">Progress siswa dipantau melalui laporan rutin dan rekomendasi latihan lanjutan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="section-reveal py-16">
        <div class="max-w-7xl mx-auto px-6 md:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <h2 class="text-3xl font-bold text-slate-900">Paket Belajar Fleksibel</h2>
                <p class="text-slate-600">Pilih paket sesuai kebutuhan belajar, dari kelas reguler hingga pendampingan intensif.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
                    <h3 class="text-xl font-bold mb-2">Basic</h3>
                    <p class="text-sm text-slate-600 mb-6">Untuk mulai membangun dasar belajar yang konsisten.</p>
                    <div class="text-3xl font-bold text-slate-900 mb-6">Rp399K<span class="text-sm font-medium text-slate-500">/bulan</span></div>
                    <ul class="space-y-3 text-sm text-slate-600 mb-8">
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>8 sesi belajar</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>Modul digital</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>Evaluasi bulanan</li>
                    </ul>
                    <a href="#" class="inline-flex w-full items-center justify-center rounded-lg border border-indigo-600 px-5 py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">Pilih Paket</a>
                </div>

                <div class="bg-indigo-600 rounded-2xl p-8 border border-indigo-600 shadow-lg text-white">
                    <div class="inline-block px-3 py-1 bg-white/15 text-xs font-semibold rounded-full mb-4">Paling Populer</div>
                    <h3 class="text-xl font-bold mb-2">Premium</h3>
                    <p class="text-sm text-indigo-100 mb-6">Untuk target nilai, ujian, dan kemampuan bahasa lebih cepat.</p>
                    <div class="text-3xl font-bold mb-6">Rp699K<span class="text-sm font-medium text-indigo-100">/bulan</span></div>
                    <ul class="space-y-3 text-sm text-indigo-50 mb-8">
                        <li class="flex gap-2"><span class="material-symbols-outlined">check_circle</span>12 sesi belajar</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined">check_circle</span>Kelas kecil intensif</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined">check_circle</span>Konsultasi progress</li>
                    </ul>
                    <a href="#" class="inline-flex w-full items-center justify-center rounded-lg bg-white px-5 py-3 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">Pilih Paket</a>
                </div>

                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
                    <h3 class="text-xl font-bold mb-2">Intensive</h3>
                    <p class="text-sm text-slate-600 mb-6">Untuk persiapan ujian masuk, TOEFL, IELTS, atau target khusus.</p>
                    <div class="text-3xl font-bold text-slate-900 mb-6">Rp999K<span class="text-sm font-medium text-slate-500">/bulan</span></div>
                    <ul class="space-y-3 text-sm text-slate-600 mb-8">
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>16 sesi belajar</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>Tryout dan pembahasan</li>
                        <li class="flex gap-2"><span class="material-symbols-outlined text-indigo-600">check_circle</span>Mentoring personal</li>
                    </ul>
                    <a href="#" class="inline-flex w-full items-center justify-center rounded-lg border border-indigo-600 px-5 py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">Pilih Paket</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="section-reveal py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 md:px-8 text-center mb-10">
            <h2 class="text-3xl font-bold text-slate-900 mb-2">Kisah Sukses Siswa</h2>
            <p class="text-slate-600">Ribuan siswa telah mencapai target mereka bersama kami.</p>
        </div>
        <div class="max-w-4xl mx-auto px-6 md:px-8">
            <div class="glass-card rounded-3xl p-10 shadow relative">
                <span class="material-symbols-outlined absolute top-6 right-8 text-6xl text-indigo-100 select-none" style="font-variation-settings: 'FILL' 1;">format_quote</span>
                <div class="flex flex-col items-center text-center">
                    <img src="https://via.placeholder.com/80" alt="Student testimonial" class="w-20 h-20 rounded-full object-cover mb-6 border-2 border-indigo-600" />
                    <p class="italic text-lg text-slate-600 mb-6">"Belajar di EduPremium mengubah cara saya memandang matematika. Tutornya sangat sabar dan materinya mudah dicerna. Berkat persiapan intensif di sini, saya berhasil diterima di universitas impian saya."</p>
                    <h4 class="font-bold">Amanda Putri</h4>
                    <p class="text-sm text-slate-600">Mahasiswi Teknik UI</p>
                </div>
            </div>
            <div class="flex justify-center gap-4 mt-6">
                <button class="w-3 h-3 rounded-full bg-indigo-600"></button>
                <button class="w-3 h-3 rounded-full bg-slate-300"></button>
                <button class="w-3 h-3 rounded-full bg-slate-300"></button>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section id="contact" class="section-reveal py-16">
        <div class="max-w-7xl mx-auto px-6 md:px-8">
            <div class="bg-indigo-600 rounded-3xl p-12 text-center text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold mb-4">Siap Memulai Perjalanan Prestasimu?</h2>
                    <p class="text-lg text-indigo-100 mb-6 max-w-2xl mx-auto">Dapatkan akses gratis ke materi percobaan dan konsultasi kurikulum dengan ahli pendidikan kami sekarang juga.</p>
                    <a href="#" class="inline-flex items-center justify-center px-8 py-3 bg-white text-indigo-700 rounded-lg font-semibold">Hubungi Konsultan Kami</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 md:px-8 py-16">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-4">
                <div>
                    <h3 class="text-2xl font-bold text-indigo-600 mb-6">EduPremium</h3>
                    <p class="text-base leading-7 text-slate-700 max-w-xs">Platform belajar modern yang menggabungkan kualitas akademik tinggi dengan teknologi pembelajaran terkini.</p>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-5">Layanan</h4>
                    <ul class="space-y-3 text-sm font-medium text-slate-700">
                        <li><a href="#programs" class="hover:text-indigo-600 transition-colors">Program Bimbel</a></li>
                        <li><a href="#pricing" class="hover:text-indigo-600 transition-colors">Persiapan Ujian</a></li>
                        <li><a href="#programs" class="hover:text-indigo-600 transition-colors">Kursus Bahasa</a></li>
                        <li><a href="#tutors" class="hover:text-indigo-600 transition-colors">Privat 1-on-1</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-5">Perusahaan</h4>
                    <ul class="space-y-3 text-sm font-medium text-slate-700">
                        <li><a href="#home" class="hover:text-indigo-600 transition-colors">Tentang Kami</a></li>
                        <li><a href="#contact" class="hover:text-indigo-600 transition-colors">Hubungi Kami</a></li>
                        <li><a href="#tutors" class="hover:text-indigo-600 transition-colors">Karir</a></li>
                        <li><a href="#contact" class="hover:text-indigo-600 transition-colors">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-5">Kontak</h4>
                    <ul class="space-y-3 text-sm font-medium text-slate-700">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">mail</span>
                            support@edupremium.id
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">call</span>
                            +62 21 5555 1234
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">location_on</span>
                            Jakarta Selatan, Indonesia
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-6 md:px-8 py-8 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <p class="text-xs font-semibold text-slate-700">© 2024 EduPremium. Empowering academic excellence.</p>

                <div class="flex items-center gap-6 text-slate-700">
                    <a href="#" aria-label="Instagram" class="hover:text-indigo-600 transition-colors">
                        <span class="material-symbols-outlined">photo_camera</span>
                    </a>
                    <a href="#" aria-label="Website" class="hover:text-indigo-600 transition-colors">
                        <span class="material-symbols-outlined">public</span>
                    </a>
                    <a href="#" aria-label="Email" class="hover:text-indigo-600 transition-colors">
                        <span class="material-symbols-outlined">alternate_email</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sections = document.querySelectorAll('.section-reveal');

        const revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        sections.forEach(function (section) {
            revealObserver.observe(section);
        });

        document.querySelectorAll('a[href^="#"]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                const target = document.querySelector(link.getAttribute('href'));

                if (!target) {
                    return;
                }

                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });

                const menu = link.closest('details');
                if (menu) {
                    menu.removeAttribute('open');
                }
            });
        });
    });
</script>

@endsection
