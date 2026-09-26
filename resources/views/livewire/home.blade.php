<div>
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-b-[2.5rem] bg-[#32B7C5] px-6 pb-8 pt-6 md:px-12">
        {{-- Quran-photo background, faded behind the search/list-selection controls --}}
        <div class="absolute inset-0 bg-cover bg-center opacity-25"
            style="background-image: url('{{ asset('images/tilawa-home-hero-bg.jpg') }}')"></div>
        <div class="absolute inset-0 bg-[#32B7C5]/80"></div>

        <div class="relative flex items-center justify-between">
            <img src="{{ asset('images/tilawa-logo.png') }}" alt="Tilawa" class="h-9 object-contain">

            <div class="flex items-center gap-3">
                <x-ui.theme-selector />
                <a href="{{ route('bookmarks') }}" wire:navigate
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25">
                    <x-ui.icon name="bookmark" class="h-[17px] w-[17px]" />
                </a>
                <a href="{{ route('profile') }}" wire:navigate
                    class="flex items-center gap-2 rounded-full border border-white/30 bg-white/15 py-1.5 pl-3.5 pr-1.5 transition hover:bg-white/25">
                    <span class="hidden text-sm font-semibold text-white sm:inline">{{ auth()->user()->name }}</span>
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-tilawa-amber text-xs font-extrabold text-[#7A5B14]">
                        {{ collect(explode(' ', auth()->user()->name))->map(fn ($n) => strtoupper($n[0]))->take(2)->implode('') }}
                    </span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full bg-white/15 px-4 py-2 text-xs font-bold text-white transition hover:bg-white/25">
                        Sign out
                    </button>
                </form>
            </div>
        </div>

        <div class="relative mx-auto mt-6 flex max-w-2xl flex-col items-center gap-4">
            @if ($tab === 'surah')
                <div class="flex w-full items-center gap-2.5 rounded-2xl bg-tilawa-surface px-4.5 py-3.5 shadow-lg">
                    <x-ui.icon name="search" class="h-[18px] w-[18px] shrink-0 text-tilawa-sub/70" />
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search a surah in the Qur'an..."
                        class="grow border-none p-0 text-sm text-tilawa-ink placeholder:text-tilawa-sub/60 focus:ring-0">
                </div>
            @endif

            <div class="flex flex-wrap items-center justify-center gap-2">
                <span class="text-xs font-semibold text-white/80">Popular:</span>
                @foreach ($this->popularSurahDetails as $surah)
                    <a href="{{ route('surah.show', $surah['number']) }}" wire:navigate
                        class="rounded-full border border-white/35 bg-white/20 px-3.5 py-1.5 text-xs font-semibold text-white transition hover:bg-white/30">
                        {{ $surah['englishName'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Continue reading --}}
    @if ($this->lastRead)
        <div class="px-6 pt-6 md:px-12">
            <a href="{{ route('surah.show', $this->lastRead['surah']['number']) }}" wire:navigate
                class="mx-auto flex max-w-2xl items-center gap-4 rounded-2xl border border-tilawa-line bg-tilawa-surface px-5 py-4 shadow-sm transition hover:border-tilawa-teal hover:shadow-md">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-tilawa-teal-light text-tilawa-teal-dark">
                    <x-ui.icon name="book" class="h-5 w-5" />
                </div>
                <div class="min-w-0 grow">
                    <div class="text-xs font-bold uppercase tracking-wide text-tilawa-gold">Continue reading</div>
                    <div class="truncate text-[15px] font-bold text-tilawa-ink">
                        {{ $this->lastRead['surah']['englishName'] }}
                        <span class="font-medium text-tilawa-sub">&middot; Ayah {{ $this->lastRead['ayah'] }}</span>
                    </div>
                </div>
                <x-ui.icon name="arrow-right" class="h-[18px] w-[18px] shrink-0 text-tilawa-sub" />
            </a>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex flex-wrap items-center gap-3 px-6 pt-6 md:px-12">
        <button wire:click="setTab('surah')"
            class="flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-sm transition {{ $tab === 'surah' ? 'bg-tilawa-amber text-[#7A5B14]' : 'bg-tilawa-surface text-tilawa-sub' }}">
            <x-ui.icon name="book" class="h-[17px] w-[17px]" /> Surah
        </button>
        <button wire:click="setTab('juz')"
            class="flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-sm transition {{ $tab === 'juz' ? 'bg-tilawa-amber text-[#7A5B14]' : 'bg-tilawa-surface text-tilawa-sub' }}">
            <x-ui.icon name="grid" class="h-[17px] w-[17px]" /> Juz
        </button>
        <button wire:click="setTab('page')"
            class="flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-sm transition {{ $tab === 'page' ? 'bg-tilawa-amber text-[#7A5B14]' : 'bg-tilawa-surface text-tilawa-sub' }}">
            <x-ui.icon name="list" class="h-[17px] w-[17px]" /> Page
        </button>

        @if ($tab === 'surah')
            <div class="grow"></div>
            <button wire:click="toggleSort"
                class="flex items-center gap-2 rounded-xl bg-tilawa-surface px-5 py-2.5 text-sm font-bold text-tilawa-sub shadow-sm transition hover:text-tilawa-ink">
                <x-ui.icon name="sort" class="h-[17px] w-[17px]" />
                {{ $sort === 'asc' ? '1 → 114' : '114 → 1' }}
            </button>
        @endif
    </div>

    {{-- Content --}}
    <div class="px-6 py-6 md:px-12 md:py-8">
        @if ($tab === 'surah')
            @if (count($this->surahs) === 0)
                <p class="py-16 text-center text-sm text-tilawa-sub">No surahs match "{{ $search }}".</p>
            @else
                <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->surahs as $surah)
                        <a href="{{ route('surah.show', $surah['number']) }}" wire:navigate
                            class="flex items-center gap-3.5 rounded-2xl border border-tilawa-line bg-tilawa-surface px-4 py-3.5 transition hover:border-tilawa-teal hover:shadow-sm">
                            <x-ui.number-badge :number="$surah['number']" class="h-11 w-11" textClass="text-sm" />
                            <div class="grow min-w-0">
                                <div class="truncate text-[15px] font-bold text-tilawa-ink">
                                    {{ $surah['englishName'] }}
                                    <span class="font-medium text-tilawa-sub text-[13px]">({{ $surah['englishNameTranslation'] }})</span>
                                </div>
                                <div class="mt-1 flex items-center gap-1.5">
                                    @if ($surah['revelationType'] === 'Meccan')
                                        <span class="rounded-full bg-tilawa-mint px-2.5 py-0.5 text-[11px] font-bold text-tilawa-purple">Meccan</span>
                                    @else
                                        <span class="rounded-full bg-[#C9E4FF] px-2.5 py-0.5 text-[11px] font-bold text-[#2A5FA0]">Medinan</span>
                                    @endif
                                    <span class="text-xs text-tilawa-sub">{{ $surah['numberOfAyahs'] }} verses</span>
                                </div>
                            </div>
                            <div class="font-arabic shrink-0 text-xl font-bold text-tilawa-ink">{{ $surah['name'] }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        @elseif ($tab === 'juz')
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                @foreach ($juzNumbers as $number)
                    <a href="{{ route('juz.show', $number) }}" wire:navigate
                        class="rounded-xl border border-tilawa-line bg-tilawa-surface px-3 py-4 text-center transition hover:border-tilawa-teal hover:shadow-sm">
                        <div class="text-xs font-semibold text-tilawa-sub">Juz</div>
                        <div class="text-lg font-bold text-tilawa-ink">{{ $number }}</div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="grid grid-cols-4 gap-2.5 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10">
                @foreach ($pageNumbers as $number)
                    <div class="rounded-lg border border-tilawa-line bg-tilawa-surface py-2.5 text-center text-sm font-semibold text-tilawa-ink">
                        {{ $number }}
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 bg-tilawa-teal px-6 py-4 md:px-12">
        <span class="text-xs text-white/85">&copy; {{ now()->year }} Tilawa. All rights reserved.</span>
        <div class="flex items-center gap-2">
            <a href="{{ route('terms') }}" wire:navigate title="Terms of Service"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25">
                <x-ui.icon name="book" class="h-[16px] w-[16px]" />
            </a>
            <a href="{{ route('privacy') }}" wire:navigate title="Privacy Policy"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25">
                <x-ui.icon name="lock" class="h-[16px] w-[16px]" />
            </a>
            <a href="mailto:{{ config('mail.from.address') }}" title="Contact us"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25">
                <x-ui.icon name="mail" class="h-[16px] w-[16px]" />
            </a>
        </div>
    </div>
</div>
