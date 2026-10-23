<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

/**
 * This user's activity on their edit page, newest first.
 */
class ActivityLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'activityLogs';

    protected static ?string $title = 'Riwayat aktivitas';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns(ActivityLogsTable::columns(withUser: false))
            ->paginated([10, 25, 50])
            ->emptyStateHeading('Belum ada aktivitas');
    }
}
