<?php

namespace App\Support;

/**
 * Reference data for the Tajweed classes returned by Quran.com's
 * "uthmani_tajweed" text — https://api.quran.com/api/v4. Rule names and
 * groupings follow Al Quran Cloud's own Tajweed guide (alquran.cloud/tajweed-guide),
 * the same provider already credited for Tilawa's Qur'an text.
 *
 * Colors are grouped by rule family (all Madd rules share a color, etc.) —
 * a legible teaching aid, not a claim to reproduce any one publisher's
 * printed Tajweed-Mushaf color key exactly.
 */
class TajweedRules
{
    /**
     * @var array<string, array{name: string, arabicName: string, group: string, description: string}>
     */
    public const RULES = [
        'ham_wasl' => [
            'name' => 'Hamzat ul-Wasl',
            'arabicName' => 'همزة الوصل',
            'group' => 'silent',
            'description' => "A connecting hamza that's only pronounced when starting recitation from this word — silent when reading straight through from the word before it.",
        ],
        'silent' => [
            'name' => 'Silent Letter',
            'arabicName' => 'حرف ساكن',
            'group' => 'silent',
            'description' => 'A letter written in the script but not pronounced in recitation.',
        ],
        'laam_shamsiyah' => [
            'name' => 'Laam Shamsiyyah',
            'arabicName' => 'لام شمسية',
            'group' => 'idgham',
            'description' => "The 'ال' laam is not pronounced — it assimilates into the \"sun letter\" that follows, which is pronounced doubled instead.",
        ],
        'madda_normal' => [
            'name' => 'Normal Madd',
            'arabicName' => 'مد عادي',
            'group' => 'madd',
            'description' => 'A standard elongation held for about two vowel counts.',
        ],
        'madda_permissible' => [
            'name' => 'Permissible Madd',
            'arabicName' => 'مد جائز',
            'group' => 'madd',
            'description' => 'An elongation the reciter may hold for two, four, or six vowel counts, as long as they stay consistent within a recitation.',
        ],
        'madda_necessary' => [
            'name' => 'Necessary Madd',
            'arabicName' => 'مد لازم',
            'group' => 'madd',
            'description' => 'A mandatory elongation held for six vowel counts, longer than the normal or permissible madd.',
        ],
        'madda_obligatory' => [
            'name' => 'Obligatory Madd',
            'arabicName' => 'مد واجب',
            'group' => 'madd',
            'description' => 'A required elongation, typically held for four to five vowel counts.',
        ],
        'qalqalah' => [
            'name' => 'Qalqalah',
            'arabicName' => 'قلقلة',
            'group' => 'qalqalah',
            'description' => "A short echoing bounce given to one of the letters ق ط ب ج د when it carries a sukoon (no vowel), especially at a stop.",
        ],
        'ikhafa' => [
            'name' => 'Ikhfa',
            'arabicName' => 'إخفاء',
            'group' => 'ghunnah',
            'description' => 'A noon sakinah or tanween is "hidden" — pronounced with a light nasal sound rather than fully clear, before certain letters.',
        ],
        'ikhafa_shafawi' => [
            'name' => 'Ikhfa Shafawi',
            'arabicName' => 'إخفاء شفوي',
            'group' => 'ghunnah',
            'description' => "A meem sakinah is lightly hidden with a nasal sound when followed by the letter ب.",
        ],
        'idgham_shafawi' => [
            'name' => 'Idgham Shafawi',
            'arabicName' => 'إدغام شفوي',
            'group' => 'idgham',
            'description' => 'A meem sakinah merges into a following meem, held with a nasal sound.',
        ],
        'idgham_ghunnah' => [
            'name' => 'Idgham with Ghunnah',
            'arabicName' => 'إدغام بغنة',
            'group' => 'ghunnah',
            'description' => 'A noon sakinah or tanween merges into the following letter, carrying a nasal sound.',
        ],
        'idgham_wo_ghunnah' => [
            'name' => 'Idgham without Ghunnah',
            'arabicName' => 'إدغام بلا غنة',
            'group' => 'idgham',
            'description' => 'A noon sakinah or tanween merges into the following letter, with no nasal sound.',
        ],
        'idgham_mutajanisayn' => [
            'name' => 'Idgham Mutajanisayn',
            'arabicName' => 'إدغام متجانسين',
            'group' => 'idgham',
            'description' => 'Two letters that share the same articulation point merge into one.',
        ],
        'idgham_mutaqaribayn' => [
            'name' => 'Idgham Mutaqaribayn',
            'arabicName' => 'إدغام متقاربين',
            'group' => 'idgham',
            'description' => 'Two letters with close (but not identical) articulation points merge into one.',
        ],
        'iqlab' => [
            'name' => 'Iqlab',
            'arabicName' => 'إقلاب',
            'group' => 'iqlab',
            'description' => "A noon sakinah or tanween is converted to a light meem sound when followed by the letter ب.",
        ],
        'ghunnah' => [
            'name' => 'Ghunnah',
            'arabicName' => 'غنة',
            'group' => 'ghunnah',
            'description' => 'A nasal resonance held for about two vowel counts, most often on a doubled (shaddah) noon or meem.',
        ],
    ];

    /** Background/foreground color for each rule family. */
    public const GROUP_COLORS = [
        'madd' => '#C2410C',
        'ghunnah' => '#15803D',
        'idgham' => '#1D4ED8',
        'qalqalah' => '#B91C1C',
        'iqlab' => '#7E22CE',
        'silent' => '#6B7280',
    ];

    /**
     * All rules with their color resolved, keyed by class name — ready to
     * hand to the frontend as one JSON blob.
     *
     * @return array<string, array{name: string, arabicName: string, group: string, description: string, color: string}>
     */
    public static function all(): array
    {
        return collect(self::RULES)
            ->map(fn (array $rule) => [...$rule, 'color' => self::GROUP_COLORS[$rule['group']] ?? '#374151'])
            ->all();
    }

    public static function color(string $ruleClass): string
    {
        $group = self::RULES[$ruleClass]['group'] ?? null;

        return self::GROUP_COLORS[$group] ?? '#374151';
    }

    /**
     * Pull the first recognized Tajweed rule out of a rendered word/ayah
     * HTML fragment (as produced by TajweedService), for showing "Tajweed
     * rule: X" on a single tapped word. Returns null when the fragment
     * carries no `data-tajweed-rule`, which simply means that word (or the
     * whole layer) has nothing to show here.
     *
     * @return null|array{class: string, name: string, arabicName: string, color: string}
     */
    public static function extractFromHtml(?string $html): ?array
    {
        if (! $html || ! preg_match('/data-tajweed-rule="([a-z_]+)"/u', $html, $matches)) {
            return null;
        }

        $ruleClass = $matches[1];
        $rule = self::RULES[$ruleClass] ?? null;

        if (! $rule) {
            return null;
        }

        return [
            'class' => $ruleClass,
            'name' => $rule['name'],
            'arabicName' => $rule['arabicName'],
            'color' => self::GROUP_COLORS[$rule['group']] ?? '#374151',
        ];
    }
}
