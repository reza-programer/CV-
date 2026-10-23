<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use App\Models\ActivityLog;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestActivity extends TableWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Aktivitas terbaru';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(ActivityLog::query()->with('user'))
            ->defaultSort('created_at', 'desc')
            ->columns(ActivityLogsTable::columns(searchable: false))
            ->headerActions([
                Action::make('all')
                    ->label('Lihat semua')
                    ->link()
                    ->color('gray')
                    ->url(ActivityLogResource::getUrl('index')),
            ])
            ->paginated([10])
            ->poll('15s')
            ->emptyStateHeading('Belum ada aktivitas');
    }
}
