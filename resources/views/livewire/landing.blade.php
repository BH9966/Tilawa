<div class="bg-white">
    {{-- Header --}}
    <header class="absolute inset-x-0 top-0 z-20 flex items-center justify-between bg-gradient-to-b from-tilawa-teal-dark/70 via-tilawa-teal-dark/25 to-transparent px-6 pb-12 pt-6 md:px-12">
        <img src="{{ asset('images/tilawa-logo.png') }}" alt="Tilawa" class="h-10 object-contain">

        <nav class="flex items-center gap-3 md:gap-5">
            <a href="{{ route('login') }}" wire:navigate
                class="text-sm font-bold text-white/90 transition hover:text-white">
                Sign in
            </a>
            <a href="{{ route('register') }}" wire:navigate
                class="rounded-full bg-tilawa-amber px-5 py-2.5 text-sm font-bold text-[#7A5B14] shadow-sm transition hover:brightness-105">
                Sign up
            </a>
        </nav>
    </header>

    {{-- Hero --}}
    <section class="relative flex min-h-[640px] items-center overflow-hidden rounded-b-[3rem] bg-tilawa-teal md:min-h-[760px]">
        <img src="{{ asset('images/tilawa-home-hero-bg.jpg') }}" alt=""
            class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-black/35 via-tilawa-teal/70 to-tilawa-teal"></div>

        <div class="relative mx-auto w-full max-w-3xl px-6 pb-16 pt-32 text-center md:px-12 md:pt-40">
            <span class="inline-block rounded-full border border-white/30 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-white/85">
                Tilawa
            </span>
            <h1 class="mt-6 text-4xl font-extrabold leading-tight text-white md:text-6xl">
                Your daily companion for the Qur'an
            </h1>
            <p class="mx-auto mt-5 max-w-xl text-base leading-relaxed text-white/85 md:text-lg">
                Read every surah in clean Uthmani script, with transliteration and translation side by side —
                or in an authentic Mushaf view. Free, and always within reach.
            </p>

            <div class="mt-9 flex flex-col items-center gap-4">
                <a href="{{ route('home') }}" wire:navigate
                    class="flex items-center gap-2 rounded-xl bg-tilawa-amber px-8 py-4 text-base font-bold text-[#7A5B14] shadow-lg transition hover:brightness-105">
                    Get Started
                    <x-ui.icon name="arrow-right" class="h-[18px] w-[18px]" />
                </a>
                <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold text-white/80 transition hover:text-white">
                    Already reading with us? <span class="underline underline-offset-2">Sign in</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="px-6 py-20 md:px-12">
        <div class="mx-auto max-w-5xl text-center">
            <h2 class="text-2xl font-extrabold text-tilawa-ink md:text-3xl">Everything you need to read, every day</h2>
            <p class="mx-auto mt-3 max-w-xl text-sm text-tilawa-sub md:text-base">
                A calm, distraction-free space built around the way people actually read the Qur'an.
            </p>
        </div>

        <div class="mx-auto mt-12 grid max-w-5xl grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['icon' => 'book', 'title' => 'Read every surah', 'body' => 'All 114 surahs with Arabic text, transliteration and translation, ayah by ayah.'],
                ['icon' => 'search', 'title' => 'Find any surah instantly', 'body' => 'Search by name and jump straight to it — no scrolling through long lists.'],
                ['icon' => 'grid', 'title' => 'Browse by Juz', 'body' => 'Move through the Qur\'an the way you already study it, one juz at a time.'],
                ['icon' => 'list', 'title' => 'Authentic Mushaf view', 'body' => 'Switch to a continuous script view paginated by the real printed Mushaf pages.'],
            ] as $feature)
                <div class="rounded-2xl border border-tilawa-line bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-tilawa-teal-light text-tilawa-teal-dark">
                        <x-ui.icon name="{{ $feature['icon'] }}" class="h-5 w-5" />
                    </div>
                    <h3 class="mt-4 text-[15px] font-bold text-tilawa-ink">{{ $feature['title'] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-tilawa-sub">{{ $feature['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Split feature highlight --}}
    <section class="bg-tilawa-paper px-6 py-20 md:px-12">
        <div class="mx-auto flex max-w-5xl flex-col items-center gap-10 md:flex-row md:gap-16">
            <div class="w-full max-w-sm shrink-0 md:w-2/5">
                <img src="{{ asset('images/tilawa-landing-child.jpg') }}" alt="A young reader with the Qur'an"
                    class="aspect-[3/4] w-full rounded-3xl object-cover shadow-xl">
            </div>
            <div class="text-center md:text-left">
                <span class="text-xs font-bold uppercase tracking-widest text-tilawa-gold">Built for quiet reading</span>
                <h2 class="mt-3 text-2xl font-extrabold text-tilawa-ink md:text-3xl">
                    No clutter. No noise. Just the words.
                </h2>
                <p class="mt-4 max-w-md text-[15px] leading-relaxed text-tilawa-sub">
                    Tilawa keeps every page focused on the text itself — clean Uthmani Arabic, careful transliteration
                    and a faithful translation, laid out so you can sit with a few ayahs at a time without feeling rushed.
                </p>
            </div>
        </div>
    </section>

    {{-- CTA banner --}}
    <section class="relative flex min-h-[380px] items-center justify-center overflow-hidden bg-tilawa-teal">
        <img src="{{ asset('images/tilawa-home-hero-bg.jpg') }}" alt=""
            class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-tilawa-teal via-tilawa-teal/85 to-black/30"></div>


    </section>

    {{-- Footer --}}
    <footer class="bg-tilawa-ink px-6 py-5 md:px-12">
        <p class="mx-auto mb-4 max-w-5xl border-b border-white/10 pb-4 text-center text-[11px] text-white/40 md:text-left">
            Qur'an text, transliteration and translation provided via the
            <a href="https://alquran.cloud" target="_blank" rel="noopener" class="underline decoration-white/30 underline-offset-2 hover:text-white/70">Al Quran Cloud API</a>.
        </p>
        <div class="mx-auto flex max-w-5xl flex-col items-center justify-between gap-3 text-center md:flex-row md:text-left">
            <img src="{{ asset('images/tilawa-logo.png') }}" alt="Tilawa" class="h-7 object-contain">

            <p class="text-xs text-white/50">&copy; {{ now()->year }} Tilawa. All rights reserved.</p>

            {{-- Social icons: intentionally left out until real Ubitech/Tilawa
                social accounts exist — a "#" link here would just look broken. --}}
            <div class="flex items-center gap-4 text-xs text-white/50">
                <a href="{{ route('terms') }}" wire:navigate class="transition hover:text-white/80">Terms</a>
                <a href="{{ route('privacy') }}" wire:navigate class="transition hover:text-white/80">Privacy</a>
            </div>
        </div>
    </footer>
</div>
