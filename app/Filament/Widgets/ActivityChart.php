<?php

namespace App\Filament\Widgets;

use App\Enums\ActivityEvent;
use App\Filament\Widgets\Concerns\CountsPerDay;
use App\Models\ActivityLog;
use Filament\Widgets\ChartWidget;

/**
 * Active users and PDF downloads per day, in two greys.
 */
class ActivityChart extends ChartWidget
{
    use CountsPerDay;

    private const DAYS = 14;

    protected static ?int $sort = 2;

    protected ?string $heading = '14 hari terakhir';

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    protected ?string $maxHeight = '260px';

    protected ?string $pollingInterval = '60s';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        return [
            'labels' => self::dayLabels(self::DAYS),
            'datasets' => [
                [
                    'label' => 'Pengguna aktif',
                    'data' => self::countsPerDay(ActivityLog::query(), self::DAYS, 'COUNT(DISTINCT user_id)'),
                    'backgroundColor' => '#52525b',
                    'borderRadius' => 2,
                ],
                [
                    'label' => 'PDF diunduh',
                    'data' => self::countsPerDay(ActivityLog::query()->where('event', ActivityEvent::PdfDownloaded), self::DAYS),
                    'backgroundColor' => '#a1a1aa',
                    'borderRadius' => 2,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'bottom', 'labels' => ['boxWidth' => 8, 'boxHeight' => 8]]],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
        ];
    }
}
