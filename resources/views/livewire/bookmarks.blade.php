<div>
    {{-- Header --}}
    <div class="rounded-b-[2.5rem] bg-tilawa-teal px-6 pb-8 pt-6 md:px-12">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" wire:navigate
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25">
                <x-ui.icon name="arrow-left" class="h-[18px] w-[18px]" />
            </a>
            <div>
                <div class="text-lg font-bold text-white">Bookmarks</div>
                <div class="text-xs font-semibold text-white/75">
                    {{ $this->bookmarksBySurah->flatten()->count() }} saved {{ \Illuminate\Support\Str::plural('ayah', $this->bookmarksBySurah->flatten()->count()) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="px-6 py-8 md:px-12">
        @if ($this->bookmarksBySurah->isEmpty())
            <div class="mx-auto flex max-w-md flex-col items-center gap-4 py-16 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-tilawa-teal-light text-tilawa-teal-dark">
                    <x-ui.icon name="bookmark" class="h-6 w-6" />
                </div>
                <h2 class="text-lg font-bold text-tilawa-ink">No bookmarks yet</h2>
                <p class="text-sm text-tilawa-sub">
                    Tap the bookmark icon on any ayah while reading to save it here for later.
                </p>
                <a href="{{ route('home') }}" wire:navigate
                    class="mt-2 rounded-xl bg-tilawa-teal px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:brightness-105">
                    Start reading
                </a>
            </div>
        @else
            <div class="mx-auto flex max-w-3xl flex-col gap-8">
                @foreach ($this->bookmarksBySurah as $surahNumber => $bookmarks)
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <a href="{{ route('surah.show', $surahNumber) }}" wire:navigate
                                class="text-sm font-bold text-tilawa-ink transition hover:text-tilawa-teal-dark">
                                {{ $bookmarks->first()->surah_name }}
                            </a>
                            <span class="text-xs font-semibold text-tilawa-sub">
                                {{ $bookmarks->count() }} {{ \Illuminate\Support\Str::plural('ayah', $bookmarks->count()) }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-3">
                            @foreach ($bookmarks as $bookmark)
                                <div class="flex items-start gap-3 rounded-2xl border border-tilawa-line bg-tilawa-surface p-4">
                                    <x-ui.number-badge :number="$bookmark->ayah_number" class="h-10 w-10 shrink-0" textClass="text-xs" />
                                    <div class="min-w-0 grow">
                                        <p class="font-arabic text-right text-xl leading-[2] text-tilawa-ink">{{ $bookmark->ayah_text }}</p>
                                    </div>
                                    <button type="button" wire:click="remove({{ $bookmark->id }})"
                                        wire:confirm="Remove this bookmark?"
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-tilawa-gold transition hover:bg-tilawa-line">
                                        <x-ui.icon name="bookmark-filled" class="h-[16px] w-[16px]" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
