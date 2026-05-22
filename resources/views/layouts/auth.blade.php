<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        .registration-card-shadow {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#3525cd",
                        "primary-container": "#4f46e5",
                        "on-primary": "#ffffff",
                        "on-primary-fixed": "#0f0069",
                        "primary-fixed": "#e2dfff",
                        "primary-fixed-dim": "#c3c0ff",
                        secondary: "#4648d4",
                        "secondary-container": "#6063ee",
                        "on-secondary": "#ffffff",
                        "on-secondary-container": "#fffbff",
                        "secondary-fixed": "#e1e0ff",
                        "secondary-fixed-dim": "#c0c1ff",
                        surface: "#f8f9fa",
                        "surface-bright": "#f8f9fa",
                        "surface-dim": "#d9dadb",
                        "surface-container": "#edeeef",
                        "surface-container-low": "#f3f4f5",
                        "surface-container-high": "#e7e8e9",
                        "surface-container-highest": "#e1e3e4",
                        "surface-container-lowest": "#ffffff",
                        "surface-variant": "#e1e3e4",
                        "on-surface": "#191c1d",
                        "on-surface-variant": "#464555",
                        outline: "#777587",
                        "outline-variant": "#c7c4d8",
                        background: "#f8f9fa",
                        "on-background": "#191c1d",
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        "2xl": "1rem",
                        "3xl": "1.5rem",
                        full: "9999px"
                    },
                    spacing: {
                        xs: "4px",
                        sm: "12px",
                        base: "8px",
                        md: "24px",
                        lg: "40px",
                        xl: "64px",
                        gutter: "24px",
                    },
                    fontSize: {
                        "label-md": ["14px", { lineHeight: "1.4", letterSpacing: "0.01em", fontWeight: "500" }],
                        "label-sm": ["12px", { lineHeight: "1.2", fontWeight: "600" }],
                        "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                        "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                        "headline-md": ["24px", { lineHeight: "1.3", fontWeight: "600" }],
                        "headline-lg": ["32px", { lineHeight: "1.2", fontWeight: "600" }],
                        "headline-lg-mobile": ["24px", { lineHeight: "1.2", fontWeight: "600" }],
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-background font-body-md text-on-surface selection:bg-primary-fixed-dim">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-surface/70 backdrop-blur-md border-b border-outline-variant/30 shadow-sm">
        <div class="flex justify-between items-center px-6 md:px-12 h-20 max-w-7xl mx-auto">
            <div class="font-bold text-2xl text-primary">EduPremium</div>
            <div class="flex items-center gap-base">
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-lg text-primary font-label-md hover:bg-primary/5 transition-all hidden md:block">Login</a>
                <a href="{{ route('register') }}" class="px-5 py-2 rounded-lg bg-primary-container text-on-primary font-label-md hover:bg-primary transition-all active:scale-95">Daftar Sekarang</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-6 md:px-12 overflow-hidden relative">
        <!-- Abstract Background Shapes -->
        <div class="absolute top-0 left-0 w-full h-full -z-10 pointer-events-none opacity-40">
            <div class="absolute top-[-10%] right-[-5%] w-[400px] h-[400px] rounded-full bg-primary-fixed blur-[120px]"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[300px] h-[300px] rounded-full bg-secondary-fixed blur-[100px]"></div>
        </div>

        @yield('content')
    </main>


    <script>
        // Micro-interaction for form inputs
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                if (input.parentElement.parentElement) {
                    input.parentElement.parentElement.classList.add('scale-[1.01]');
                }
            });
            input.addEventListener('blur', () => {
                if (input.parentElement.parentElement) {
                    input.parentElement.parentElement.classList.remove('scale-[1.01]');
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
