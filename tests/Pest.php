<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Cache isolation
|--------------------------------------------------------------------------
|
| QuranService/TajweedService/WordService deliberately cache their (often
| large) payloads through the 'file' store rather than the app's default
| store (see those classes) — a database cache row has a max_allowed_packet
| ceiling that a long surah's cached HTML can exceed, which file storage
| doesn't. That store lives on real disk, though, so unlike CACHE_STORE=array
| (phpunit.xml) it isn't naturally reset between tests. Flush it before every
| Feature test so a cached response from one test can't leak into the next.
|
*/

beforeEach(function () {
    \Illuminate\Support\Facades\Cache::store('file')->flush();
})->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
