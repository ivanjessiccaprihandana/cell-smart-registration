<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Cell English Course and Learning' }}</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
</head>
<body class="bg-white text-slate-900 font-sans antialiased">
    <header class="sticky top-0 z-50 w-full border-b border-slate-200/40 bg-white/95 backdrop-blur-sm">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 md:px-8">
            <div class="text-2xl font-bold text-indigo-600">Cell English Course and Learning</div>
            <nav class="hidden md:flex gap-8">
                <a class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" href="#home">Home</a>
                <a class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" href="#programs">Programs</a>
                <a class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" href="#tutors">Tutors</a>
                <a class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" href="#pricing">Pricing</a>
                <a class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" href="#testimonials">Testimonials</a>
            </nav>
            <div class="flex gap-4 items-center">
                <button class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Login</button>
                <button class="inline-flex items-center justify-center rounded-lg px-6 py-2 text-sm font-semibold text-white shadow-md bg-primary hover:bg-primary-hover transition-all active:scale-95">Daftar Sekarang</button>
            </div>
        </div>
    </header>

    @yield('content')
</body>
</html>
