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

class Reading extends Component
{
    public int $number;

    /** @var 'ayat'|'mushaf' */
    public string $mode = 'ayat';

    /** Ayat-mode pagination: long surahs (e.g. Al-Baqarah, 286 ayahs) are shown in chunks. */
    public int $page = 1;

    public int $perPage = 10;

    /** Mushaf-mode pagination: a 1-based index into this surah's real printed-page numbers. */
    public int $mushafStep = 1;

    public bool $apiUnavailable = false;

    /** @var 'en'|'ar'|'sw' */
    public string $translationLanguage = 'en';

    /** Off by default — the reading page stays plain unless the user opts in. */
    public bool $tajweedEnabled = false;

    public function mount(int $number): void
    {
        abort_unless($number >= 1 && $number <= 114, 404);

        $this->number = $number;

        $preferredLanguage = Auth::user()->translation_language;

        if (array_key_exists($preferredLanguage, app(QuranService::class)->supportedTranslationLanguages())) {
            $this->translationLanguage = $preferredLanguage;
        }

        $this->tajweedEnabled = (bool) Auth::user()->tajweed_enabled;

        try {
            // Priming these here warms QuranService's cache for the rest of
            // this request/lifecycle, so render() and every computed prop
            // below reuse the result instead of hitting the API again.
            $this->surahList;
            $this->recordLastRead($this->pagedAyahs[0]['number'] ?? 1);
        } catch (QuranApiException) {
            $this->apiUnavailable = true;
        }
    }

    public function setMode(string $mode): void
    {
        $this->mode = in_array($mode, ['ayat', 'mushaf'], true) ? $mode : 'ayat';
    }

    /**
     * Switch the ayah translation's language and remember the choice on the
     * user's account, so it carries over to every surah and every device.
     */
    public function setTranslationLanguage(string $language): void
    {
        if (! array_key_exists($language, app(QuranService::class)->supportedTranslationLanguages())) {
            return;
        }

        $this->translationLanguage = $language;

        // forceFill(): translation_language is intentionally left out of
        // User::$fillable (see recordLastRead()'s note) — the whitelist
        // check above is what makes this safe to persist.
        Auth::user()->forceFill(['translation_language' => $language])->save();
    }

    public function toggleTajweed(): void
    {
        $this->tajweedEnabled = ! $this->tajweedEnabled;

        Auth::user()->forceFill(['tajweed_enabled' => $this->tajweedEnabled])->save();
    }

    public function nextPage(): void
    {
        if ($this->page < $this->totalPages()) {
            $this->page++;
            $this->recordLastRead($this->pagedAyahs[0]['number'] ?? 1);
        }
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->recordLastRead($this->pagedAyahs[0]['number'] ?? 1);
        }
    }

    public function nextMushafPage(): void
    {
        if ($this->mushafStep < $this->totalMushafSteps()) {
            $this->mushafStep++;
            $this->recordLastRead($this->mushafAyahs[0]['number'] ?? 1);
        }
    }

    public function previousMushafPage(): void
    {
        if ($this->mushafStep > 1) {
            $this->mushafStep--;
            $this->recordLastRead($this->mushafAyahs[0]['number'] ?? 1);
        }
    }

    /**
     * Toggle a bookmark on a single ayah for the current user.
     */
    public function toggleBookmark(int $ayahNumber): void
    {
        $ayah = collect($this->surah['ayahs'])->firstWhere('number', $ayahNumber);

        if (! $ayah) {
            return;
        }

        $query = Auth::user()->bookmarks()
            ->where('surah_number', $this->number)
            ->where('ayah_number', $ayahNumber);

        if ($query->exists()) {
            $query->delete();

            return;
        }

        Auth::user()->bookmarks()->create([
            'surah_number' => $this->number,
            'surah_name' => $this->surah['englishName'],
            'ayah_number' => $ayahNumber,
            'ayah_text' => $ayah['arabic'],
        ]);
    }

    /**
     * Remember where the user left off, so the Home page can offer to resume.
     */
    protected function recordLastRead(int $ayahNumber): void
    {
        // forceFill(): these columns are intentionally left out of User::$fillable
        // (they should never be settable from arbitrary user input), so mass
        // assignment via update() would silently discard them.
        Auth::user()->forceFill([
            'last_read_surah' => $this->number,
            'last_read_ayah' => $ayahNumber,
            'last_read_at' => now(),
        ])->save();
    }

    public function goToSurah(int $number): void
    {
        abort_unless($number >= 1 && $number <= 114, 404);

        $this->redirectRoute('surah.show', ['number' => $number], navigate: true);
    }

    #[Computed]
    public function surah(): array
    {
        return app(QuranService::class)->getSurah($this->number, $this->translationLanguage);
    }

    #[Computed]
    public function surahList(): array
    {
        return app(QuranService::class)->getSurahList();
    }

    /** @return array<string, string> language code => label, for the translation-language picker. */
    #[Computed]
    public function translationLanguages(): array
    {
        return app(QuranService::class)->supportedTranslationLanguages();
    }

    /**
     * Tajweed-highlighted HTML per ayah number, or an empty array when the
     * layer is off or its API is unavailable — callers must fall back to
     * plain Arabic text in that case, never break the page over it.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function tajweedAyahs(): array
    {
        if (! $this->tajweedEnabled) {
            return [];
        }

        try {
            return app(TajweedService::class)->getSurahTajweed($this->number);
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
     * Word-by-word text/translation/transliteration per ayah number, for
     * the tap-a-word drawer. An empty array (API hiccup) simply means the
     * ayah renders as plain, non-word-tappable text — never breaks the page.
     *
     * @return array<int, array<int, array{position: int, arabic: string, translation: ?string, transliteration: ?string}>>
     */
    #[Computed]
    public function wordsByAyah(): array
    {
        try {
            return app(WordService::class)->getSurahWords($this->number);
        } catch (QuranApiException) {
            return [];
        }
    }

    /** @return array<string, string> language code => translator/tafsir credit, for the translation-language picker. */
    #[Computed]
    public function translatorCredits(): array
    {
        return app(QuranService::class)->translatorCredits();
    }

    /** Ayah numbers the current user has bookmarked within this surah. */
    #[Computed]
    public function bookmarkedAyahNumbers(): array
    {
        return Auth::user()->bookmarks()
            ->where('surah_number', $this->number)
            ->pluck('ayah_number')
            ->all();
    }

    #[Computed]
    public function pagedAyahs(): array
    {
        return array_slice($this->surah['ayahs'], ($this->page - 1) * $this->perPage, $this->perPage);
    }

    public function totalPages(): int
    {
        return (int) max(1, ceil(count($this->surah['ayahs']) / $this->perPage));
    }

    /** The distinct real Qur'an page numbers (1-604) this surah's ayahs fall on, in order. */
    #[Computed]
    public function mushafPageNumbers(): array
    {
        return collect($this->surah['ayahs'])->pluck('page')->unique()->values()->all();
    }

    #[Computed]
    public function currentMushafPageNumber(): ?int
    {
        return $this->mushafPageNumbers[$this->mushafStep - 1] ?? null;
    }

    #[Computed]
    public function mushafAyahs(): array
    {
        $pageNumber = $this->currentMushafPageNumber;

        if ($pageNumber === null) {
            return $this->surah['ayahs'];
        }

        return collect($this->surah['ayahs'])
            ->where('page', $pageNumber)
            ->values()
            ->all();
    }

    public function totalMushafSteps(): int
    {
        return max(1, count($this->mushafPageNumbers));
    }

    public function render()
    {
        if ($this->apiUnavailable) {
            return view('livewire.quran-unavailable', ['backRoute' => 'home'])
                ->layout('layouts.app', ['title' => 'Tilawa — Unavailable']);
        }

        return view('livewire.reading', [
            'previousNumber' => $this->number > 1 ? $this->number - 1 : null,
            'nextNumber' => $this->number < 114 ? $this->number + 1 : null,
        ])->layout('layouts.app', [
            'title' => "Tilawa — {$this->surah['englishName']}",
        ]);
    }
}
