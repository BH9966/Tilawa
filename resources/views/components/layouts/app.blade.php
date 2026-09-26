@props(['title' => 'Tilawa'])

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Tilawa' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#F4FAFA] font-sans text-tilawa-ink antialiased">
    <header class="flex items-center justify-between bg-tilawa-teal px-6 py-4 md:px-12">
        <img src="{{ asset('images/tilawa-logo.png') }}" alt="Tilawa" class="h-7 object-contain">

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 rounded-full border border-white/30 bg-white/15 py-1.5 pl-3.5 pr-1.5">
                <span class="text-sm font-semibold text-white">{{ auth()->user()->name }}</span>
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-tilawa-amber text-xs font-extrabold text-[#7A5B14]">
                    {{ collect(explode(' ', auth()->user()->name))->map(fn ($n) => strtoupper($n[0]))->take(2)->implode('') }}
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-full bg-white/15 px-4 py-2 text-xs font-bold text-white transition hover:bg-white/25">
                    Sign out
                </button>
            </form>
        </div>
    </header>

    <main>{{ $slot }}</main>

    @livewireScripts
</body>
</html>
