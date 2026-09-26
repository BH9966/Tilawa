{{--
    Word Details Drawer — slides in from the right over a semi-transparent
    overlay when a word is tapped in the Arabic text. Driven entirely by an
    ancestor's `selectedWord` Alpine state (set via a window-level
    `open-word` custom event); tapping another word while this is open just
    updates `selectedWord` in place rather than opening a second drawer.
--}}
<div x-show="selectedWord" x-cloak x-transition.opacity.duration.200ms @click="selectedWord = null" class="fixed inset-0 z-40 bg-tilawa-ink/40 dark:bg-black/60"></div>
<div x-show="selectedWord" x-cloak
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
    @keydown.escape.window="selectedWord = null"
    class="fixed inset-y-0 right-0 z-50 flex w-full max-w-sm flex-col bg-tilawa-surface shadow-2xl dark:bg-[#1c2b30]">
    <template x-if="selectedWord">
        <div class="flex h-full flex-col">
            <div class="flex items-center justify-between border-b border-tilawa-line px-5 py-4 dark:border-white/10">
                <h2 class="text-base font-bold text-tilawa-ink dark:text-[#f3ead9]">Word details</h2>
                <button type="button" @click="selectedWord = null" class="flex h-8 w-8 items-center justify-center rounded-full text-tilawa-sub transition hover:bg-tilawa-line/60 dark:text-[#a9b9bc] dark:hover:bg-white/10">
                    <x-ui.icon name="close" class="h-4 w-4" />
                </button>
            </div>
            <div class="flex flex-col gap-5 overflow-y-auto p-5">
                <div class="rounded-2xl border border-tilawa-line bg-tilawa-paper/60 px-5 py-6 text-center dark:border-white/10 dark:bg-white/5">
                    <div class="font-arabic text-4xl leading-relaxed text-tilawa-ink dark:text-[#f3ead9]" x-text="selectedWord.arabic"></div>
                    <div class="mt-2 text-xs font-semibold uppercase tracking-wide text-tilawa-sub dark:text-[#a9b9bc]" x-show="selectedWord.surahName">
                        <span x-text="selectedWord.surahName"></span> · <span x-text="selectedWord.surah + ':' + selectedWord.ayah"></span> · word <span x-text="selectedWord.position"></span>
                    </div>
                </div>

                <div x-show="selectedWord.transliteration">
                    <div class="text-xs font-bold uppercase tracking-wide text-tilawa-sub dark:text-[#a9b9bc]">Pronunciation</div>
                    <div class="mt-1 text-[15px] italic text-tilawa-ink dark:text-[#f3ead9]" x-text="selectedWord.transliteration"></div>
                </div>

                <div x-show="selectedWord.translation">
                    <div class="text-xs font-bold uppercase tracking-wide text-tilawa-sub dark:text-[#a9b9bc]">Meaning</div>
                    <div class="mt-1 text-[15px] font-semibold text-tilawa-ink dark:text-[#f3ead9]" x-text="selectedWord.translation"></div>
                </div>

                <div x-show="selectedWord.root">
                    <div class="text-xs font-bold uppercase tracking-wide text-tilawa-sub dark:text-[#a9b9bc]">Root</div>
                    <div class="font-arabic mt-1 text-lg text-tilawa-ink dark:text-[#f3ead9]" x-text="selectedWord.root"></div>
                </div>

                <div x-show="selectedWord.grammar">
                    <div class="text-xs font-bold uppercase tracking-wide text-tilawa-sub dark:text-[#a9b9bc]">Grammar</div>
                    <div class="mt-1 text-[15px] text-tilawa-ink dark:text-[#f3ead9]" x-text="selectedWord.grammar"></div>
                </div>

                <div x-show="selectedWord.tajweedRule" class="rounded-xl border border-tilawa-line p-4 dark:border-white/10">
                    <div class="text-xs font-bold uppercase tracking-wide text-tilawa-sub dark:text-[#a9b9bc]">Tajweed rule</div>
                    <div class="mt-2 flex items-center gap-2.5">
                        <span class="h-2.5 w-2.5 shrink-0 rounded-full" x-bind:style="`background-color: ${selectedWord.tajweedRule?.color}`"></span>
                        <div class="text-sm font-bold text-tilawa-ink dark:text-[#f3ead9]" x-text="selectedWord.tajweedRule?.name"></div>
                        <div class="font-arabic text-sm text-tilawa-sub dark:text-[#a9b9bc]" x-text="selectedWord.tajweedRule?.arabicName"></div>
                    </div>
                </div>

                <p class="text-xs text-tilawa-sub dark:text-[#a9b9bc]" x-show="!selectedWord.translation && !selectedWord.transliteration">
                    Word details aren't available for this ayah right now.
                </p>
            </div>
        </div>
    </template>
</div>
