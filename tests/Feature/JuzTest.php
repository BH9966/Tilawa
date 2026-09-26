<?php

use App\Livewire\Juz;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

function fakeJuzEndpoints(): void
{
    Http::fake([
        'api.alquran.cloud/v1/juz/1/quran-uthmani' => Http::response([
            'code' => 200, 'status' => 'OK',
            'data' => [
                'number' => 1,
                'ayahs' => [
                    ['number' => 1, 'text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'numberInSurah' => 1, 'juz' => 1, 'page' => 1, 'surah' => ['number' => 1, 'name' => 'الفاتحة', 'englishName' => 'Al-Fatihah', 'englishNameTranslation' => 'The Opening', 'numberOfAyahs' => 1, 'revelationType' => 'Meccan']],
                    ['number' => 8, 'text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ الم', 'numberInSurah' => 1, 'juz' => 1, 'page' => 2, 'surah' => ['number' => 2, 'name' => 'البقرة', 'englishName' => 'Al-Baqarah', 'englishNameTranslation' => 'The Cow', 'numberOfAyahs' => 286, 'revelationType' => 'Medinan']],
                ],
            ],
        ]),
        'api.alquran.cloud/v1/juz/1/en.transliteration' => Http::response([
            'code' => 200, 'status' => 'OK',
            'data' => ['number' => 1, 'ayahs' => [
                ['text' => 'Bismillah'],
                ['text' => 'Alif-Lam-Meem'],
            ]],
        ]),
        'api.alquran.cloud/v1/juz/1/en.sahih' => Http::response([
            'code' => 200, 'status' => 'OK',
            'data' => ['number' => 1, 'ayahs' => [
                ['text' => 'In the name of Allah, the Entirely Merciful, the Especially Merciful.'],
                ['text' => 'Alif, Lam, Meem.'],
            ]],
        ]),
    ]);
}

test('guests are redirected away from the juz page', function () {
    $response = $this->get(route('juz.show', 1));

    $response->assertRedirect(route('login'));
});

test('an invalid juz number returns a 404', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Juz::class, ['number' => 31])
        ->assertStatus(404);
});

test('authenticated users can read a juz spanning two surahs', function () {
    fakeJuzEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Juz::class, ['number' => 1])
        ->assertSee('Juz 1')
        ->assertSee('Al-Fatihah')
        ->assertSee('Al-Baqarah')
        ->assertSee('In the name of Allah, the Entirely Merciful, the Especially Merciful.')
        ->assertSee('Alif, Lam, Meem.');
});

test('opening a juz records the first ayah as the last-read position', function () {
    fakeJuzEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Juz::class, ['number' => 1]);

    $user->refresh();

    expect($user->last_read_surah)->toBe(1);
    expect($user->last_read_ayah)->toBe(1);
});

test('a user can bookmark an ayah while reading a juz', function () {
    fakeJuzEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Juz::class, ['number' => 1])
        ->call('toggleBookmark', 2, 1);

    expect($user->bookmarks()->where('surah_number', 2)->where('ayah_number', 1)->exists())->toBeTrue();
});

test('a user can switch the translation language while reading a juz', function () {
    fakeJuzEndpoints();

    $user = User::factory()->create(['translation_language' => 'en']);

    Livewire::actingAs($user)
        ->test(Juz::class, ['number' => 1])
        ->call('setTranslationLanguage', 'sw')
        ->assertSet('translationLanguage', 'sw');

    expect($user->fresh()->translation_language)->toBe('sw');
});

test('a Quran API outage shows a friendly unavailable state instead of crashing', function () {
    $user = User::factory()->create();

    Http::fake([
        'api.alquran.cloud/v1/juz/*' => Http::response(['status' => 'error'], 503),
    ]);

    $response = $this->actingAs($user)->get(route('juz.show', 1));

    $response->assertOk();
    $response->assertSee("We can't reach the Qur'an service right now");
});

test('tajweed highlighting is off by default on a juz page', function () {
    fakeJuzEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Juz::class, ['number' => 1])
        ->assertSet('tajweedEnabled', false)
        ->assertDontSee('data-tajweed-rule', false);
});

test('a user can turn tajweed highlighting on while reading a juz', function () {
    fakeJuzEndpoints();

    Http::fake([
        'api.quran.com/api/v4/quran/verses/uthmani_tajweed*' => Http::response([
            'verses' => [
                ['verse_key' => '1:1', 'text_uthmani_tajweed' => 'بِسْمِ <tajweed class=ham_wasl>ٱ</tajweed>للَّهِ <span class=end>١</span>'],
                ['verse_key' => '2:1', 'text_uthmani_tajweed' => 'الم <span class=end>١</span>'],
            ],
        ]),
    ]);

    $user = User::factory()->create(['tajweed_enabled' => false]);

    Livewire::actingAs($user)
        ->test(Juz::class, ['number' => 1])
        ->call('toggleTajweed')
        ->assertSet('tajweedEnabled', true)
        ->assertSee('data-tajweed-rule="ham_wasl"', false);

    expect($user->fresh()->tajweed_enabled)->toBeTrue();
});
