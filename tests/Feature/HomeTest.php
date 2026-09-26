<?php

use App\Livewire\Home;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

function fakeSurahList(): void
{
    Http::fake([
        'api.alquran.cloud/v1/surah' => Http::response([
            'code' => 200,
            'status' => 'OK',
            'data' => [
                ['number' => 1, 'name' => 'الفاتحة', 'englishName' => 'Al-Fatihah', 'englishNameTranslation' => 'The Opening', 'numberOfAyahs' => 7, 'revelationType' => 'Meccan'],
                ['number' => 2, 'name' => 'البقرة', 'englishName' => 'Al-Baqarah', 'englishNameTranslation' => 'The Cow', 'numberOfAyahs' => 286, 'revelationType' => 'Medinan'],
                ['number' => 18, 'name' => 'الكهف', 'englishName' => 'Al-Kahf', 'englishNameTranslation' => 'The Cave', 'numberOfAyahs' => 110, 'revelationType' => 'Meccan'],
            ],
        ]),
    ]);
}

test('guests are redirected away from the home page', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can see the surah list', function () {
    fakeSurahList();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Home::class)
        ->assertSee('Al-Fatihah')
        ->assertSee('Al-Baqarah');
});

test('the surah list can be searched', function () {
    fakeSurahList();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Home::class)
        ->set('search', 'Kahf')
        ->assertSee('Al-Kahf')
        ->assertDontSee('Al-Fatihah');
});

test('the surah sort order can be toggled', function () {
    fakeSurahList();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Home::class)
        ->assertSet('sort', 'asc')
        ->call('toggleSort')
        ->assertSet('sort', 'desc');
});

test('switching tabs shows juz and page grids', function () {
    fakeSurahList();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Home::class)
        ->call('setTab', 'juz')
        ->assertSet('tab', 'juz')
        ->assertSee('Juz')
        ->call('setTab', 'page')
        ->assertSet('tab', 'page');
});

test('surah numbers are rendered as arabic-indic numerals', function () {
    fakeSurahList();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Home::class)
        ->assertSee('١');
});

test('the continue-reading banner is hidden when the user has no last-read position', function () {
    fakeSurahList();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Home::class)
        ->assertDontSee('Continue reading');
});

test('the continue-reading banner shows the last-read surah and ayah', function () {
    fakeSurahList();

    $user = User::factory()->create([
        'last_read_surah' => 18,
        'last_read_ayah' => 5,
    ]);

    Livewire::actingAs($user)
        ->test(Home::class)
        ->assertSee('Continue reading')
        ->assertSee('Al-Kahf')
        ->assertSee('Ayah 5');
});

test('a Quran API outage shows a friendly unavailable state instead of crashing', function () {
    $user = User::factory()->create();

    Http::fake([
        'api.alquran.cloud/v1/surah' => Http::response(['status' => 'error'], 503),
    ]);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
    $response->assertSee("We can't reach the Qur'an service right now");
});

test('the juz tab links each juz number to its reading page', function () {
    fakeSurahList();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Home::class)
        ->call('setTab', 'juz')
        ->assertSeeHtml(route('juz.show', 1))
        ->assertSeeHtml(route('juz.show', 30));
});
