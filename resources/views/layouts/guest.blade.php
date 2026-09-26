<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Tilawa' }}</title>
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('tilawa-theme');
                var resolved = (stored === 'light' || stored === 'dark')
                    ? stored
                    : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                if (resolved === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-white font-sans text-tilawa-ink antialiased dark:bg-[#101c20]">
    {{ $slot }}
    @livewireScripts
</body>
</html>
