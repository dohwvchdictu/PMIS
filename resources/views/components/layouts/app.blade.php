<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'PMIS' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/images/DOH_Logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()

    <!-- Custom styles -->
    <style>
        /* Ensure SweetAlert2 appears above modals */
        .swal-z-index-max {
            z-index: 999999 !important;
        }

        /* Disable built-in NProgress — we use our own custom bar */
        #nprogress {
            display: none !important;
        }

        /* Custom navigate progress bar */
        #custom-progress-bar {
            position: fixed;
            top: 156px;
            left: 12rem;
            /* sidebar w-48 */
            right: 0;
            height: 3px;
            background: #10b981;
            z-index: 999999;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
            pointer-events: none;
        }

        @media (max-width: 1023px) {
            #custom-progress-bar {
                left: 0;
            }
        }

        /* Global Livewire loading bar (component updates) */
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

        /* Skeleton pulse animation */
        @keyframes skeleton-pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .skeleton {
            animation: skeleton-pulse 1.5s ease-in-out infinite;
        }

        /* Page content fade-in on navigate */
        .page-content {
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

    <!-- Custom Navigate Progress Bar -->
    <div id="custom-progress-bar"></div>

    <x-partials.header />
    @persist('navbar')
        @livewire('partials.navbar')
    @endpersist
    <x-partials.sidebar />

    <!-- Content -->
    <div class="w-full lg:pl-48 pt-[156px]">
        <main class="p-4 md:p-6 page-content">
            {{ $slot }}
        </main>
    </div>
    <!-- Footer sticks to bottom -->
    <x-partials.footer />

    @livewireScripts()
    @livewireAlert()
    @stack('scripts')

    <!-- Progress Bar Controller -->
    <script>
        (function() {
            var bar = document.getElementById('custom-progress-bar');
            var rafId, progress = 0,
                active = false;

            function setWidth(p) {
                bar.style.transform = 'scaleX(' + p + ')';
            }

            function trickle() {
                if (!active) return;
                progress += Math.random() * 0.08;
                if (progress > 0.9) progress = 0.9;
                setWidth(progress);
                rafId = setTimeout(trickle, 300);
            }

            function start() {
                progress = 0.05;
                active = true;
                bar.style.opacity = '1';
                bar.style.transition = 'transform 0.3s ease';
                setWidth(progress);
                trickle();
            }

            function done() {
                clearTimeout(rafId);
                active = false;
                bar.style.transition = 'transform 0.2s ease, opacity 0.3s ease 0.15s';
                setWidth(1);
                setTimeout(function() {
                    bar.style.opacity = '0';
                    setTimeout(function() {
                        setWidth(0);
                        bar.style.opacity = '1';
                    }, 300);
                }, 200);
            }

            /* wire:navigate page transitions */
            document.addEventListener('livewire:navigating', start);
            document.addEventListener('livewire:navigated', done);

            /* Livewire component request/response (AJAX updates) */
            document.addEventListener('livewire:request', start);
            document.addEventListener('livewire:response', done);
            document.addEventListener('livewire:error', done);
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
