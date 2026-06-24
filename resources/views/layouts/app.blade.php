<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'CELL English Course' }}</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">
    @php
        $adminWhatsappNumber = '6281292538501';
        $adminWhatsappUrl = 'https://wa.me/' . $adminWhatsappNumber . '?text=' . rawurlencode('Halo admin CELL English Course, saya ingin bertanya tentang program dan jadwal belajar.');
        $authProgram = auth()->check() && auth()->user()->program
            ? \App\Models\Program::find(auth()->user()->program)
            : null;
        $requiresPlacementTest = !$authProgram
            || !(
                \Illuminate\Support\Str::lower($authProgram->category ?? '') === 'bimbel'
                || \Illuminate\Support\Str::startsWith(\Illuminate\Support\Str::lower($authProgram->name), 'bimbel')
            );
        $hasPlacementAttempt = auth()->check()
            && \App\Models\PlacementTestAttempt::where('user_id', auth()->id())->exists();
        $canAccessPlacementTest = auth()->check()
            && auth()->user()->payment_status === 'diterima'
            && $requiresPlacementTest
            && !$hasPlacementAttempt;
        $canAccessClassSchedule = auth()->check()
            && auth()->user()->payment_status === 'diterima'
            && (!$requiresPlacementTest || $hasPlacementAttempt);
    @endphp
    <header class="sticky top-0 z-50 w-full border-b border-slate-200/70 bg-white/95 backdrop-blur-sm">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 md:px-8">
            <a href="{{ route('home') }}" class="text-xl font-semibold tracking-tight text-indigo-600">CELL English Course</a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a class="hover:text-indigo-600 transition-colors" href="{{ route('home') }}#home">Beranda</a>
                <a class="hover:text-indigo-600 transition-colors" href="{{ route('home') }}#programs">Program</a>
                <a class="hover:text-indigo-600 transition-colors" href="{{ route('home') }}#tutors">Profile</a>
                <a class="hover:text-indigo-600 transition-colors" href="{{ route('home') }}#pricing">Kelas</a>
                <a class="hover:text-indigo-600 transition-colors" href="{{ route('programs.quota') }}">Cek Kuota</a>
                @auth
                    <a class="hover:text-indigo-600 transition-colors" href="{{ route('student.status') }}">Status Saya</a>
                    @if($canAccessPlacementTest)
                        <a class="hover:text-indigo-600 transition-colors" href="{{ route('placement-test') }}">Placement Test</a>
                    @endif
                    @if($canAccessClassSchedule)
                        <a class="hover:text-indigo-600 transition-colors" href="{{ route('student.schedule') }}">Konsultasi Jadwal</a>
                    @endif
                @endauth
                <a class="hover:text-indigo-600 transition-colors" href="{{ route('home') }}#contact">Kontak</a>
            </nav>

            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="hidden md:inline-flex text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/15 hover:bg-indigo-700 transition">Daftar Sekarang</a>
                @endguest

                @auth
                    <span class="hidden md:inline-flex text-sm text-slate-600">Halo, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hidden md:inline-flex text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Logout</button>
                    </form>
                @endauth
            </div>

            <details class="md:hidden">
                <summary class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm">
                    <span class="material-symbols-outlined">menu</span>
                </summary>
                <div class="mt-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-lg shadow-slate-900/5">
                    <div class="flex flex-col gap-3 text-sm font-medium text-slate-700">
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="{{ route('home') }}#home">Beranda</a>
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="{{ route('home') }}#programs">Program</a>
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="{{ route('home') }}#tutors">Profile</a>
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="{{ route('home') }}#pricing">Kelas</a>
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="{{ route('programs.quota') }}">Cek Kuota</a>
                        @auth
                            <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="{{ route('student.status') }}">Status Saya</a>
                            @if($canAccessPlacementTest)
                                <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="{{ route('placement-test') }}">Placement Test</a>
                            @endif
                            @if($canAccessClassSchedule)
                                <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="{{ route('student.schedule') }}">Konsultasi Jadwal</a>
                            @endif
                        @endauth
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="{{ route('home') }}#contact">Kontak</a>
                        @guest
                            <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="{{ route('login') }}">Login</a>
                            <a class="block rounded-xl bg-indigo-600 px-3 py-2 text-center text-white hover:bg-indigo-700" href="{{ route('register') }}">Daftar Sekarang</a>
                        @endguest
                        @auth
                            <form method="POST" action="{{ route('logout') }}" class="space-y-2">
                                @csrf
                                <button type="submit" class="w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-slate-700 hover:bg-slate-100">Logout</button>
                            </form>
                        @endauth
                    </div>
                </div>
            </details>
        </div>
    </header>

    @yield('content')

    <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener" aria-label="Chat WhatsApp Admin"
        class="fixed bottom-5 right-5 z-50 inline-flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500 text-white shadow-xl shadow-emerald-500/30 transition hover:bg-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-200">
        <svg class="h-7 w-7" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
            <path d="M16.01 3.2A12.71 12.71 0 0 0 5.16 22.54L3.6 28.8l6.41-1.5A12.76 12.76 0 1 0 16.01 3.2Zm0 23.25c-2.05 0-3.95-.59-5.57-1.61l-.4-.25-3.8.89.92-3.7-.26-.42a10.48 10.48 0 1 1 9.11 5.09Zm5.78-7.84c-.32-.16-1.89-.93-2.18-1.04-.29-.11-.5-.16-.72.16-.21.32-.82 1.04-1.01 1.25-.18.21-.37.24-.69.08-.32-.16-1.35-.5-2.57-1.58-.95-.85-1.59-1.9-1.78-2.22-.18-.32-.02-.49.14-.65.14-.14.32-.37.48-.56.16-.18.21-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.73-.98-2.37-.26-.62-.52-.54-.72-.55h-.61c-.21 0-.56.08-.85.4-.29.32-1.12 1.09-1.12 2.66 0 1.57 1.15 3.09 1.31 3.3.16.21 2.26 3.45 5.48 4.84.77.33 1.37.53 1.84.68.77.24 1.47.21 2.02.13.62-.09 1.89-.77 2.16-1.52.27-.75.27-1.39.19-1.52-.08-.13-.29-.21-.61-.37Z" />
        </svg>
    </a>
</body>
</html>
