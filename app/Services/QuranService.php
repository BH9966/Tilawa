<?php

namespace App\Services;

use App\Exceptions\QuranApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class QuranService
{
    protected string $baseUrl;

    protected string $arabicEdition;

    protected string $transliterationEdition;

    /** @var array<string, string> Translation edition identifier keyed by language code. */
    protected array $translationEditions;

    /** Human-readable label for each supported translation language, in display order. */
    public const LANGUAGE_LABELS = [
        'en' => 'English',
        'ar' => 'Arabic',
        'sw' => 'Swahili',
    ];

    /** Which translator/tafsir each language's edition is credited to. */
    public const TRANSLATOR_CREDITS = [
        'en' => 'Saheeh International',
        'ar' => 'Tafsir al-Muyassar',
        'sw' => 'Ali Muhsin Al-Barwani',
    ];

    public function __construct()
    {
        $this->baseUrl = config('services.quran.base_url');
        $this->arabicEdition = config('services.quran.arabic_edition');
        $this->transliterationEdition = config('services.quran.transliteration_edition');
        $this->translationEditions = config('services.quran.translation_editions');
    }

    /**
     * Language codes the translation can be switched to, in display order.
     *
     * @return array<string, string> language code => label
     */
    public function supportedTranslationLanguages(): array
    {
        return self::LANGUAGE_LABELS;
    }

    /**
     * The translator/tafsir credited for each language's edition, keyed the
     * same way as supportedTranslationLanguages().
     *
     * @return array<string, string>
     */
    public function translatorCredits(): array
    {
        return self::TRANSLATOR_CREDITS;
    }

    /**
     * All 114 surahs with their basic metadata (number, names, ayah count, revelation type).
     *
     * @return array<int, array{
     *     number: int,
     *     name: string,
     *     englishName: string,
     *     englishNameTranslation: string,
     *     numberOfAyahs: int,
     *     revelationType: string,
     * }>
     */
    public function getSurahList(): array
    {
        return Cache::store('file')->rememberForever('tilawa-quran:quran.surah-list', function (): array {
            try {
                $response = Http::timeout(15)->get("{$this->baseUrl}/surah");
            } catch (ConnectionException $e) {
                throw $this->apiUnavailable('surah list', $e);
            }

            if ($response->failed()) {
                throw $this->apiUnavailable('surah list', null, $response->status());
            }

            $data = $response->json('data');

            if (! is_array($data)) {
                throw $this->apiUnavailable('surah list', null, $response->status());
            }

            return $data;
        });
    }

    /**
     * A single surah's metadata, without ayahs. Reuses the cached surah list.
     *
     * @return array{
     *     number: int,
     *     name: string,
     *     englishName: string,
     *     englishNameTranslation: string,
     *     numberOfAyahs: int,
     *     revelationType: string,
     * }|null
     */
    public function getSurahMeta(int $number): ?array
    {
        return collect($this->getSurahList())
            ->firstWhere('number', $number);
    }

    /**
     * A single surah with Arabic text, transliteration and translation merged per ayah.
     *
     * @return array{
     *     number: int,
     *     name: string,
     *     englishName: string,
     *     englishNameTranslation: string,
     *     revelationType: string,
     *     numberOfAyahs: int,
     *     ayahs: array<int, array{
     *         number: int,
     *         arabic: string,
     *         transliteration: string,
     *         translation: string,
     *         juz: int,
     *         page: int,
     *     }>,
     * }
     */
    public function getSurah(int $number, string $translationLanguage = 'en'): array
    {
        if (! array_key_exists($translationLanguage, $this->translationEditions)) {
            $translationLanguage = 'en';
        }

        return Cache::store('file')->rememberForever("tilawa-quran:quran.surah.v2.{$number}.{$translationLanguage}", function () use ($number, $translationLanguage): array {
            $editions = implode(',', [
                $this->arabicEdition,
                $this->transliterationEdition,
                $this->translationEditions[$translationLanguage],
            ]);

            try {
                $response = Http::timeout(15)->get("{$this->baseUrl}/surah/{$number}/editions/{$editions}");
            } catch (ConnectionException $e) {
                throw $this->apiUnavailable("surah {$number}", $e);
            }

            if ($response->failed()) {
                throw $this->apiUnavailable("surah {$number}", null, $response->status());
            }

            $data = $response->json('data');

            if (! is_array($data) || count($data) !== 3) {
                throw $this->apiUnavailable("surah {$number}", null, $response->status());
            }

            try {
                [$arabic, $transliteration, $translation] = $data;
            } catch (Throwable $e) {
                throw $this->apiUnavailable("surah {$number}", $e);
            }

            $ayahs = collect($arabic['ayahs'])
                ->map(function (array $ayah, int $index) use ($number, $transliteration, $translation): array {
                    $arabicText = $ayah['text'];

                    // The Uthmani edition prefixes ayah 1 of every surah (except Al-Fatihah,
                    // where the Bismillah IS ayah 1) with the Bismillah. We already display
                    // it once as a heading above the surah, so strip it here to avoid it
                    // being shown twice. Translation/transliteration editions don't repeat it.
                    if ($ayah['numberInSurah'] === 1 && $number !== 1) {
                        $arabicText = $this->stripLeadingBismillah($arabicText);
                    }

                    return [
                        'number' => $ayah['numberInSurah'],
                        'arabic' => $arabicText,
                        'transliteration' => $transliteration['ayahs'][$index]['text'],
                        'translation' => $translation['ayahs'][$index]['text'],
                        'juz' => $ayah['juz'],
                        'page' => $ayah['page'],
                    ];
                })
                ->values()
                ->all();

            return [
                'number' => $arabic['number'],
                'name' => $arabic['name'],
                'englishName' => $arabic['englishName'],
                'englishNameTranslation' => $arabic['englishNameTranslation'],
                'revelationType' => $arabic['revelationType'],
                'numberOfAyahs' => $arabic['numberOfAyahs'],
                'ayahs' => $ayahs,
            ];
        });
    }

    /**
     * A single juz (1-30) with Arabic text, transliteration and translation
     * merged per ayah, spanning however many surahs that juz covers.
     *
     * Unlike getSurah(), the API has no combined "/juz/{n}/editions/{...}"
     * endpoint (it 404s), so this fetches each edition separately — in
     * parallel via Http::pool() — and zips them together by index.
     *
     * @return array{
     *     number: int,
     *     ayahs: array<int, array{
     *         surahNumber: int,
     *         surahName: string,
     *         surahArabicName: string,
     *         number: int,
     *         globalAyahNumber: int,
     *         arabic: string,
     *         transliteration: string,
     *         translation: string,
     *         page: int,
     *     }>,
     * }
     */
    public function getJuz(int $juzNumber, string $translationLanguage = 'en'): array
    {
        if (! array_key_exists($translationLanguage, $this->translationEditions)) {
            $translationLanguage = 'en';
        }

        return Cache::store('file')->rememberForever("tilawa-quran:quran.juz.v1.{$juzNumber}.{$translationLanguage}", function () use ($juzNumber, $translationLanguage): array {
            $translationEdition = $this->translationEditions[$translationLanguage];

            try {
                $responses = Http::pool(fn ($pool) => [
                    'arabic' => $pool->as('arabic')->timeout(15)->get("{$this->baseUrl}/juz/{$juzNumber}/{$this->arabicEdition}"),
                    'transliteration' => $pool->as('transliteration')->timeout(15)->get("{$this->baseUrl}/juz/{$juzNumber}/{$this->transliterationEdition}"),
                    'translation' => $pool->as('translation')->timeout(15)->get("{$this->baseUrl}/juz/{$juzNumber}/{$translationEdition}"),
                ]);
            } catch (ConnectionException $e) {
                throw $this->apiUnavailable("juz {$juzNumber}", $e);
            }

            $parsed = [];

            foreach (['arabic', 'transliteration', 'translation'] as $key) {
                $response = $responses[$key] ?? null;

                if (! $response instanceof \Illuminate\Http\Client\Response || $response->failed()) {
                    throw $this->apiUnavailable(
                        "juz {$juzNumber}",
                        $response instanceof Throwable ? $response : null,
                        $response instanceof \Illuminate\Http\Client\Response ? $response->status() : null,
                    );
                }

                $data = $response->json('data');

                if (! is_array($data) || ! isset($data['ayahs']) || ! is_array($data['ayahs'])) {
                    throw $this->apiUnavailable("juz {$juzNumber}", null, $response->status());
                }

                $parsed[$key] = $data;
            }

            $ayahs = collect($parsed['arabic']['ayahs'])
                ->map(function (array $ayah, int $index) use ($parsed): array {
                    $arabicText = $ayah['text'];
                    $surahNumber = $ayah['surah']['number'];

                    // Same rule as getSurah(): strip the Bismillah the API embeds
                    // in ayah 1 of every surah except Al-Fatihah, since we show
                    // it once as a heading whenever a surah starts within the juz.
                    if ($ayah['numberInSurah'] === 1 && $surahNumber !== 1) {
                        $arabicText = $this->stripLeadingBismillah($arabicText);
                    }

                    return [
                        'surahNumber' => $surahNumber,
                        'surahName' => $ayah['surah']['englishName'],
                        'surahArabicName' => $ayah['surah']['name'],
                        'number' => $ayah['numberInSurah'],
                        'globalAyahNumber' => $ayah['number'],
                        'arabic' => $arabicText,
                        'transliteration' => $parsed['transliteration']['ayahs'][$index]['text'] ?? '',
                        'translation' => $parsed['translation']['ayahs'][$index]['text'] ?? '',
                        'page' => $ayah['page'],
                    ];
                })
                ->values()
                ->all();

            return [
                'number' => $parsed['arabic']['number'],
                'ayahs' => $ayahs,
            ];
        });
    }

    /**
     * Log the real failure and return a QuranApiException with a message
     * that's safe to show a caller/user, without leaking response internals.
     */
    protected function apiUnavailable(string $what, ?Throwable $previous = null, ?int $status = null): QuranApiException
    {
        Log::error("Qur'an API unavailable while fetching {$what}.", [
            'status' => $status,
            'exception' => $previous?->getMessage(),
        ]);

        return new QuranApiException("Unable to fetch {$what} from the Qur'an API.", 0, $previous);
    }

    /**
     * Remove a leading Bismillah phrase from an ayah's Arabic text, if present.
     *
     * Matches on the four Bismillah words after normalizing Arabic diacritics
     * (tashkeel) and alef variants (ٱ/أ/إ/آ -> ا), since the Uthmani script
     * text is fully vocalized and doesn't match a plain string comparison.
     */
    protected function stripLeadingBismillah(string $text): string
    {
        $words = preg_split('/\s+/u', trim($text));

        if (! is_array($words) || count($words) < 4) {
            return $text;
        }

        $bismillahWords = ['بسم', 'الله', 'الرحمن', 'الرحيم'];

        $normalize = function (string $word): string {
            // Strip Arabic diacritics (tashkeel / tanween / dagger alif, etc).
            $word = preg_replace('/[\x{0610}-\x{061A}\x{064B}-\x{065F}\x{06D6}-\x{06DC}\x{06DF}-\x{06E8}\x{06EA}-\x{06ED}\x{0670}]/u', '', $word);

            // Normalize alef variants to a plain alef.
            return strtr($word, [
                'ٱ' => 'ا',
                'أ' => 'ا',
                'إ' => 'ا',
                'آ' => 'ا',
            ]);
        };

        $leadingWords = array_map($normalize, array_slice($words, 0, 4));

        if ($leadingWords !== $bismillahWords) {
            return $text;
        }

        return trim(implode(' ', array_slice($words, 4)));
    }
}
