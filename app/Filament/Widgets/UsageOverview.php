<?php

namespace App\Filament\Widgets;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\Resume;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsageOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s';

    protected int|array|null $columns = ['md' => 2, 'xl' => 4];

    protected function getStats(): array
    {
        $weekAgo = now()->subDays(7);
        $today = now()->startOfDay();
        $pdf = fn () => ActivityLog::query()->where('event', ActivityEvent::PdfDownloaded);

        return [
            Stat::make('Pengguna', self::number(User::query()->count()))
                ->description(User::query()->where('created_at', '>=', $weekAgo)->count().' baru dalam 7 hari'),
            Stat::make('Sedang online', self::number(User::query()->where('last_seen_at', '>=', now()->subMinutes(User::ONLINE_MINUTES))->count()))
                ->description(User::query()->where('last_seen_at', '>=', $today)->count().' aktif hari ini'),
            Stat::make('CV dibuat', self::number(Resume::query()->count()))
                ->description(Resume::query()->where('created_at', '>=', $weekAgo)->count().' baru dalam 7 hari'),
            Stat::make('PDF diunduh', self::number($pdf()->count()))
                ->description($pdf()->where('created_at', '>=', $today)->count().' hari ini'),
        ];
    }

    private static function number(int $value): string
    {
        return number_format($value, 0, ',', '.');
    }
}
