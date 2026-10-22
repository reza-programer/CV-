<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * CV dates are stored as "YYYY" or "YYYY-MM": most CVs state a month or only a
 * year, never a day.
 */
class PartialDate implements ValidationRule
{
    private const PATTERN = '/^(\d{4})(?:-(0[1-9]|1[0-2]))?$/';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match(self::PATTERN, $value, $matches)) {
            $fail('Format :attribute harus tahun (YYYY) atau bulan dan tahun (YYYY-MM).');

            return;
        }

        $year = (int) $matches[1];
        $maxYear = (int) now()->format('Y') + 10;

        if ($year < 1950 || $year > $maxYear) {
            $fail("Tahun pada :attribute harus antara 1950 dan {$maxYear}.");
        }
    }

    /**
     * Compare two partial dates. A bare year is treated as its first month when
     * used as a start and its last month when used as an end, so "2020" to
     * "2020-03" is a valid range.
     */
    public static function isBefore(string $start, string $end): bool
    {
        $startKey = strlen($start) === 4 ? $start.'-01' : $start;
        $endKey = strlen($end) === 4 ? $end.'-12' : $end;

        return strcmp($startKey, $endKey) <= 0;
    }
}
