<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when the Al Quran Cloud API can't be reached or returns an
 * unusable response — network down, timeout, 5xx, malformed payload.
 * Callers (Livewire components) catch this specifically to show a
 * friendly "service unavailable" state instead of a 500 error page.
 */
class QuranApiException extends RuntimeException
{
}
