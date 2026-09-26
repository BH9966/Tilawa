<?php

use App\Services\WordService;
use Illuminate\Support\Facades\Http;

test('getSurahWords returns word-by-word text, translation and transliteration keyed by ayah number', function () {
    Http::fake([
        'api.quran.com/api/v4/verses/by_chapter/1*' => Http::response([
            'verses' => [
                [
                    'verse_key' => '1:1',
                    'words' => [
                        ['char_type_name' => 'word', 'text_uthmani' => 'بِسْمِ', 'translation' => ['text' => 'In the name'], 'transliteration' => ['text' => 'bismi']],
                        ['char_type_name' => 'word', 'text_uthmani' => 'اللَّهِ', 'translation' => ['text' => 'of Allah'], 'transliteration' => ['text' => 'llahi']],
                        ['char_type_name' => 'end', 'text_uthmani' => '١'],
                    ],
                ],
            ],
        ]),
    ]);

    $result = app(WordService::class)->getSurahWords(1);

    expect($result)->toHaveCount(1);
    expect($result[1])->toHaveCount(2); // the "end" ayah-number marker is excluded
    expect($result[1][0])->toBe([
        'position' => 1,
        'arabic' => 'بِسْمِ',
        'translation' => 'In the name',
        'transliteration' => 'bismi',
    ]);
});

test('getSurahWords follows pagination when a surah is split across pages', function () {
    Http::fake([
        'api.quran.com/api/v4/verses/by_chapter/2*page=1*' => Http::response([
            'verses' => [
                ['verse_key' => '2:1', 'words' => [['char_type_name' => 'word', 'text_uthmani' => 'الم', 'translation' => ['text' => 'Alif Lam Meem'], 'transliteration' => ['text' => 'Alm']]]],
            ],
            'pagination' => ['total_pages' => 2],
        ]),
        'api.quran.com/api/v4/verses/by_chapter/2*page=2*' => Http::response([
            'verses' => [
                ['verse_key' => '2:2', 'words' => [['char_type_name' => 'word', 'text_uthmani' => 'ذَٰلِكَ', 'translation' => ['text' => 'That'], 'transliteration' => ['text' => 'Dhalika']]]],
            ],
            'pagination' => ['total_pages' => 2],
        ]),
    ]);

    $result = app(WordService::class)->getSurahWords(2);

    expect($result)->toHaveKeys([1, 2]);
});

test('getJuzWords keys results by surah:ayah', function () {
    Http::fake([
        'api.quran.com/api/v4/verses/by_juz/1*' => Http::response([
            'verses' => [
                ['verse_key' => '1:1', 'words' => [['char_type_name' => 'word', 'text_uthmani' => 'بِسْمِ', 'translation' => ['text' => 'In the name'], 'transliteration' => ['text' => 'bismi']]]],
            ],
        ]),
    ]);

    $result = app(WordService::class)->getJuzWords(1);

    expect($result)->toHaveKey('1:1');
});

test('a failed word request throws so callers can fall back to plain, non-tappable text', function () {
    Http::fake([
        'api.quran.com/api/v4/verses/by_chapter/1*' => Http::response(['message' => 'error'], 503),
    ]);

    expect(fn () => app(WordService::class)->getSurahWords(1))
        ->toThrow(\App\Exceptions\QuranApiException::class);
});
