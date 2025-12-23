<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'WV CHD PMIS' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/images/DOH_Logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()

    <!-- Dark Mode Initialization (must be in head to prevent flash) -->
    <script>
        // Initialize dark mode immediately before page renders
        if (localStorage.getItem('darkMode') === 'dark' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-neutral-900">

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
