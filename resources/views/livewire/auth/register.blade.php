<div class="flex min-h-screen">
    <x-auth.panel tagline="Join thousands reading with Tilawa." />

    <div class="relative flex grow items-center justify-center px-6 py-12">
        <x-ui.theme-selector variant="surface" class="absolute right-6 top-6" />
        <form wire:submit="register" class="flex w-full max-w-[400px] flex-col gap-5">
            <div>
                <h1 class="mb-2 text-[30px] font-extrabold text-tilawa-ink">Create your account</h1>
                <p class="text-sm text-tilawa-sub">Start your daily reading habit today.</p>
            </div>

            <div class="flex flex-col gap-4">
                <x-ui.text-field label="Full name" name="name" type="text" icon="user" wire:model="name"
                    placeholder="Ahmad Yahya" autofocus />
                <x-ui.text-field label="Email address" name="email" type="email" icon="mail" wire:model="email"
                    placeholder="you@example.com" />
                <x-ui.text-field label="Password" name="password" type="password" icon="lock" wire:model="password"
                    placeholder="Create a password" />
                <x-ui.text-field label="Confirm password" name="password_confirmation" type="password" icon="lock"
                    wire:model="password_confirmation" placeholder="Repeat your password" />
            </div>

            <x-ui.button type="submit" variant="primary" class="mt-1 w-full">
                <span wire:loading.remove wire:target="register">Create Account</span>
                <span wire:loading wire:target="register">Creating account…</span>
                <x-ui.icon name="arrow-right" class="h-[18px] w-[18px]" />
            </x-ui.button>

            <p class="text-center text-sm text-tilawa-sub">
                Already have an account?
                <a href="{{ route('login') }}" wire:navigate class="font-bold text-tilawa-purple">Sign in</a>
            </p>
        </form>
    </div>
</div>
