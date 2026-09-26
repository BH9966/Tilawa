<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Tilawa — Bookmarks'])]
class Bookmarks extends Component
{
    public function remove(int $bookmarkId): void
    {
        Auth::user()->bookmarks()->whereKey($bookmarkId)->delete();
    }

    /** Bookmarks grouped by surah number, each surah's ayahs ordered ascending. */
    #[Computed]
    public function bookmarksBySurah(): Collection
    {
        return Auth::user()->bookmarks()
            ->orderBy('surah_number')
            ->orderBy('ayah_number')
            ->get()
            ->groupBy('surah_number');
    }

    public function render()
    {
        return view('livewire.bookmarks');
    }
}
