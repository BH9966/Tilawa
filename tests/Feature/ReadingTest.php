<?php

use App\Livewire\Reading;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

function fakeSurahReadingEndpoints(): void
{
    Http::fake([
        'api.alquran.cloud/v1/surah' => Http::response([
            'code' => 200,
            'status' => 'OK',
            'data' => [
                ['number' => 1, 'name' => 'الفاتحة', 'englishName' => 'Al-Fatihah', 'englishNameTranslation' => 'The Opening', 'numberOfAyahs' => 1, 'revelationType' => 'Meccan'],
                ['number' => 2, 'name' => 'البقرة', 'englishName' => 'Al-Baqarah', 'englishNameTranslation' => 'The Cow', 'numberOfAyahs' => 1, 'revelationType' => 'Medinan'],
            ],
        ]),
        'api.alquran.cloud/v1/surah/1/editions/*' => Http::response([
            'code' => 200,
            'status' => 'OK',
            'data' => [
                [
                    'number' => 1, 'name' => 'الفاتحة', 'englishName' => 'Al-Fatihah',
                    'englishNameTranslation' => 'The Opening', 'revelationType' => 'Meccan', 'numberOfAyahs' => 1,
                    'ayahs' => [
                        ['number' => 1, 'numberInSurah' => 1, 'text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'juz' => 1, 'page' => 1],
                    ],
                ],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'Bismillahir-Rahmanir-Rahim']]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'In the name of Allah, the Entirely Merciful, the Especially Merciful.']]],
            ],
        ]),
        'api.alquran.cloud/v1/surah/2/editions/*' => Http::response([
            'code' => 200,
            'status' => 'OK',
            'data' => [
                [
                    'number' => 2, 'name' => 'البقرة', 'englishName' => 'Al-Baqarah',
                    'englishNameTranslation' => 'The Cow', 'revelationType' => 'Medinan', 'numberOfAyahs' => 1,
                    'ayahs' => [
                        ['number' => 1, 'numberInSurah' => 1, 'text' => 'الم', 'juz' => 1, 'page' => 2],
                    ],
                ],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'Alif-Lam-Meem']]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'Alif, Lam, Meem.']]],
            ],
        ]),
    ]);
}

test('guests are redirected away from the reading page', function () {
    $response = $this->get(route('surah.show', 1));

    $response->assertRedirect(route('login'));
});

test('authenticated users can read a surah in ayat mode', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->assertSet('mode', 'ayat')
        ->assertSee('Al-Fatihah')
        ->assertSee('In the name of Allah, the Entirely Merciful, the Especially Merciful.');
});

test('an invalid surah number returns a 404', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 200])
        ->assertStatus(404);
});

test('the reading mode can be toggled between ayat and mushaf', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->assertSet('mode', 'ayat')
        ->call('setMode', 'mushaf')
        ->assertSet('mode', 'mushaf')
        ->call('setMode', 'not-a-real-mode')
        ->assertSet('mode', 'ayat');
});

test('choosing a surah from the picker navigates to it', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->call('goToSurah', 2)
        ->assertRedirect(route('surah.show', 2));
});

test('the bismillah heading appears before the ayahs for an ordinary surah', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 2])
        ->assertSee('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ');
});

test('the bismillah heading is not duplicated for al-fatihah, whose first ayah is the bismillah', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    $html = Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->html();

    expect(substr_count($html, 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ'))->toBe(1);
});

test('ayah numbers are rendered as arabic-indic numerals', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->assertSee('١');
});

function fakePaginatedSurah(): void
{
    // Surah 6 with 12 ayahs: ayahs 1-6 fall on mushaf page 100, ayahs 7-12 on page 101.
    // Ten ayahs per ayat-mode page means ayah 11 only appears once we move to page 2.
    // Markers use a trailing "-END" so e.g. "MARK-1-END" is never a substring of "MARK-11-END".
    $arabicAyahs = [];
    $transliterationAyahs = [];
    $translationAyahs = [];

    for ($i = 1; $i <= 12; $i++) {
        $arabicAyahs[] = [
            'number' => $i,
            'numberInSurah' => $i,
            // Trailing "X" disambiguates e.g. ayah 1 ("...1X") from ayah 10/11/12 ("...10X"/"...11X"/"...12X").
            'text' => "نص الآية {$i}X",
            'juz' => 1,
            'page' => $i <= 6 ? 100 : 101,
        ];
        $transliterationAyahs[] = ['numberInSurah' => $i, 'text' => "Transliteration {$i}"];
        $translationAyahs[] = ['numberInSurah' => $i, 'text' => "MARK-{$i}-END"];
    }

    Http::fake([
        'api.alquran.cloud/v1/surah/6/editions/*' => Http::response([
            'code' => 200,
            'status' => 'OK',
            'data' => [
                [
                    'number' => 6, 'name' => 'الأنعام', 'englishName' => 'Al-An\'am',
                    'englishNameTranslation' => 'The Cattle', 'revelationType' => 'Meccan', 'numberOfAyahs' => 12,
                    'ayahs' => $arabicAyahs,
                ],
                ['ayahs' => $transliterationAyahs],
                ['ayahs' => $translationAyahs],
            ],
        ]),
    ]);
}

test('ayat mode shows ten ayahs per page for a long surah', function () {
    fakePaginatedSurah();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 6])
        ->assertSet('page', 1)
        ->assertSee('MARK-1-END')
        ->assertSee('MARK-10-END')
        ->assertDontSee('MARK-11-END')
        ->call('nextPage')
        ->assertSet('page', 2)
        ->assertSee('MARK-11-END')
        ->assertSee('MARK-12-END')
        ->assertDontSee('MARK-1-END');
});

test('ayat pagination cannot go past the first or last page', function () {
    fakePaginatedSurah();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 6])
        ->call('previousPage')
        ->assertSet('page', 1)
        ->call('nextPage')
        ->assertSet('page', 2)
        ->call('nextPage')
        ->assertSet('page', 2);
});

test('mushaf mode is chunked by real quran page numbers', function () {
    fakePaginatedSurah();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 6])
        ->call('setMode', 'mushaf')
        ->assertSet('mushafStep', 1)
        ->assertSee('نص الآية 1X')
        ->assertDontSee('نص الآية 7X')
        ->call('nextMushafPage')
        ->assertSet('mushafStep', 2)
        ->assertSee('نص الآية 7X')
        ->assertDontSee('نص الآية 1X');
});

test('a user can bookmark and unbookmark an ayah', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->assertSet('bookmarkedAyahNumbers', [])
        ->call('toggleBookmark', 1);

    expect($user->bookmarks()->count())->toBe(1);
    expect($user->bookmarks()->first())
        ->surah_number->toBe(1)
        ->ayah_number->toBe(1)
        ->surah_name->toBe('Al-Fatihah');

    $component->call('toggleBookmark', 1);

    expect($user->bookmarks()->count())->toBe(0);
});

test('bookmarking an ayah that does not exist in the surah is a no-op', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->call('toggleBookmark', 999);

    expect($user->bookmarks()->count())->toBe(0);
});

test('opening a surah records it as the last-read position', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Reading::class, ['number' => 2]);

    $user->refresh();

    expect($user->last_read_surah)->toBe(2);
    expect($user->last_read_ayah)->toBe(1);
    expect($user->last_read_at)->not->toBeNull();
});

test('paginating updates the last-read ayah to the first ayah on the new page', function () {
    fakePaginatedSurah();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 6])
        ->call('nextPage');

    $user->refresh();

    expect($user->last_read_surah)->toBe(6);
    expect($user->last_read_ayah)->toBe(11);
});

test('a Quran API outage shows a friendly unavailable state instead of crashing', function () {
    $user = User::factory()->create();

    Http::fake([
        'api.alquran.cloud/v1/surah' => Http::response(['status' => 'error'], 503),
    ]);

    $response = $this->actingAs($user)->get(route('surah.show', 1));

    $response->assertOk();
    $response->assertSee("We can't reach the Qur'an service right now");
});

test('a user can switch the translation language and it persists to their account', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create(['translation_language' => 'en']);

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->assertSet('translationLanguage', 'en')
        ->call('setTranslationLanguage', 'ar')
        ->assertSet('translationLanguage', 'ar');

    expect($user->fresh()->translation_language)->toBe('ar');
});

test('a returning user sees their previously chosen translation language', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create(['translation_language' => 'sw']);

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->assertSet('translationLanguage', 'sw');
});

test('switching to an unsupported translation language is ignored', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create(['translation_language' => 'en']);

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->call('setTranslationLanguage', 'fr')
        ->assertSet('translationLanguage', 'en');

    expect($user->fresh()->translation_language)->toBe('en');
});

test('the translation picker lists every supported language with its translator credit', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->assertSee('English')
        ->assertSee('Saheeh International')
        ->assertSee('Arabic')
        ->assertSee('Tafsir al-Muyassar')
        ->assertSee('Swahili')
        ->assertSee('Ali Muhsin Al-Barwani');
});

test('tajweed highlighting is off by default', function () {
    fakeSurahReadingEndpoints();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->assertSet('tajweedEnabled', false)
        ->assertDontSee('data-tajweed-rule', false);
});

test('a user can turn tajweed highlighting on and it persists to their account', function () {
    fakeSurahReadingEndpoints();

    Http::fake([
        'api.quran.com/api/v4/quran/verses/uthmani_tajweed*' => Http::response([
            'verses' => [
                ['verse_key' => '1:1', 'text_uthmani_tajweed' => 'بِسْمِ <tajweed class=ham_wasl>ٱ</tajweed>للَّهِ <span class=end>١</span>'],
            ],
        ]),
    ]);

    $user = User::factory()->create(['tajweed_enabled' => false]);

    Livewire::actingAs($user)
        ->test(Reading::class, ['number' => 1])
        ->call('toggleTajweed')
        ->assertSet('tajweedEnabled', true)
        ->assertSee('data-tajweed-rule="ham_wasl"', false);

    expect($user->fresh()->tajweed_enabled)->toBeTrue();
});

test('tajweed falls back to plain text without breaking the page when its API is unavailable', function () {
    fakeSurahReadingEndpoints();

    Http::fake([
        'api.quran.com/api/v4/quran/verses/uthmani_tajweed*' => Http::response(['message' => 'error'], 503),
    ]);

    $user = User::factory()->create(['tajweed_enabled' => true]);

    $response = $this->actingAs($user)->get(route('surah.show', 1));

    $response->assertOk();
    $response->assertSee('Al-Fatihah');
    $response->assertDontSee('data-tajweed-rule', false);
});
