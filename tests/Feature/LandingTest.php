<?php

use App\Livewire\Landing;
use App\Models\User;
use Livewire\Livewire;

test('guests see the landing page with sign in and sign up links', function () {
    Livewire::test(Landing::class)
        ->assertOk()
        ->assertSee('Get Started')
        ->assertSee('Sign in')
        ->assertSee('Sign up');
});

test('the get started button points to the home route', function () {
    Livewire::test(Landing::class)
        ->assertSeeHtml(route('home'));
});

test('authenticated users are redirected straight to home', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Landing::class)
        ->assertRedirect(route('home'));
});
