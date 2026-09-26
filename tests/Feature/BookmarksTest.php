<?php

use App\Livewire\Bookmarks;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected away from the bookmarks page', function () {
    $response = $this->get(route('bookmarks'));

    $response->assertRedirect(route('login'));
});

test('an empty state is shown when the user has no bookmarks', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Bookmarks::class)
        ->assertSee('No bookmarks yet');
});

test('bookmarks are listed grouped by surah', function () {
    $user = User::factory()->create();

    $user->bookmarks()->create([
        'surah_number' => 2,
        'surah_name' => 'Al-Baqarah',
        'ayah_number' => 255,
        'ayah_text' => 'آية الكرسي',
    ]);

    $user->bookmarks()->create([
        'surah_number' => 2,
        'surah_name' => 'Al-Baqarah',
        'ayah_number' => 1,
        'ayah_text' => 'الم',
    ]);

    $user->bookmarks()->create([
        'surah_number' => 36,
        'surah_name' => 'Ya-Sin',
        'ayah_number' => 1,
        'ayah_text' => 'يس',
    ]);

    Livewire::actingAs($user)
        ->test(Bookmarks::class)
        ->assertSee('Al-Baqarah')
        ->assertSee('Ya-Sin')
        ->assertSee('2 ayahs')
        ->assertSee('1 ayah');
});

test('a user can only see their own bookmarks', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $owner->bookmarks()->create([
        'surah_number' => 2,
        'surah_name' => 'Al-Baqarah',
        'ayah_number' => 1,
        'ayah_text' => 'الم',
    ]);

    Livewire::actingAs($other)
        ->test(Bookmarks::class)
        ->assertSee('No bookmarks yet')
        ->assertDontSee('Al-Baqarah');
});

test('a bookmark can be removed', function () {
    $user = User::factory()->create();

    $bookmark = $user->bookmarks()->create([
        'surah_number' => 2,
        'surah_name' => 'Al-Baqarah',
        'ayah_number' => 1,
        'ayah_text' => 'الم',
    ]);

    Livewire::actingAs($user)
        ->test(Bookmarks::class)
        ->call('remove', $bookmark->id);

    expect($user->bookmarks()->count())->toBe(0);
});

test('a user cannot remove another user\'s bookmark', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $bookmark = $owner->bookmarks()->create([
        'surah_number' => 2,
        'surah_name' => 'Al-Baqarah',
        'ayah_number' => 1,
        'ayah_text' => 'الم',
    ]);

    Livewire::actingAs($other)
        ->test(Bookmarks::class)
        ->call('remove', $bookmark->id);

    expect($owner->bookmarks()->count())->toBe(1);
});
