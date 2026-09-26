@props(['class' => '', 'variant' => 'header'])

{{--
    Site-wide Light / Night toggle. Only two states — no "System" option.
    Toggling flips `.dark` on <html> immediately (every token in app.css is
    keyed off that class, so every page reacts at once) and remembers the
    choice in localStorage so it's restored on return. The very first time
    a browser visits with nothing saved yet, the initial state instead
    follows the OS's prefers-color-scheme (see the boot script in
    layouts/app.blade.php) — this control simply takes over from then on.
--}}
<div
    x-data="{
        dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('tilawa-theme', this.dark ? 'dark' : 'light');
        },
    }"
    class="{{ $class }}"
>
    @if ($variant === 'surface')
        <button
            type="button"
            @click="toggle()"
            title="Switch theme"
            class="flex items-center gap-2 rounded-full border border-tilawa-line bg-tilawa-surface px-3.5 py-1.5 text-sm font-semibold text-tilawa-ink transition hover:border-tilawa-teal"
        >
            <span x-show="!dark"><x-ui.icon name="sun" class="h-4 w-4" /></span>
            <span x-show="dark" x-cloak><x-ui.icon name="moon" class="h-4 w-4" /></span>
            <span x-text="dark ? 'Night' : 'Light'"></span>
        </button>
    @else
        <button
            type="button"
            @click="toggle()"
            title="Switch theme"
            class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25"
        >
            <span x-show="!dark"><x-ui.icon name="sun" class="h-[17px] w-[17px]" /></span>
            <span x-show="dark" x-cloak><x-ui.icon name="moon" class="h-[17px] w-[17px]" /></span>
        </button>
    @endif
</div>
