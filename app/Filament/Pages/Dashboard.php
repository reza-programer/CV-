<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ActivityChart;
use App\Filament\Widgets\LatestActivity;
use App\Filament\Widgets\OnlineUsers;
use App\Filament\Widgets\UsageOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use UnitEnum;

/**
 * Admin home: who uses CVForge right now and what they have been doing.
 */
class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Ringkasan';

    protected static string|UnitEnum|null $navigationGroup = 'Pemantauan';

    public function getWidgets(): array
    {
        return [
            UsageOverview::class,
            ActivityChart::class,
            OnlineUsers::class,
            LatestActivity::class,
        ];
    }

    public function getColumns(): int|array
    {
        return ['md' => 2, 'xl' => 3];
    }
}
