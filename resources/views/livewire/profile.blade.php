<div>
    {{-- Header --}}
    <div class="rounded-b-[2.5rem] bg-tilawa-teal px-6 pb-8 pt-6 md:px-12">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" wire:navigate
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25">
                    <x-ui.icon name="arrow-left" class="h-[18px] w-[18px]" />
                </a>
                <div class="text-lg font-bold text-white">Profile &amp; Settings</div>
            </div>
            <x-ui.theme-selector />
        </div>

        <div class="mx-auto mt-6 flex max-w-2xl flex-col items-center gap-3 text-center">
            <span class="flex h-16 w-16 items-center justify-center rounded-full border-2 border-white/30 bg-white/15 text-xl font-extrabold text-white">
                {{ collect(explode(' ', auth()->user()->name))->map(fn ($n) => strtoupper($n[0]))->take(2)->implode('') }}
            </span>
            <div>
                <div class="text-base font-bold text-white">{{ auth()->user()->name }}</div>
                <div class="text-xs text-white/75">
                    Member since {{ auth()->user()->created_at->format('F Y') }}
                    &middot; {{ $bookmarkCount }} {{ \Illuminate\Support\Str::plural('bookmark', $bookmarkCount) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="px-6 py-8 md:px-12">
        <div class="mx-auto flex max-w-xl flex-col gap-6">

            {{-- Appearance --}}
            <div class="rounded-2xl border border-tilawa-line bg-tilawa-surface p-6">
                <h2 class="text-base font-bold text-tilawa-ink">Appearance</h2>
                <p class="mt-1 text-sm text-tilawa-sub">Choose how Tilawa looks on this device.</p>
                <div class="mt-4">
                    <x-ui.theme-selector variant="surface" />
                </div>
            </div>

            {{-- Profile information --}}
            <div class="rounded-2xl border border-tilawa-line bg-tilawa-surface p-6">
                <h2 class="text-base font-bold text-tilawa-ink">Profile information</h2>
                <p class="mt-1 text-sm text-tilawa-sub">Update your name and email address.</p>

                @if (session('profile-updated'))
                    <div class="mt-4 flex items-center gap-2 rounded-xl bg-tilawa-mint/40 px-4 py-2.5 text-sm font-semibold text-tilawa-teal-dark">
                        <x-ui.icon name="check-circle" class="h-[16px] w-[16px] shrink-0" />
                        Your profile has been updated.
                    </div>
                @endif

                <form wire:submit="updateProfile" class="mt-4 flex flex-col gap-4">
                    <x-ui.text-field label="Full name" name="name" type="text" icon="user" wire:model="name" />
                    <x-ui.text-field label="Email address" name="email" type="email" icon="mail" wire:model="email" />

                    <x-ui.button type="submit" variant="primary" class="self-start">
                        <span wire:loading.remove wire:target="updateProfile">Save changes</span>
                        <span wire:loading wire:target="updateProfile">Saving…</span>
                    </x-ui.button>
                </form>
            </div>

            {{-- Change password --}}
            <div class="rounded-2xl border border-tilawa-line bg-tilawa-surface p-6">
                <h2 class="text-base font-bold text-tilawa-ink">Change password</h2>
                <p class="mt-1 text-sm text-tilawa-sub">Use a strong password you don't use elsewhere.</p>

                @if (session('password-updated'))
                    <div class="mt-4 flex items-center gap-2 rounded-xl bg-tilawa-mint/40 px-4 py-2.5 text-sm font-semibold text-tilawa-teal-dark">
                        <x-ui.icon name="check-circle" class="h-[16px] w-[16px] shrink-0" />
                        Your password has been changed.
                    </div>
                @endif

                <form wire:submit="updatePassword" class="mt-4 flex flex-col gap-4">
                    <x-ui.text-field label="Current password" name="current_password" type="password" icon="lock"
                        wire:model="current_password" />
                    <x-ui.text-field label="New password" name="password" type="password" icon="lock"
                        wire:model="password" />
                    <x-ui.text-field label="Confirm new password" name="password_confirmation" type="password" icon="lock"
                        wire:model="password_confirmation" />

                    <x-ui.button type="submit" variant="primary" class="self-start">
                        <span wire:loading.remove wire:target="updatePassword">Update password</span>
                        <span wire:loading wire:target="updatePassword">Updating…</span>
                    </x-ui.button>
                </form>
            </div>

            {{-- Danger zone --}}
            <div class="rounded-2xl border border-red-200 bg-red-50/50 p-6">
                <h2 class="text-base font-bold text-red-600">Delete account</h2>
                <p class="mt-1 text-sm text-red-600/80">
                    This permanently deletes your account and every bookmark you've saved. This cannot be undone.
                </p>

                @if (! $confirmingDeletion)
                    <button type="button" wire:click="confirmDeletion"
                        class="mt-4 flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">
                        <x-ui.icon name="trash" class="h-[16px] w-[16px]" />
                        Delete my account
                    </button>
                @else
                    <form wire:submit="deleteAccount" class="mt-4 flex flex-col gap-4">
                        <x-ui.text-field label="Confirm your password to continue" name="delete_password" type="password"
                            icon="lock" wire:model="delete_password" placeholder="Your current password" />

                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">
                                <span wire:loading.remove wire:target="deleteAccount">Yes, permanently delete</span>
                                <span wire:loading wire:target="deleteAccount">Deleting…</span>
                            </button>
                            <button type="button" wire:click="cancelDeletion"
                                class="rounded-xl bg-tilawa-surface px-5 py-2.5 text-sm font-bold text-tilawa-sub shadow-sm transition hover:text-tilawa-ink">
                                Cancel
                            </button>
                        </div>
                    </form>
                @endif
            </div>

        </div>
    </div>
</div>
