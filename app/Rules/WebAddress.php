<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Accepts addresses the way people write them on a CV, with or without the
 * scheme: "linkedin.com/in/nama", "www.situs.id", "https://github.com/nama".
 */
class WebAddress implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Alamat :attribute tidak valid.');

            return;
        }

        $url = preg_match('#^https?://#i', $value) ? $value : 'https://'.$value;
        $host = parse_url($url, PHP_URL_HOST);

        if (filter_var($url, FILTER_VALIDATE_URL) === false || ! is_string($host) || ! str_contains($host, '.')) {
            $fail('Alamat :attribute tidak valid. Contoh: linkedin.com/in/nama-anda');
        }
    }
}
