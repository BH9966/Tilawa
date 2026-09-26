<div class="flex min-h-screen">
    <x-auth.panel tagline="Welcome back to your daily Tilawa." />

    <div class="relative flex grow items-center justify-center px-6 py-12">
        <x-ui.theme-selector variant="surface" class="absolute right-6 top-6" />
        <form wire:submit="login" class="flex w-full max-w-[400px] flex-col gap-6">
            <div>
                <h1 class="mb-2 text-[30px] font-extrabold text-tilawa-ink">Sign in</h1>
                <p class="text-sm text-tilawa-sub">Continue your journey through the Qur'an.</p>
            </div>

            <div class="flex flex-col gap-4">
                <x-ui.text-field label="Email address" name="email" type="email" icon="mail" wire:model="email"
                    placeholder="you@example.com" autofocus />
                <x-ui.text-field label="Password" name="password" type="password" icon="lock" wire:model="password"
                    placeholder="Enter your password" />
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-tilawa-sub">
                    <input type="checkbox" wire:model="remember"
                        class="rounded border-tilawa-line text-tilawa-teal focus:ring-tilawa-teal">
                    Remember me
                </label>
                <a href="#" class="font-semibold text-tilawa-teal-dark">Forgot password?</a>
            </div>

            <x-ui.button type="submit" variant="primary" class="w-full">
                <span wire:loading.remove wire:target="login">Sign In</span>
                <span wire:loading wire:target="login">Signing in…</span>
                <x-ui.icon name="arrow-right" class="h-[18px] w-[18px]" />
            </x-ui.button>

            <p class="text-center text-sm text-tilawa-sub">
                Don't have an account?
                <a href="{{ route('register') }}" wire:navigate class="font-bold text-tilawa-purple">Create one</a>
            </p>
        </form>
    </div>
</div>
