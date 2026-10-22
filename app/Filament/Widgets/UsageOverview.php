<?php

namespace App\Filament\Widgets;

use App\Models\Resume;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsageOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $weekAgo = now()->subDays(7);

        return [
            Stat::make('Pengguna', number_format(User::query()->count(), 0, ',', '.'))
                ->description(User::query()->where('created_at', '>=', $weekAgo)->count().' baru dalam 7 hari'),
            Stat::make('CV tersimpan', number_format(Resume::query()->count(), 0, ',', '.'))
                ->description(Resume::query()->where('created_at', '>=', $weekAgo)->count().' dibuat dalam 7 hari'),
            Stat::make('CV diperbarui', number_format(Resume::query()->where('updated_at', '>=', $weekAgo)->count(), 0, ',', '.'))
                ->description('dalam 7 hari terakhir'),
        ];
    }
}
