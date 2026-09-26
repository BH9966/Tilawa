<?php

namespace App\Livewire;

use App\Exceptions\QuranApiException;
use App\Services\QuranService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Tilawa — Surahs'])]
class Home extends Component
{
    public bool $apiUnavailable = false;

    public string $search = '';

    /** @var 'surah'|'juz'|'page' */
    public string $tab = 'surah';

    /** @var 'asc'|'desc' */
    public string $sort = 'asc';

    /**
     * Surah numbers commonly recommended for regular reading — a curated
     * starting point until Phase 6 adds real last-read/most-read tracking.
     *
     * @var list<int>
     */
    protected array $popularSurahs = [18, 2, 55, 36, 67];

    public function mount(): void
    {
        try {
            // Priming this warms QuranService's own cache for the rest of
            // the request, so popularSurahDetails()/lastRead() below won't
            // hit the API again even though they call it independently.
            $this->surahs;
        } catch (QuranApiException) {
            $this->apiUnavailable = true;
        }
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['surah', 'juz', 'page'], true) ? $tab : 'surah';
    }

    public function toggleSort(): void
    {
        $this->sort = $this->sort === 'asc' ? 'desc' : 'asc';
    }

    #[Computed]
    public function surahs(): array
    {
        $surahs = collect(app(QuranService::class)->getSurahList());

        if ($this->search !== '') {
            $needle = mb_strtolower(trim($this->search));

            $surahs = $surahs->filter(function (array $surah) use ($needle): bool {
                return str_contains(mb_strtolower($surah['englishName']), $needle)
                    || str_contains(mb_strtolower($surah['englishNameTranslation']), $needle)
                    || str_contains($surah['name'], trim($this->search));
            });
        }

        $surahs = $this->sort === 'asc'
            ? $surahs->sortBy('number')
            : $surahs->sortByDesc('number');

        return $surahs->values()->all();
    }

    #[Computed]
    public function popularSurahDetails(): array
    {
        $quran = app(QuranService::class);

        return collect($this->popularSurahs)
            ->map(fn (int $number) => $quran->getSurahMeta($number))
            ->filter()
            ->values()
            ->all();
    }

    /** Where the user left off, if anywhere — powers the "Continue reading" banner. */
    #[Computed]
    public function lastRead(): ?array
    {
        $user = auth()->user();

        if (! $user->last_read_surah) {
            return null;
        }

        $surah = app(QuranService::class)->getSurahMeta($user->last_read_surah);

        if (! $surah) {
            return null;
        }

        return [
            'surah' => $surah,
            'ayah' => $user->last_read_ayah,
        ];
    }

    public function render()
    {
        if ($this->apiUnavailable) {
            return view('livewire.quran-unavailable');
        }

        return view('livewire.home', [
            'juzNumbers' => range(1, 30),
            'pageNumbers' => range(1, 604),
        ]);
    }
}
