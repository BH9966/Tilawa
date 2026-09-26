<x-layouts.app title="Tilawa — Dashboard">
    <div class="mx-auto flex max-w-2xl flex-col items-center gap-4 px-6 py-24 text-center">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-tilawa-mint text-tilawa-purple">
            <x-ui.icon name="user" class="h-7 w-7" />
        </div>
        <h1 class="text-2xl font-extrabold text-tilawa-ink">Welcome, {{ auth()->user()->name }}</h1>
        <p class="max-w-md text-sm leading-relaxed text-tilawa-sub">
            You're signed in and Tilawa's authentication is fully wired up. The Surah list and reading experience
            (Phase 4) land next — this page confirms accounts, sessions and the design system all work end-to-end.
        </p>
    </div>
</x-layouts.app>
