<?php

namespace App\Services;

use App\Exceptions\QuranApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Word-by-word Arabic text, translation and transliteration for the
 * tap-a-word drawer, from Quran.com's public content API (the same
 * provider already credited for the optional Tajweed layer).
 *
 * Root/morphology data isn't available from this endpoint, so every word
 * simply omits those fields when absent — callers show "where available"
 * rather than treating a missing field as an error.
 */
class WordService
{
    protected string $baseUrl;

    protected int $translationId;

    public function __construct()
    {
        $this->baseUrl = config('services.words.base_url');
        $this->translationId = (int) config('services.words.translation_id');
    }

    /**
     * Word-by-word data for every ayah of a surah, keyed by ayah number.
     *
     * @return array<int, array<int, array{position: int, arabic: string, translation: ?string, transliteration: ?string}>>
     */
    public function getSurahWords(int $surahNumber): array
    {
        // Routed through the 'file' cache store (not the app's default) —
        // a long surah's word-by-word data can run large, and file storage
        // has no MySQL max_allowed_packet-style ceiling to worry about.
        return Cache::store('file')->rememberForever("tilawa-words:quran.words.surah.v1.{$surahNumber}", function () use ($surahNumber): array {
            $verses = $this->fetchVerses("verses/by_chapter/{$surahNumber}");

            $result = [];

            foreach ($verses as $verse) {
                [, $ayahNumber] = explode(':', $verse['verse_key']);
                $result[(int) $ayahNumber] = $this->mapWords($verse);
            }

            return $result;
        });
    }

    /**
     * Word-by-word data for every ayah of a juz, keyed by "surah:ayah".
     *
     * @return array<string, array<int, array{position: int, arabic: string, translation: ?string, transliteration: ?string}>>
     */
    public function getJuzWords(int $juzNumber): array
    {
        return Cache::store('file')->rememberForever("tilawa-words:quran.words.juz.v1.{$juzNumber}", function () use ($juzNumber): array {
            $verses = $this->fetchVerses("verses/by_juz/{$juzNumber}");

            $result = [];

            foreach ($verses as $verse) {
                $result[$verse['verse_key']] = $this->mapWords($verse);
            }

            return $result;
        });
    }

    /**
     * @return array<int, array{position: int, arabic: string, translation: ?string, transliteration: ?string}>
     */
    protected function mapWords(array $verse): array
    {
        return collect($verse['words'] ?? [])
            ->filter(fn (array $w): bool => ($w['char_type_name'] ?? null) === 'word')
            ->values()
            ->map(fn (array $w, int $i): array => [
                'position' => $i + 1,
                'arabic' => $w['text_uthmani'] ?? ($w['text'] ?? ''),
                'translation' => $w['translation']['text'] ?? null,
                'transliteration' => $w['transliteration']['text'] ?? null,
            ])
            ->all();
    }

    /**
     * Fetch every verse for a Quran.com "by_chapter"/"by_juz" path,
     * following pagination defensively (some deployments of this API cap
     * per_page well below a long surah's ayah count).
     *
     * @return array<int, array{verse_key: string, words: array}>
     */
    protected function fetchVerses(string $path): array
    {
        $all = [];
        $page = 1;
        $maxPages = 10; // sane ceiling — no surah/juz needs more than a couple of pages even at a small per_page cap

        do {
            try {
                $response = Http::timeout(15)->get("{$this->baseUrl}/{$path}", [
                    'words' => 'true',
                    'word_fields' => 'text_uthmani,text',
                    'translations' => $this->translationId,
                    'fields' => 'text_uthmani',
                    'per_page' => 300,
                    'page' => $page,
                ]);
            } catch (ConnectionException $e) {
                throw $this->wordsUnavailable($path, $e);
            }

            if ($response->failed()) {
                throw $this->wordsUnavailable($path, null, $response->status());
            }

            $verses = $response->json('verses');

            if (! is_array($verses)) {
                throw $this->wordsUnavailable($path, null, $response->status());
            }

            $all = array_merge($all, $verses);

            $totalPages = (int) ($response->json('pagination.total_pages') ?? 1);
            $page++;
        } while ($page <= $totalPages && $page <= $maxPages);

        return $all;
    }

    protected function wordsUnavailable(string $path, ?\Throwable $previous = null, ?int $status = null): QuranApiException
    {
        Log::warning('Word-by-word API unavailable; word drawer will show plain text only.', [
            'path' => $path,
            'status' => $status,
            'exception' => $previous?->getMessage(),
        ]);

        return new QuranApiException('Unable to fetch word-by-word data.', 0, $previous);
    }
}
