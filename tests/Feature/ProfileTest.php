<?php

use App\Livewire\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('guests are redirected away from the profile page', function () {
    $response = $this->get(route('profile'));

    $response->assertRedirect(route('login'));
});

test('the profile form is pre-filled with the current user', function () {
    $user = User::factory()->create(['name' => 'Amani Juma', 'email' => 'amani@example.com']);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->assertSet('name', 'Amani Juma')
        ->assertSet('email', 'amani@example.com');
});

test('a user can update their name and email', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('name', 'New Name')
        ->set('email', 'new-email@example.com')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($user->fresh())
        ->name->toBe('New Name')
        ->email->toBe('new-email@example.com');
});

test('a user cannot update their email to one already taken', function () {
    User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('email', 'taken@example.com')
        ->call('updateProfile')
        ->assertHasErrors('email');
});

test('updating the profile with the same email the user already has is allowed', function () {
    $user = User::factory()->create(['email' => 'me@example.com']);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('name', 'Updated Name')
        ->set('email', 'me@example.com')
        ->call('updateProfile')
        ->assertHasNoErrors();
});

test('a user can change their password with the correct current password', function () {
    $user = User::factory()->create(['password' => 'old-password-123']);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('current_password', 'old-password-123')
        ->set('password', 'new-password-456')
        ->set('password_confirmation', 'new-password-456')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('new-password-456', $user->fresh()->password))->toBeTrue();
});

test('changing the password fails with the wrong current password', function () {
    $user = User::factory()->create(['password' => 'old-password-123']);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('current_password', 'totally-wrong')
        ->set('password', 'new-password-456')
        ->set('password_confirmation', 'new-password-456')
        ->call('updatePassword')
        ->assertHasErrors('current_password');

    expect(Hash::check('old-password-123', $user->fresh()->password))->toBeTrue();
});

test('changing the password fails when the confirmation does not match', function () {
    $user = User::factory()->create(['password' => 'old-password-123']);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('current_password', 'old-password-123')
        ->set('password', 'new-password-456')
        ->set('password_confirmation', 'does-not-match')
        ->call('updatePassword')
        ->assertHasErrors('password');
});

test('deleting the account requires the correct password', function () {
    $user = User::factory()->create(['password' => 'my-password-123']);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('confirmingDeletion', true)
        ->set('delete_password', 'wrong-password')
        ->call('deleteAccount')
        ->assertHasErrors('delete_password');

    expect(User::find($user->id))->not->toBeNull();
});

test('a user can permanently delete their account, cascading their bookmarks', function () {
    $user = User::factory()->create(['password' => 'my-password-123']);
    $user->bookmarks()->create([
        'surah_number' => 2,
        'surah_name' => 'Al-Baqarah',
        'ayah_number' => 1,
        'ayah_text' => 'الم',
    ]);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('confirmingDeletion', true)
        ->set('delete_password', 'my-password-123')
        ->call('deleteAccount')
        ->assertRedirect(route('landing'));

    expect(User::find($user->id))->toBeNull();
    expect(\App\Models\Bookmark::where('user_id', $user->id)->count())->toBe(0);
});
