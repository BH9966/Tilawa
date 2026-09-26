<?php

use App\Services\TajweedService;
use Illuminate\Support\Facades\Http;

test('getSurahTajweed converts tajweed tags into class-based spans keyed by ayah number', function () {
    Http::fake([
        'api.quran.com/api/v4/quran/verses/uthmani_tajweed*' => Http::response([
            'verses' => [
                [
                    'verse_key' => '1:1',
                    'text_uthmani_tajweed' => 'بِسْمِ <tajweed class=ham_wasl>ٱ</tajweed>للَّهِ <span class=end>١</span>',
                ],
                [
                    'verse_key' => '1:2',
                    'text_uthmani_tajweed' => '<tajweed class=qalqalah>قّ</tajweed>لْبٌ <span class=end>٢</span>',
                ],
            ],
        ]),
    ]);

    $result = app(TajweedService::class)->getSurahTajweed(1);

    expect($result)->toHaveCount(2);
    expect($result[1])->toHaveKeys(['html', 'words']);
    expect($result[1]['html'])->toContain('data-tajweed-rule="ham_wasl"');
    expect($result[1]['html'])->toContain('tajweed-group-silent');
    expect($result[1]['html'])->not->toContain('style="color');
    expect($result[1]['html'])->not->toContain('class=end');
    expect($result[1]['html'])->not->toContain('١'); // trailing ayah-number marker stripped
    expect($result[2]['html'])->toContain('data-tajweed-rule="qalqalah"');
    expect($result[2]['html'])->toContain('tajweed-group-qalqalah');
});

test('an unrecognized tajweed class is rendered as plain text, not an unstyled span', function () {
    Http::fake([
        'api.quran.com/api/v4/quran/verses/uthmani_tajweed*' => Http::response([
            'verses' => [
                [
                    'verse_key' => '1:1',
                    'text_uthmani_tajweed' => '<tajweed class=some_future_rule>حرف</tajweed> <span class=end>١</span>',
                ],
            ],
        ]),
    ]);

    $result = app(TajweedService::class)->getSurahTajweed(1);

    expect($result[1]['html'])->not->toContain('<span');
    expect($result[1]['html'])->toContain('حرف');
});

test('getSurahTajweed splits each ayah into per-word fragments, keeping a multi-word tajweed span well-formed', function () {
    Http::fake([
        'api.quran.com/api/v4/quran/verses/uthmani_tajweed*' => Http::response([
            'verses' => [
                [
                    'verse_key' => '1:1',
                    // "ham_wasl" spans two words here on purpose.
                    'text_uthmani_tajweed' => '<tajweed class=ham_wasl>بِسْمِ اللَّهِ</tajweed> الرَّحْمَٰنِ <span class=end>١</span>',
                ],
            ],
        ]),
    ]);

    $result = app(TajweedService::class)->getSurahTajweed(1);

    expect($result[1]['words'])->toHaveCount(3);
    expect($result[1]['words'][0])->toContain('data-tajweed-rule="ham_wasl"')->toContain('</span>');
    expect($result[1]['words'][1])->toContain('data-tajweed-rule="ham_wasl"')->toContain('<span');
    expect($result[1]['words'][2])->not->toContain('tajweed-mark');
});

test('getJuzTajweed keys results by surah:ayah since a juz spans multiple surahs', function () {
    Http::fake([
        'api.quran.com/api/v4/quran/verses/uthmani_tajweed*' => Http::response([
            'verses' => [
                ['verse_key' => '1:7', 'text_uthmani_tajweed' => 'نص <span class=end>٧</span>'],
                ['verse_key' => '2:1', 'text_uthmani_tajweed' => '<tajweed class=laam_shamsiyah>ل</tajweed>م <span class=end>١</span>'],
            ],
        ]),
    ]);

    $result = app(TajweedService::class)->getJuzTajweed(1);

    expect($result)->toHaveKeys(['1:7', '2:1']);
    expect($result['2:1']['html'])->toContain('data-tajweed-rule="laam_shamsiyah"');
});

test('a failed tajweed request throws so callers can fall back to plain text', function () {
    Http::fake([
        'api.quran.com/api/v4/quran/verses/uthmani_tajweed*' => Http::response(['message' => 'error'], 503),
    ]);

    expect(fn () => app(TajweedService::class)->getSurahTajweed(1))
        ->toThrow(\App\Exceptions\QuranApiException::class);
});
