<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Landing extends Component
{
    public function mount(): void
    {
        // Signed-in users don't need the marketing page — take them straight to their reading list.
        if (Auth::check()) {
            $this->redirectRoute('home', navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.landing')->layout('layouts.app', [
            'title' => 'Tilawa — Your daily companion for the Qur\'an',
        ]);
    }
}
