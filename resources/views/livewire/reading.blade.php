<div x-data="{ tajweedRule: null, tajweedRules: @js($this->tajweedRules), selectedWord: null }"
    x-on:open-tajweed.window="tajweedRule = tajweedRules[$event.detail] ?? null"
    x-on:open-word.window="selectedWord = $event.detail">
    {{-- Header --}}
    <div class="rounded-b-[2.5rem] bg-tilawa-teal px-6 pb-6 pt-6 md:px-12">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" wire:navigate
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25">
                <x-ui.icon name="arrow-left" class="h-[18px] w-[18px]" />
            </a>

            <x-ui.theme-selector class="order-last" />

            <div class="min-w-0 flex-1 text-center">
                <div class="truncate text-lg font-bold text-white">{{ $this->surah['englishName'] }}</div>
                <div class="text-xs font-semibold text-white/75">
                    {{ $this->surah['englishNameTranslation'] }} &middot; {{ $this->surah['revelationType'] }} &middot; {{ $this->surah['numberOfAyahs'] }} verses
                </div>
            </div>

            <div class="font-arabic h-10 shrink-0 text-2xl font-bold text-white">{{ $this->surah['name'] }}</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3 px-6 pt-6 md:px-12">
        <x-ui.drawer title="Choose a surah" :trigger-label="$number . '. ' . $this->surah['englishName']" search-placeholder="Search surah by name or number...">
            @foreach ($this->surahList as $item)
                <button type="button" wire:click="goToSurah({{ $item['number'] }})" @click="open = false"
                    x-show="query === '' || @js(\Illuminate\Support\Str::lower($item['englishName'].' '.$item['number'])).includes(query.toLowerCase())"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm transition hover:bg-tilawa-line dark:hover:bg-white/10 {{ $item['number'] === $number ? 'bg-tilawa-teal-light/40 font-bold text-tilawa-teal-dark dark:bg-tilawa-teal/20 dark:text-tilawa-teal-light' : 'text-tilawa-ink dark:text-[#f3ead9]' }}">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-tilawa-line text-xs font-bold text-tilawa-sub dark:border-white/10 dark:text-[#a9b9bc]">
                        {{ $item['number'] }}
                    </span>
                    <span class="grow truncate">{{ $item['englishName'] }}</span>
                    <span class="font-arabic shrink-0 text-base text-tilawa-sub">{{ $item['name'] }}</span>
                </button>
            @endforeach
        </x-ui.drawer>

        <x-ui.drawer title="Choose a translation" trigger-icon="globe" :trigger-label="$this->translationLanguages[$translationLanguage]" search-placeholder="Search language or translator...">
            @foreach ($this->translationLanguages as $code => $label)
                <button type="button" wire:click="setTranslationLanguage('{{ $code }}')" @click="open = false"
                    x-show="query === '' || @js(\Illuminate\Support\Str::lower($label.' '.$this->translatorCredits[$code])).includes(query.toLowerCase())"
                    class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-tilawa-line dark:hover:bg-white/10 {{ $translationLanguage === $code ? 'bg-tilawa-teal-light/40 dark:bg-tilawa-teal/20' : '' }}">
                    <span>
                        <span class="block text-sm font-bold {{ $translationLanguage === $code ? 'text-tilawa-teal-dark dark:text-tilawa-teal-light' : 'text-tilawa-ink dark:text-[#f3ead9]' }}">{{ $label }}</span>
                        <span class="block text-xs text-tilawa-sub dark:text-[#a9b9bc]">{{ $this->translatorCredits[$code] }}</span>
                    </span>
                    @if ($translationLanguage === $code)
                        <x-ui.icon name="check-circle" class="h-4 w-4 shrink-0 text-tilawa-teal-dark" />
                    @endif
                </button>
            @endforeach
        </x-ui.drawer>

        <button type="button" wire:click="toggleTajweed" title="Highlight Tajweed rules in the Arabic text"
            class="flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-bold shadow-sm transition {{ $tajweedEnabled ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-950' : 'border-tilawa-line bg-tilawa-surface hover:border-cyan-300' }}">
            <img src="{{ asset('images/tajweed-icon.png') }}" alt="" class="h-5 w-5 shrink-0 {{ $tajweedEnabled ? '' : 'opacity-60' }}">
            <span class="hidden text-cyan-600 dark:text-cyan-400 sm:inline">Tajweed</span>
        </button>

        <div class="grow"></div>

        <div class="flex items-center gap-1 rounded-xl bg-tilawa-surface p-1 shadow-sm">
            <button wire:click="setMode('ayat')"
                class="rounded-lg px-4 py-2 text-sm font-bold transition {{ $mode === 'ayat' ? 'bg-tilawa-amber text-[#7A5B14]' : 'text-tilawa-sub' }}">
                Ayat
            </button>
            <button wire:click="setMode('mushaf')"
                class="rounded-lg px-4 py-2 text-sm font-bold transition {{ $mode === 'mushaf' ? 'bg-tilawa-amber text-[#7A5B14]' : 'text-tilawa-sub' }}">
                Mushaf
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div class="px-6 py-6 md:px-12 md:py-8">
        @if (! in_array($number, [1, 9], true))
            <div class="mx-auto mb-8 max-w-3xl text-center">
                <p class="font-arabic text-3xl font-bold leading-relaxed text-tilawa-teal-dark">
                    بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                </p>
            </div>
        @endif

        @if ($mode === 'ayat')
            <div id="ayat-top" class="mx-auto flex max-w-3xl flex-col gap-4">
                @foreach ($this->pagedAyahs as $ayah)
                    @php
                        $shareText = "{$ayah['arabic']}\n\n{$ayah['translation']}\n\n— {$this->surah['englishName']} {$this->surah['number']}:{$ayah['number']}, Tilawa";
                    @endphp
                    @php $isBookmarked = in_array($ayah['number'], $this->bookmarkedAyahNumbers, true); @endphp
                    <div class="rounded-2xl border {{ $isBookmarked ? 'border-tilawa-gold/50' : 'border-tilawa-line' }} bg-tilawa-surface p-5" x-data="{ copied: false }">
                        <div class="mb-3 flex items-center justify-between">
                            <x-ui.number-badge :number="$ayah['number']" class="h-11 w-11" textClass="text-sm" />
                            <div class="flex items-center gap-1.5">
                                <button type="button" wire:click="toggleBookmark({{ $ayah['number'] }})"
                                    class="flex h-8 w-8 items-center justify-center rounded-full transition hover:bg-tilawa-line {{ $isBookmarked ? 'text-tilawa-gold' : 'text-tilawa-sub/70 hover:text-tilawa-ink' }}">
                                    <x-ui.icon name="{{ $isBookmarked ? 'bookmark-filled' : 'bookmark' }}" class="h-[16px] w-[16px]" />
                                </button>
                                <button type="button" x-cloak x-show="!copied"
                                    @click="navigator.clipboard.writeText(@js($shareText)); copied = true; setTimeout(() => copied = false, 1800)"
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-tilawa-sub/70 transition hover:bg-tilawa-line hover:text-tilawa-ink">
                                    <x-ui.icon name="copy" class="h-[16px] w-[16px]" />
                                </button>
                                <span x-cloak x-show="copied" class="px-2 text-xs font-semibold text-tilawa-teal-dark">Copied</span>
                                <button type="button"
                                    @click="if (navigator.share) { navigator.share({ text: @js($shareText) }) } else { navigator.clipboard.writeText(@js($shareText)); copied = true; setTimeout(() => copied = false, 1800) }"
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-tilawa-sub/70 transition hover:bg-tilawa-line hover:text-tilawa-ink">
                                    <x-ui.icon name="share" class="h-[16px] w-[16px]" />
                                </button>
                            </div>
                        </div>
                        <p class="font-arabic mb-4 text-right text-2xl leading-[2.2] text-tilawa-ink"
                            @click="$event.target.closest('.tajweed-mark') && $dispatch('open-tajweed', $event.target.closest('.tajweed-mark').dataset.tajweedRule)">
                            @php
                                $words = $this->wordsByAyah[$ayah['number']] ?? null;
                                $tajweedWords = ($tajweedEnabled ? ($this->tajweedAyahs[$ayah['number']]['words'] ?? null) : null);
                            @endphp
                            @if ($words)
                                @foreach ($words as $i => $word)
                                    @php $wordKey = $this->number.':'.$ayah['number'].':'.$i; @endphp
                                    <span class="word-token" wire:key="word-{{ $ayah['number'] }}-{{ $i }}"
                                        @click="!$event.target.closest('.tajweed-mark') && $dispatch('open-word', @js(array_merge($word, [
                                            'surah' => $this->number,
                                            'surahName' => $this->surah['englishName'],
                                            'ayah' => $ayah['number'],
                                            'wordKey' => $wordKey,
                                            'tajweedRule' => \App\Support\TajweedRules::extractFromHtml($tajweedWords[$i] ?? null),
                                        ])))"
                                        x-bind:class="selectedWord && selectedWord.wordKey === '{{ $wordKey }}' ? 'word-token-selected' : ''">
                                        @if ($tajweedWords && isset($tajweedWords[$i]))
                                            {!! $tajweedWords[$i] !!}
                                        @else
                                            {{ $word['arabic'] }}
                                        @endif
                                    </span>
                                @endforeach
                            @elseif ($tajweedEnabled && isset($this->tajweedAyahs[$ayah['number']]))
                                {!! $this->tajweedAyahs[$ayah['number']]['html'] !!}
                            @else
                                {{ $ayah['arabic'] }}
                            @endif
                        </p>
                        <p class="mb-2 text-sm italic leading-relaxed text-tilawa-sub">{{ $ayah['transliteration'] }}</p>
                        @if ($translationLanguage === 'ar')
                            <p class="font-arabic text-right text-lg leading-[2] text-tilawa-ink" dir="rtl">{{ $ayah['translation'] }}</p>
                        @else
                            <p class="text-[15px] leading-relaxed text-tilawa-ink">{{ $ayah['translation'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($this->totalPages() > 1)
                <div class="mx-auto mt-6 flex max-w-3xl items-center justify-between gap-3 rounded-2xl border border-tilawa-line bg-tilawa-surface px-4 py-3 shadow-sm sm:px-5">
                    <button type="button" wire:click="previousPage" @disabled($page === 1)
                        class="flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-sm font-bold transition disabled:cursor-not-allowed disabled:opacity-35 {{ $page === 1 ? '' : 'text-tilawa-ink hover:bg-tilawa-line' }}">
                        <x-ui.icon name="arrow-left" class="h-[15px] w-[15px]" />
                        <span class="hidden sm:inline">Previous</span>
                    </button>

                    <div class="flex flex-col items-center gap-1.5">
                        <span class="text-xs font-bold text-tilawa-ink">
                            Ayah {{ ($page - 1) * $perPage + 1 }}–{{ min($page * $perPage, $this->surah['numberOfAyahs']) }}
                            <span class="font-medium text-tilawa-sub">of {{ $this->surah['numberOfAyahs'] }}</span>
                        </span>
                        @if ($this->totalPages() <= 12)
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                @for ($i = 1; $i <= $this->totalPages(); $i++)
                                    <span class="h-1.5 rounded-full transition-all {{ $i === $page ? 'w-4 bg-tilawa-teal' : 'w-1.5 bg-tilawa-line' }}"></span>
                                @endfor
                            </div>
                        @else
                            <span class="text-[11px] font-semibold text-tilawa-sub">Page {{ $page }} of {{ $this->totalPages() }}</span>
                        @endif
                    </div>

                    <button type="button" wire:click="nextPage" @disabled($page === $this->totalPages())
                        class="flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-sm font-bold transition disabled:cursor-not-allowed disabled:opacity-35 {{ $page === $this->totalPages() ? '' : 'text-tilawa-ink hover:bg-tilawa-line' }}">
                        <span class="hidden sm:inline">Next</span>
                        <x-ui.icon name="arrow-right" class="h-[15px] w-[15px]" />
                    </button>
                </div>
            @endif
        @else
            <div id="mushaf-top" class="mx-auto max-w-3xl rounded-2xl border-2 border-tilawa-gold/30 bg-tilawa-paper p-8 md:p-10">
                @if ($this->currentMushafPageNumber)
                    <div class="mb-5 text-center text-xs font-bold uppercase tracking-wide text-tilawa-gold">
                        Page {{ $this->currentMushafPageNumber }}
                    </div>
                @endif
                <p class="font-arabic text-right text-[28px] leading-[2.4] text-tilawa-ink"
                    @click="$event.target.closest('.tajweed-mark') && $dispatch('open-tajweed', $event.target.closest('.tajweed-mark').dataset.tajweedRule)">
                    @foreach ($this->mushafAyahs as $ayah)
                        @php
                            $words = $this->wordsByAyah[$ayah['number']] ?? null;
                            $tajweedWords = ($tajweedEnabled ? ($this->tajweedAyahs[$ayah['number']]['words'] ?? null) : null);
                        @endphp
                        @if ($words)
                            @foreach ($words as $i => $word)
                                @php $wordKey = $this->number.':'.$ayah['number'].':'.$i; @endphp
                                <span class="word-token" wire:key="mushaf-word-{{ $ayah['number'] }}-{{ $i }}"
                                    @click="!$event.target.closest('.tajweed-mark') && $dispatch('open-word', @js(array_merge($word, [
                                        'surah' => $this->number,
                                        'surahName' => $this->surah['englishName'],
                                        'ayah' => $ayah['number'],
                                        'wordKey' => $wordKey,
                                        'tajweedRule' => \App\Support\TajweedRules::extractFromHtml($tajweedWords[$i] ?? null),
                                    ])))"
                                    x-bind:class="selectedWord && selectedWord.wordKey === '{{ $wordKey }}' ? 'word-token-selected' : ''">
                                    @if ($tajweedWords && isset($tajweedWords[$i]))
                                        {!! $tajweedWords[$i] !!}
                                    @else
                                        {{ $word['arabic'] }}
                                    @endif
                                </span>
                            @endforeach
                        @elseif ($tajweedEnabled && isset($this->tajweedAyahs[$ayah['number']]))
                            {!! $this->tajweedAyahs[$ayah['number']]['html'] !!}
                        @else
                            {{ $ayah['arabic'] }}
                        @endif
                        <x-ui.number-badge :number="$ayah['number']" class="mx-1 h-8 w-8" textClass="text-xs" />
                    @endforeach
                </p>
            </div>

            @if ($this->totalMushafSteps() > 1)
                <div class="mx-auto mt-6 flex max-w-3xl items-center justify-between gap-3 rounded-2xl border border-tilawa-line bg-tilawa-surface px-4 py-3 shadow-sm sm:px-5">
                    <button type="button" wire:click="previousMushafPage" @disabled($mushafStep === 1)
                        class="flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-sm font-bold transition disabled:cursor-not-allowed disabled:opacity-35 {{ $mushafStep === 1 ? '' : 'text-tilawa-ink hover:bg-tilawa-line' }}">
                        <x-ui.icon name="arrow-left" class="h-[15px] w-[15px]" />
                        <span class="hidden sm:inline">Previous</span>
                    </button>

                    <div class="flex flex-col items-center gap-1.5">
                        <span class="text-xs font-bold text-tilawa-ink">
                            Mushaf page {{ $this->currentMushafPageNumber }}
                        </span>
                        @if ($this->totalMushafSteps() <= 12)
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                @for ($i = 1; $i <= $this->totalMushafSteps(); $i++)
                                    <span class="h-1.5 rounded-full transition-all {{ $i === $mushafStep ? 'w-4 bg-tilawa-gold' : 'w-1.5 bg-tilawa-gold/25' }}"></span>
                                @endfor
                            </div>
                        @else
                            <span class="text-[11px] font-semibold text-tilawa-sub">{{ $mushafStep }} of {{ $this->totalMushafSteps() }}</span>
                        @endif
                    </div>

                    <button type="button" wire:click="nextMushafPage" @disabled($mushafStep === $this->totalMushafSteps())
                        class="flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-sm font-bold transition disabled:cursor-not-allowed disabled:opacity-35 {{ $mushafStep === $this->totalMushafSteps() ? '' : 'text-tilawa-ink hover:bg-tilawa-line' }}">
                        <span class="hidden sm:inline">Next</span>
                        <x-ui.icon name="arrow-right" class="h-[15px] w-[15px]" />
                    </button>
                </div>
            @endif
        @endif

        {{-- Prev / next surah navigation --}}
        <div class="mx-auto mt-8 flex max-w-3xl items-center justify-between gap-3">
            @if ($previousNumber)
                <a href="{{ route('surah.show', $previousNumber) }}" wire:navigate
                    class="flex items-center gap-2 rounded-xl border border-tilawa-line bg-tilawa-surface px-4 py-2.5 text-sm font-bold text-tilawa-ink shadow-sm transition hover:border-tilawa-teal">
                    <x-ui.icon name="arrow-left" class="h-[15px] w-[15px]" /> Previous surah
                </a>
            @else
                <span></span>
            @endif

            @if ($nextNumber)
                <a href="{{ route('surah.show', $nextNumber) }}" wire:navigate
                    class="flex items-center gap-2 rounded-xl border border-tilawa-line bg-tilawa-surface px-4 py-2.5 text-sm font-bold text-tilawa-ink shadow-sm transition hover:border-tilawa-teal">
                    Next surah <x-ui.icon name="arrow-right" class="h-[15px] w-[15px]" />
                </a>
            @else
                <span></span>
            @endif
        </div>
    </div>

    <x-ui.tajweed-panel />
    <x-ui.word-drawer />
</div>
