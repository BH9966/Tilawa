<?php

namespace App\Livewire;

use App\Exceptions\QuranApiException;
use App\Services\QuranService;
use App\Services\TajweedService;
use App\Services\WordService;
use App\Support\TajweedRules;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Juz extends Component
{
    public int $number;

    public int $page = 1;

    public int $perPage = 10;

    public bool $apiUnavailable = false;

    /** @var 'en'|'ar'|'sw' */
    public string $translationLanguage = 'en';

    /** Off by default — the reading page stays plain unless the user opts in. */
    public bool $tajweedEnabled = false;

    public function mount(int $number): void
    {
        abort_unless($number >= 1 && $number <= 30, 404);

        $this->number = $number;

        $preferredLanguage = Auth::user()->translation_language;

        if (array_key_exists($preferredLanguage, app(QuranService::class)->supportedTranslationLanguages())) {
            $this->translationLanguage = $preferredLanguage;
        }

        $this->tajweedEnabled = (bool) Auth::user()->tajweed_enabled;

        try {
            $firstAyah = $this->pagedAyahs[0] ?? null;

            if ($firstAyah) {
                $this->recordLastRead($firstAyah['surahNumber'], $firstAyah['number']);
            }
        } catch (QuranApiException) {
            $this->apiUnavailable = true;
        }
    }

    public function nextPage(): void
    {
        if ($this->page < $this->totalPages()) {
            $this->page++;
            $this->recordCurrentPageAsLastRead();
        }
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->recordCurrentPageAsLastRead();
        }
    }

    protected function recordCurrentPageAsLastRead(): void
    {
        $firstAyah = $this->pagedAyahs[0] ?? null;

        if ($firstAyah) {
            $this->recordLastRead($firstAyah['surahNumber'], $firstAyah['number']);
        }
    }

    /**
     * Remember where the user left off, so the Home page can offer to resume
     * — same last-read fields Reading uses, so it doesn't matter whether
     * someone reads surah-by-surah or juz-by-juz.
     */
    protected function recordLastRead(int $surahNumber, int $ayahNumber): void
    {
        Auth::user()->forceFill([
            'last_read_surah' => $surahNumber,
            'last_read_ayah' => $ayahNumber,
            'last_read_at' => now(),
        ])->save();
    }

    /**
     * Toggle a bookmark on a single ayah for the current user. Bookmarks are
     * stored by surah + ayah number, so this works the same regardless of
     * whether the ayah was reached via a surah page or a juz page.
     */
    public function toggleBookmark(int $surahNumber, int $ayahNumber): void
    {
        $ayah = collect($this->juz['ayahs'])
            ->firstWhere(fn (array $a) => $a['surahNumber'] === $surahNumber && $a['number'] === $ayahNumber);

        if (! $ayah) {
            return;
        }

        $query = Auth::user()->bookmarks()
            ->where('surah_number', $surahNumber)
            ->where('ayah_number', $ayahNumber);

        if ($query->exists()) {
            $query->delete();

            return;
        }

        Auth::user()->bookmarks()->create([
            'surah_number' => $surahNumber,
            'surah_name' => $ayah['surahName'],
            'ayah_number' => $ayahNumber,
            'ayah_text' => $ayah['arabic'],
        ]);
    }

    public function setTranslationLanguage(string $language): void
    {
        if (! array_key_exists($language, app(QuranService::class)->supportedTranslationLanguages())) {
            return;
        }

        $this->translationLanguage = $language;

        Auth::user()->forceFill(['translation_language' => $language])->save();
    }

    public function toggleTajweed(): void
    {
        $this->tajweedEnabled = ! $this->tajweedEnabled;

        Auth::user()->forceFill(['tajweed_enabled' => $this->tajweedEnabled])->save();
    }

    public function goToJuz(int $number): void
    {
        abort_unless($number >= 1 && $number <= 30, 404);

        $this->redirectRoute('juz.show', ['number' => $number], navigate: true);
    }

    #[Computed]
    public function juz(): array
    {
        return app(QuranService::class)->getJuz($this->number, $this->translationLanguage);
    }

    #[Computed]
    public function translationLanguages(): array
    {
        return app(QuranService::class)->supportedTranslationLanguages();
    }

    #[Computed]
    public function translatorCredits(): array
    {
        return app(QuranService::class)->translatorCredits();
    }

    /**
     * Tajweed-highlighted HTML keyed by "surah:ayah", or an empty array
     * when the layer is off or its API is unavailable — callers must fall
     * back to plain Arabic text in that case, never break the page over it.
     *
     * @return array<string, string>
     */
    #[Computed]
    public function tajweedAyahs(): array
    {
        if (! $this->tajweedEnabled) {
            return [];
        }

        try {
            return app(TajweedService::class)->getJuzTajweed($this->number);
        } catch (QuranApiException) {
            return [];
        }
    }

    /** @return array<string, array{name: string, arabicName: string, group: string, description: string, color: string}> */
    #[Computed]
    public function tajweedRules(): array
    {
        return TajweedRules::all();
    }

    /**
     * Word-by-word text/translation/transliteration keyed by "surah:ayah",
     * for the tap-a-word drawer. Empty means the layer degrades to plain,
     * non-word-tappable text — never breaks the page.
     *
     * @return array<string, array<int, array{position: int, arabic: string, translation: ?string, transliteration: ?string}>>
     */
    #[Computed]
    public function wordsByAyah(): array
    {
        try {
            return app(WordService::class)->getJuzWords($this->number);
        } catch (QuranApiException) {
            return [];
        }
    }

    /** "{surahNumber}:{ayahNumber}" keys for every ayah in this juz the user has bookmarked. */
    #[Computed]
    public function bookmarkedPairs(): array
    {
        $surahNumbers = collect($this->juz['ayahs'])->pluck('surahNumber')->unique()->values();

        return Auth::user()->bookmarks()
            ->whereIn('surah_number', $surahNumbers)
            ->get(['surah_number', 'ayah_number'])
            ->map(fn ($bookmark) => "{$bookmark->surah_number}:{$bookmark->ayah_number}")
            ->all();
    }

    #[Computed]
    public function pagedAyahs(): array
    {
        return array_slice($this->juz['ayahs'], ($this->page - 1) * $this->perPage, $this->perPage);
    }

    public function totalPages(): int
    {
        return (int) max(1, ceil(count($this->juz['ayahs']) / $this->perPage));
    }

    public function render()
    {
        if ($this->apiUnavailable) {
            return view('livewire.quran-unavailable', ['backRoute' => 'home'])
                ->layout('layouts.app', ['title' => 'Tilawa — Unavailable']);
        }

        return view('livewire.juz', [
            'previousNumber' => $this->number > 1 ? $this->number - 1 : null,
            'nextNumber' => $this->number < 30 ? $this->number + 1 : null,
        ])->layout('layouts.app', [
            'title' => "Tilawa — Juz {$this->number}",
        ]);
    }
}
