<?php

namespace App\Livewire\Legal;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Tilawa — Privacy Policy'])]
class Privacy extends Component
{
    public function render()
    {
        return view('livewire.legal.privacy');
    }
}
