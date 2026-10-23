<?php

namespace App\Filament\Widgets\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CountsPerDay
{
    /**
     * Rows per calendar day for the last $days days, oldest first, with zero
     * for days without rows.
     *
     * @param  Builder<covariant \Illuminate\Database\Eloquent\Model>  $query
     * @param  literal-string  $aggregate
     * @return list<float>
     */
    protected static function countsPerDay(Builder $query, int $days, string $aggregate = 'COUNT(*)'): array
    {
        $since = now()->subDays($days - 1)->startOfDay();

        /** @var array<string, int|string> $totals */
        $totals = $query
            ->where('created_at', '>=', $since)
            ->selectRaw("DATE(created_at) as day, {$aggregate} as total")
            ->groupBy('day')
            ->pluck('total', 'day')
            ->all();

        return array_map(
            fn (int $ago) => (float) ($totals[now()->subDays($ago)->toDateString()] ?? 0),
            range($days - 1, 0),
        );
    }

    /**
     * @return list<string>
     */
    protected static function dayLabels(int $days): array
    {
        return array_map(
            fn (int $ago) => now()->subDays($ago)->settings(['locale' => 'id'])->translatedFormat('j M'),
            range($days - 1, 0),
        );
    }
}
