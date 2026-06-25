<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Admin CELL English Course' }}</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="hidden w-72 shrink-0 border-r border-slate-200 bg-white lg:flex lg:flex-col">
            <div class="border-b border-slate-200 px-6 py-6">
                <a href="{{ route('admin.dashboard') }}" class="block text-2xl font-extrabold tracking-tight text-indigo-600">CELL Admin</a>
                <p class="mt-1 text-sm font-medium text-slate-500">CELL English Course</p>
            </div>

            <nav class="flex-1 space-y-1 px-4 py-5 text-sm font-semibold text-slate-600">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    Dashboard
                </a>
                <a href="{{ route('admin.registrants.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.registrants.*') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                    Pendaftar
                </a>
                <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.payments.*') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                    Pembayaran
                </a>
                <a href="{{ route('admin.programs.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.programs.*') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">school</span>
                    Program
                </a>
                <a href="{{ route('admin.program-categories.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.program-categories.*') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">category</span>
                    Kelompok Program
                </a>
                <a href="{{ route('admin.tutors.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.tutors.*') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">person_book</span>
                    Tutor
                </a>
                <a href="{{ route('admin.placement.questions.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.placement.questions.*') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">quiz</span>
                    Kelola Soal
                </a>
                <a href="{{ route('admin.placement.results') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.placement.results') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">assignment_turned_in</span>
                    Hasil Placement Test
                </a>
                <a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.schedules.*') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                    Jadwal Belajar Siswa
                </a>
                <a href="{{ route('admin.schedule-templates.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.schedule-templates.*') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">event_repeat</span>
                    Batch & Pilihan Jadwal
                </a>
                <a href="{{ route('admin.class-rooms.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 {{ request()->routeIs('admin.class-rooms.*') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="material-symbols-outlined text-[20px]">meeting_room</span>
                    Ruang Kelas
                </a>
                <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-slate-100 hover:text-slate-900">
                    <span class="material-symbols-outlined text-[20px]">public</span>
                    Lihat Website
                </a>
            </nav>

            <div class="border-t border-slate-200 p-4">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="mt-1 truncate text-xs font-medium text-slate-500">{{ auth()->user()->email ?? 'admin@cell.local' }}</p>
                </div>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
                <div class="flex h-16 items-center justify-between px-5 md:px-8">
                    <div class="flex items-center gap-3">
                        <details class="lg:hidden">
                            <summary class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700">
                                <span class="material-symbols-outlined">menu</span>
                            </summary>
                            <div class="absolute left-4 top-14 w-72 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.registrants.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.registrants.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">group</span>
                                    Pendaftar
                                </a>
                                <a href="{{ route('admin.payments.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.payments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">payments</span>
                                    Pembayaran
                                </a>
                                <a href="{{ route('admin.programs.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.programs.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">school</span>
                                    Program
                                </a>
                                <a href="{{ route('admin.program-categories.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.program-categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">category</span>
                                    Kelompok Program
                                </a>
                                <a href="{{ route('admin.tutors.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.tutors.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">person_book</span>
                                    Tutor
                                </a>
                                <a href="{{ route('admin.placement.questions.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.placement.questions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">quiz</span>
                                    Kelola Soal
                                </a>
                                <a href="{{ route('admin.placement.results') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.placement.results') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">assignment_turned_in</span>
                                    Hasil Placement Test
                                </a>
                                <a href="{{ route('admin.schedules.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.schedules.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                                    Jadwal Belajar Siswa
                                </a>
                                <a href="{{ route('admin.schedule-templates.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.schedule-templates.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">event_repeat</span>
                                    Batch & Pilihan Jadwal
                                </a>
                                <a href="{{ route('admin.class-rooms.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('admin.class-rooms.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                    <span class="material-symbols-outlined text-[20px]">meeting_room</span>
                                    Ruang Kelas
                                </a>
                            </div>
                        </details>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">Admin Panel</p>
                            <h1 class="text-lg font-bold text-slate-900">{{ $pageTitle ?? 'Dashboard' }}</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="hidden rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-indigo-600 hover:text-indigo-600 md:inline-flex">Website</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                <span class="material-symbols-outlined text-[18px]">logout</span>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="px-5 py-8 md:px-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
