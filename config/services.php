<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'quran' => [
        'base_url' => env('QURAN_API_BASE_URL', 'https://api.alquran.cloud/v1'),
        'arabic_edition' => env('QURAN_API_ARABIC_EDITION', 'quran-uthmani'),
        'transliteration_edition' => env('QURAN_API_TRANSLITERATION_EDITION', 'en.transliteration'),

        // One translation edition per language the reader can switch to.
        // 'en' stays the fallback/default whenever a user has no preference set.
        'translation_editions' => [
            'en' => env('QURAN_API_TRANSLATION_EDITION', 'en.sahih'),
            'ar' => env('QURAN_API_TRANSLATION_EDITION_AR', 'ar.muyassar'),
            'sw' => env('QURAN_API_TRANSLATION_EDITION_SW', 'sw.barwani'),
        ],
    ],

    // Separate provider (Quran.com's public content API) used only for the
    // optional Tajweed-highlighting layer. Kept apart from 'quran' above so
    // an outage here never affects normal reading, translations or bookmarks.
    'tajweed' => [
        'base_url' => env('TAJWEED_API_BASE_URL', 'https://api.quran.com/api/v4'),
    ],

    // Word-by-word text, translation and transliteration for the tap-a-word
    // drawer. Same provider/host as 'tajweed' above (Quran.com), a separate
    // block purely so the two features can point at different hosts later
    // without getting tangled. translation_id 131 = Saheeh International.
    'words' => [
        'base_url' => env('WORDS_API_BASE_URL', env('TAJWEED_API_BASE_URL', 'https://api.quran.com/api/v4')),
        'translation_id' => env('WORDS_API_TRANSLATION_ID', 131),
    ],

];
