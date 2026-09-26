<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Tilawa — Profile & Settings'])]
class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $confirmingDeletion = false;

    public string $delete_password = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfile(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($validated);

        session()->flash('profile-updated');
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('password-updated');
    }

    public function confirmDeletion(): void
    {
        $this->confirmingDeletion = true;
    }

    public function cancelDeletion(): void
    {
        $this->confirmingDeletion = false;
        $this->delete_password = '';
        $this->resetErrorBag('delete_password');
    }

    public function deleteAccount(): void
    {
        $this->validate([
            'delete_password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        $user->delete();

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirectRoute('landing', navigate: true);
    }

    public function render()
    {
        // Guarded: after deleteAccount() calls Auth::logout(), render() may still
        // run once more before the redirect takes effect, with no user left to query.
        $user = Auth::user();

        return view('livewire.profile', [
            'bookmarkCount' => $user ? $user->bookmarks()->count() : 0,
        ]);
    }
}
