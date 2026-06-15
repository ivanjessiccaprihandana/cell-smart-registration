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
        $hasPlacementAttempt = auth()->check()
            && \App\Models\PlacementTestAttempt::where('user_id', auth()->id())->exists();
        $canAccessPlacementTest = auth()->check()
            && auth()->user()->payment_status === 'diterima'
            && !$hasPlacementAttempt;
        $canAccessClassSchedule = auth()->check()
            && auth()->user()->payment_status === 'diterima'
            && $hasPlacementAttempt;
    @endphp
    <header class="sticky top-0 z-50 w-full border-b border-slate-200/70 bg-white/95 backdrop-blur-sm">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 md:px-8">
            <a href="{{ route('home') }}" class="text-xl font-semibold tracking-tight text-indigo-600">CELL English Course</a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a class="hover:text-indigo-600 transition-colors" href="#home">Beranda</a>
                <a class="hover:text-indigo-600 transition-colors" href="#programs">Program</a>
                <a class="hover:text-indigo-600 transition-colors" href="#tutors">Profile</a>
                <a class="hover:text-indigo-600 transition-colors" href="#pricing">Kelas</a>
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
                <a class="hover:text-indigo-600 transition-colors" href="#contact">Kontak</a>
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
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="#home">Beranda</a>
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="#programs">Program</a>
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="#tutors">Profile</a>
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="#pricing">Kelas</a>
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
                        <a class="block rounded-xl px-3 py-2 hover:bg-slate-100" href="#contact">Kontak</a>
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
</body>
</html>
