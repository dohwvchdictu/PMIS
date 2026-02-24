<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'WVCHD PMIS' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/images/DOH_Logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()

    <!-- Custom styles for Livewire Alert z-index -->
    <style>
        /* Ensure SweetAlert2 appears above modals (modal z-index: 99999) */
        .swal-z-index-max {
            z-index: 999999 !important;
        }

        /* Global Livewire loading bar animation */
        @keyframes loading-bar {
            0% {
                transform: translateX(-100%);
            }

            50% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .animate-loading-bar {
            animation: loading-bar 1.2s ease-in-out infinite;
        }
    </style>

    <!-- Dark Mode Initialization (must be in head to prevent flash) -->
    <script>
        // Initialize dark mode immediately before page renders
        if (localStorage.getItem('darkMode') === 'dark' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-neutral-800">

    <!-- Global Livewire Loading Bar -->
    <div id="global-loading-bar" class="fixed top-0 left-0 right-0 z-[99999] h-1 overflow-hidden pointer-events-none"
        style="display:none">
        <div class="h-full bg-emerald-500" style="animation: loading-bar 1.2s ease-in-out infinite;"></div>
    </div>

    @livewire('partials.header')
    @livewire('partials.navbar')
    @livewire('partials.sidebar')

    <!-- Content -->
    <div class="w-full lg:pl-48 pt-[156px]">
        <main class="p-4 md:p-6">
            {{ $slot }}
        </main>
    </div>
    <!-- Footer sticks to bottom -->
    @livewire('partials.footer')

    @livewireScripts()
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @livewireAlert()
    @stack('scripts')

    <!-- Global Loading Bar Controller -->
    <script>
        (function() {
            var bar = document.getElementById('global-loading-bar');
            var timer;

            function show() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    if (bar) bar.style.display = 'block';
                }, 150);
            }

            function hide() {
                clearTimeout(timer);
                if (bar) bar.style.display = 'none';
            }
            document.addEventListener('livewire:request', show);
            document.addEventListener('livewire:response', hide);
            document.addEventListener('livewire:error', hide);
        })();
    </script>

    <!-- Dark Mode Toggle Function -->
    <script>
        function toggleDarkMode() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');

            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('darkMode', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('darkMode', 'dark');
            }

            // Reinitialize Preline components
            setTimeout(() => {
                if (window.HSStaticMethods) {
                    window.HSStaticMethods.autoInit();
                }
            }, 50);
        }
    </script>
</body>

</html>
