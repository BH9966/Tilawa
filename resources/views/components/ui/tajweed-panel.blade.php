{{-- Slide-in panel showing a tapped Tajweed rule's details. Driven entirely
     by the ancestor x-data's `tajweedRule` (set via the `open-tajweed`
     browser event dispatched when a highlighted letter is tapped) — this
     component has no trigger button of its own. --}}
<div x-show="tajweedRule" x-cloak x-transition.opacity.duration.200ms
    @click="tajweedRule = null"
    class="fixed inset-0 z-40 bg-tilawa-ink/40 dark:bg-black/60"></div>

<div x-show="tajweedRule" x-cloak
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
    @keydown.escape.window="tajweedRule = null"
    class="fixed inset-y-0 right-0 z-50 flex w-full max-w-sm flex-col bg-tilawa-surface shadow-2xl dark:bg-[#1c2b30]">
    <template x-if="tajweedRule">
        <div class="flex h-full flex-col">
            <div class="flex items-center justify-between border-b border-tilawa-line px-5 py-4 dark:border-white/10">
                <h2 class="text-base font-bold text-tilawa-ink dark:text-[#f3ead9]">Tajweed rule</h2>
                <button type="button" @click="tajweedRule = null"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-tilawa-sub transition hover:bg-tilawa-line hover:text-tilawa-ink dark:text-[#a9b9bc] dark:hover:bg-white/10 dark:hover:text-[#f3ead9]">
                    <x-ui.icon name="close" class="h-4 w-4" />
                </button>
            </div>

            <div class="flex flex-col gap-4 overflow-y-auto p-5">
                <div class="flex items-center gap-3">
                    <span class="h-3 w-3 shrink-0 rounded-full" x-bind:style="`background-color: ${tajweedRule.color}`"></span>
                    <div>
                        <div class="text-lg font-bold text-tilawa-ink dark:text-[#f3ead9]" x-text="tajweedRule.name"></div>
                        <div class="font-arabic text-xl text-tilawa-sub dark:text-[#a9b9bc]" x-text="tajweedRule.arabicName"></div>
                    </div>
                </div>

                <p class="text-[15px] leading-relaxed text-tilawa-ink dark:text-[#f3ead9]" x-text="tajweedRule.description"></p>

                <a href="https://alquran.cloud/tajweed-guide" target="_blank" rel="noopener"
                    class="flex items-center justify-center gap-2 rounded-xl border border-tilawa-line px-4 py-2.5 text-sm font-bold text-tilawa-teal-dark transition hover:border-tilawa-teal hover:bg-tilawa-teal-light/30 dark:border-white/10 dark:text-tilawa-teal-light dark:hover:bg-tilawa-teal/10">
                    Learn more at Al Quran Cloud's Tajweed guide
                    <x-ui.icon name="arrow-right" class="h-[14px] w-[14px]" />
                </a>
            </div>
        </div>
    </template>
</div>
