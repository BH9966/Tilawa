@props(['title', 'triggerLabel', 'triggerIcon' => null, 'searchPlaceholder' => null])

<div class="relative" x-data="{ open: false, query: '' }">
    <button type="button" @click="open = true"
        class="flex items-center gap-2.5 rounded-xl border border-tilawa-line bg-tilawa-surface py-2.5 pl-4 pr-3.5 text-sm font-bold text-tilawa-ink shadow-sm transition hover:border-tilawa-teal dark:border-white/10 dark:bg-[#1c2b30] dark:text-[#f3ead9] dark:hover:border-tilawa-teal">
        @if ($triggerIcon)
            <x-ui.icon :name="$triggerIcon" class="h-4 w-4 shrink-0 text-tilawa-sub" />
        @endif
        <span class="truncate">{{ $triggerLabel }}</span>
        <x-ui.icon name="chevron-down" class="h-[14px] w-[14px] shrink-0 text-tilawa-sub" />
    </button>

    {{-- Backdrop --}}
    <div x-show="open" x-cloak x-transition.opacity.duration.200ms
        @click="open = false"
        class="fixed inset-0 z-40 bg-tilawa-ink/40 dark:bg-black/60"></div>

    {{-- Drawer panel, slides in from the right --}}
    <div x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        @keydown.escape.window="open = false"
        x-init="$watch('open', value => { if (value) { $nextTick(() => $refs.drawerSearch?.focus()) } else { query = '' } })"
        class="fixed inset-y-0 right-0 z-50 flex w-full max-w-sm flex-col bg-tilawa-surface shadow-2xl dark:bg-[#1c2b30]">
        <div class="flex items-center justify-between border-b border-tilawa-line px-5 py-4 dark:border-white/10">
            <h2 class="text-base font-bold text-tilawa-ink dark:text-[#f3ead9]">{{ $title }}</h2>
            <button type="button" @click="open = false"
                class="flex h-8 w-8 items-center justify-center rounded-full text-tilawa-sub transition hover:bg-tilawa-line hover:text-tilawa-ink dark:text-[#a9b9bc] dark:hover:bg-white/10 dark:hover:text-[#f3ead9]">
                <x-ui.icon name="close" class="h-4 w-4" />
            </button>
        </div>

        @if ($searchPlaceholder)
            <div class="border-b border-tilawa-line p-3 dark:border-white/10">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-tilawa-sub/60" />
                    <input type="text" x-model="query" x-ref="drawerSearch" placeholder="{{ $searchPlaceholder }}"
                        class="w-full rounded-lg border-none bg-tilawa-paper/70 py-2.5 pl-9 pr-3 text-sm text-tilawa-ink placeholder:text-tilawa-sub/60 focus:ring-1 focus:ring-tilawa-teal dark:bg-white/5 dark:text-[#f3ead9] dark:placeholder:text-[#a9b9bc]/60">
                </div>
            </div>
        @endif

        <div class="grow overflow-y-auto p-3">
            {{ $slot }}
        </div>
    </div>
</div>
