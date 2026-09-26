@props(['backRoute' => null, 'backLabel' => 'Back to Home'])

<div class="mx-auto flex min-h-[60vh] max-w-md flex-col items-center justify-center gap-4 px-6 py-16 text-center">
    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400">
        <x-ui.icon name="alert-triangle" class="h-6 w-6" />
    </div>
    <h2 class="text-lg font-bold text-tilawa-ink">We can't reach the Qur'an service right now</h2>
    <p class="text-sm text-tilawa-sub">
        This usually clears up on its own after a moment. Please try again shortly.
    </p>
    <div class="mt-2 flex items-center gap-3">
        <a href="{{ url()->current() }}" wire:navigate
            class="flex items-center gap-2 rounded-xl bg-tilawa-teal px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:brightness-105">
            <x-ui.icon name="refresh" class="h-4 w-4" />
            Try again
        </a>
        @if ($backRoute)
            <a href="{{ route($backRoute) }}" wire:navigate
                class="rounded-xl border border-tilawa-line px-5 py-3 text-sm font-bold text-tilawa-ink transition hover:bg-tilawa-line/40">
                {{ $backLabel }}
            </a>
        @endif
    </div>
</div>
