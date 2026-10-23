<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * Users seen in the last 15 minutes.
 */
class OnlineUsers extends TableWidget
{
    private const RECENT_MINUTES = 15;

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Sedang online';

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 1];

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->where('last_seen_at', '>=', now()->subMinutes(self::RECENT_MINUTES)))
            ->defaultSort('last_seen_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Pengguna')
                    ->tooltip(fn (User $record) => $record->email),
                TextColumn::make('last_seen_at')
                    ->label('Aktif')
                    ->since()
                    ->color('gray')
                    ->alignEnd(),
            ])
            ->recordUrl(fn (User $record) => UserResource::getUrl('edit', ['record' => $record]))
            ->paginated([5])
            ->poll('30s')
            ->emptyStateHeading('Belum ada yang online');
    }
}
