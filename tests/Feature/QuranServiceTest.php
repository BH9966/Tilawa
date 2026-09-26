<?php

use App\Services\QuranService;
use Illuminate\Support\Facades\Http;

test('it fetches and caches the surah list', function () {
    Http::fake([
        'api.alquran.cloud/v1/surah' => Http::response([
            'code' => 200,
            'status' => 'OK',
            'data' => [
                [
                    'number' => 1,
                    'name' => 'سُورَةُ ٱلْفَاتِحَةِ',
                    'englishName' => 'Al-Fatihah',
                    'englishNameTranslation' => 'The Opening',
                    'numberOfAyahs' => 7,
                    'revelationType' => 'Meccan',
                ],
            ],
        ]),
    ]);

    $surahs = app(QuranService::class)->getSurahList();

    expect($surahs)->toHaveCount(1);
    expect($surahs[0]['englishName'])->toBe('Al-Fatihah');

    // A second call must not hit the API again — it should be served from cache.
    app(QuranService::class)->getSurahList();
    Http::assertSentCount(1);
});

test('it merges arabic, transliteration and translation for a surah', function () {
    Http::fake([
        'api.alquran.cloud/v1/surah/1/editions/*' => Http::response([
            'code' => 200,
            'status' => 'OK',
            'data' => [
                [
                    'number' => 1,
                    'name' => 'سُورَةُ ٱلْفَاتِحَةِ',
                    'englishName' => 'Al-Fatihah',
                    'englishNameTranslation' => 'The Opening',
                    'revelationType' => 'Meccan',
                    'numberOfAyahs' => 1,
                    'ayahs' => [
                        ['number' => 1, 'numberInSurah' => 1, 'text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'juz' => 1, 'page' => 1],
                    ],
                ],
                [
                    'ayahs' => [
                        ['numberInSurah' => 1, 'text' => "Bismillahir-Rahmanir-Rahim"],
                    ],
                ],
                [
                    'ayahs' => [
                        ['numberInSurah' => 1, 'text' => 'In the name of Allah, the Entirely Merciful, the Especially Merciful.'],
                    ],
                ],
            ],
        ]),
    ]);

    $surah = app(QuranService::class)->getSurah(1);

    expect($surah['englishName'])->toBe('Al-Fatihah');
    expect($surah['ayahs'])->toHaveCount(1);
    expect($surah['ayahs'][0]['arabic'])->toBe('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ');
    expect($surah['ayahs'][0]['transliteration'])->toBe('Bismillahir-Rahmanir-Rahim');
    expect($surah['ayahs'][0]['translation'])->toBe('In the name of Allah, the Entirely Merciful, the Especially Merciful.');
});

test('it strips the leading bismillah from ayah 1 of surahs other than al-fatihah', function () {
    Http::fake([
        'api.alquran.cloud/v1/surah/2/editions/*' => Http::response([
            'code' => 200,
            'status' => 'OK',
            'data' => [
                [
                    'number' => 2,
                    'name' => 'سُورَةُ البَقَرَة',
                    'englishName' => 'Al-Baqarah',
                    'englishNameTranslation' => 'The Cow',
                    'revelationType' => 'Medinan',
                    'numberOfAyahs' => 1,
                    'ayahs' => [
                        [
                            'number' => 8,
                            'numberInSurah' => 1,
                            'text' => 'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ الٓمٓ',
                            'juz' => 1,
                            'page' => 2,
                        ],
                    ],
                ],
                [
                    'ayahs' => [
                        ['numberInSurah' => 1, 'text' => 'Alif-Laaam-Meeem'],
                    ],
                ],
                [
                    'ayahs' => [
                        ['numberInSurah' => 1, 'text' => 'Alif, Lam, Meem.'],
                    ],
                ],
            ],
        ]),
    ]);

    $surah = app(QuranService::class)->getSurah(2);

    expect($surah['ayahs'][0]['arabic'])->toBe('الٓمٓ');
    // The translation/transliteration editions never carried the bismillah, so they're untouched.
    expect($surah['ayahs'][0]['translation'])->toBe('Alif, Lam, Meem.');
});

test('it does not strip anything from al-fatihah, whose first ayah is the bismillah itself', function () {
    Http::fake([
        'api.alquran.cloud/v1/surah/1/editions/*' => Http::response([
            'code' => 200,
            'status' => 'OK',
            'data' => [
                [
                    'number' => 1,
                    'name' => 'سُورَةُ ٱلْفَاتِحَةِ',
                    'englishName' => 'Al-Fatihah',
                    'englishNameTranslation' => 'The Opening',
                    'revelationType' => 'Meccan',
                    'numberOfAyahs' => 1,
                    'ayahs' => [
                        ['number' => 1, 'numberInSurah' => 1, 'text' => 'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ', 'juz' => 1, 'page' => 1],
                    ],
                ],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'Bismillahir-Rahmanir-Rahim']]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'In the name of Allah, the Entirely Merciful, the Especially Merciful.']]],
            ],
        ]),
    ]);

    $surah = app(QuranService::class)->getSurah(1);

    expect($surah['ayahs'][0]['arabic'])->toBe('بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ');
});

test('a failed surah list response throws a QuranApiException without caching the failure', function () {
    Http::fake([
        'api.alquran.cloud/v1/surah' => Http::response(['status' => 'error'], 500),
    ]);

    expect(fn () => app(QuranService::class)->getSurahList())
        ->toThrow(\App\Exceptions\QuranApiException::class);

    // Nothing should have been cached from the failed attempt, so a
    // successful retry must hit the API again and succeed.
    Http::fake([
        'api.alquran.cloud/v1/surah' => Http::response([
            'code' => 200,
            'status' => 'OK',
            'data' => [
                [
                    'number' => 1,
                    'name' => 'سُورَةُ ٱلْفَاتِحَةِ',
                    'englishName' => 'Al-Fatihah',
                    'englishNameTranslation' => 'The Opening',
                    'numberOfAyahs' => 7,
                    'revelationType' => 'Meccan',
                ],
            ],
        ]),
    ]);

    $surahs = app(QuranService::class)->getSurahList();
    expect($surahs)->toHaveCount(1);
});

test('a connection failure fetching the surah list throws a QuranApiException', function () {
    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
    });

    expect(fn () => app(QuranService::class)->getSurahList())
        ->toThrow(\App\Exceptions\QuranApiException::class);
});

test('a failed surah response throws a QuranApiException', function () {
    Http::fake([
        'api.alquran.cloud/v1/surah/1/editions/*' => Http::response(['status' => 'error'], 503),
    ]);

    expect(fn () => app(QuranService::class)->getSurah(1))
        ->toThrow(\App\Exceptions\QuranApiException::class);
});

test('getSurah requests the matching translation edition per language and caches them separately', function () {
    Http::fake([
        'api.alquran.cloud/v1/surah/1/editions/quran-uthmani,en.transliteration,en.sahih' => Http::response([
            'code' => 200, 'status' => 'OK',
            'data' => [
                ['number' => 1, 'name' => 'الفاتحة', 'englishName' => 'Al-Fatihah', 'englishNameTranslation' => 'The Opening', 'revelationType' => 'Meccan', 'numberOfAyahs' => 1, 'ayahs' => [['number' => 1, 'numberInSurah' => 1, 'text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'juz' => 1, 'page' => 1]]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'Bismillah']]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'In the name of Allah.']]],
            ],
        ]),
        'api.alquran.cloud/v1/surah/1/editions/quran-uthmani,en.transliteration,ar.muyassar' => Http::response([
            'code' => 200, 'status' => 'OK',
            'data' => [
                ['number' => 1, 'name' => 'الفاتحة', 'englishName' => 'Al-Fatihah', 'englishNameTranslation' => 'The Opening', 'revelationType' => 'Meccan', 'numberOfAyahs' => 1, 'ayahs' => [['number' => 1, 'numberInSurah' => 1, 'text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'juz' => 1, 'page' => 1]]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'Bismillah']]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'بسم الله']]],
            ],
        ]),
        'api.alquran.cloud/v1/surah/1/editions/quran-uthmani,en.transliteration,sw.barwani' => Http::response([
            'code' => 200, 'status' => 'OK',
            'data' => [
                ['number' => 1, 'name' => 'الفاتحة', 'englishName' => 'Al-Fatihah', 'englishNameTranslation' => 'The Opening', 'revelationType' => 'Meccan', 'numberOfAyahs' => 1, 'ayahs' => [['number' => 1, 'numberInSurah' => 1, 'text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'juz' => 1, 'page' => 1]]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'Bismillah']]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'Kwa jina la Mwenyezi Mungu.']]],
            ],
        ]),
    ]);

    $en = app(QuranService::class)->getSurah(1, 'en');
    $ar = app(QuranService::class)->getSurah(1, 'ar');
    $sw = app(QuranService::class)->getSurah(1, 'sw');

    expect($en['ayahs'][0]['translation'])->toBe('In the name of Allah.');
    expect($ar['ayahs'][0]['translation'])->toBe('بسم الله');
    expect($sw['ayahs'][0]['translation'])->toBe('Kwa jina la Mwenyezi Mungu.');

    // Each language has its own cache entry, so re-requesting any of them
    // must not hit the API again.
    app(QuranService::class)->getSurah(1, 'en');
    app(QuranService::class)->getSurah(1, 'ar');
    app(QuranService::class)->getSurah(1, 'sw');
    Http::assertSentCount(3);
});

test('an unsupported translation language falls back to English', function () {
    Http::fake([
        'api.alquran.cloud/v1/surah/1/editions/quran-uthmani,en.transliteration,en.sahih' => Http::response([
            'code' => 200, 'status' => 'OK',
            'data' => [
                ['number' => 1, 'name' => 'الفاتحة', 'englishName' => 'Al-Fatihah', 'englishNameTranslation' => 'The Opening', 'revelationType' => 'Meccan', 'numberOfAyahs' => 1, 'ayahs' => [['number' => 1, 'numberInSurah' => 1, 'text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'juz' => 1, 'page' => 1]]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'Bismillah']]],
                ['ayahs' => [['numberInSurah' => 1, 'text' => 'In the name of Allah.']]],
            ],
        ]),
    ]);

    $surah = app(QuranService::class)->getSurah(1, 'fr');

    expect($surah['ayahs'][0]['translation'])->toBe('In the name of Allah.');
});

test('getJuz merges arabic, transliteration and translation across a surah boundary', function () {
    Http::fake([
        'api.alquran.cloud/v1/juz/1/quran-uthmani' => Http::response([
            'code' => 200, 'status' => 'OK',
            'data' => [
                'number' => 1,
                'ayahs' => [
                    ['number' => 1, 'text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'numberInSurah' => 1, 'juz' => 1, 'page' => 1, 'surah' => ['number' => 1, 'name' => 'الفاتحة', 'englishName' => 'Al-Fatihah', 'englishNameTranslation' => 'The Opening', 'numberOfAyahs' => 7, 'revelationType' => 'Meccan']],
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
                ['text' => 'In the name of Allah.'],
                ['text' => 'Alif, Lam, Meem.'],
            ]],
        ]),
    ]);

    $juz = app(QuranService::class)->getJuz(1);

    expect($juz['ayahs'])->toHaveCount(2);
    expect($juz['ayahs'][0]['surahName'])->toBe('Al-Fatihah');
    expect($juz['ayahs'][0]['arabic'])->toBe('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ');

    // Ayah 1 of Al-Baqarah (a surah other than Al-Fatihah) has its embedded
    // Bismillah stripped, same rule as getSurah().
    expect($juz['ayahs'][1]['surahName'])->toBe('Al-Baqarah');
    expect($juz['ayahs'][1]['arabic'])->toBe('الم');
    expect($juz['ayahs'][1]['translation'])->toBe('Alif, Lam, Meem.');
});

test('a failed juz request throws a QuranApiException', function () {
    Http::fake([
        'api.alquran.cloud/v1/juz/*' => Http::response(['status' => 'error'], 503),
    ]);

    expect(fn () => app(QuranService::class)->getJuz(1))
        ->toThrow(\App\Exceptions\QuranApiException::class);
});
