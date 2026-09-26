<?php

namespace App\Livewire\Legal;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Tilawa — Terms of Service'])]
class Terms extends Component
{
    public function render()
    {
        return view('livewire.legal.terms');
    }
}
