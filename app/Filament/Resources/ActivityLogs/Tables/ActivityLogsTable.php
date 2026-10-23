<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogsTable
{
    /**
     * @param  Builder<ActivityLog>  $query
     * @return Builder<ActivityLog>
     */
    public static function withUser(Builder $query): Builder
    {
        return $query->with('user');
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => self::withUser($query))
            ->defaultSort('created_at', 'desc')
            ->columns(self::columns())
            ->filters([
                SelectFilter::make('event')
                    ->label('Aktivitas')
                    ->options(ActivityEvent::class)
                    ->multiple(),
                SelectFilter::make('user')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->label('Tanggal')
                    ->schema([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date))),
            ])
            ->poll('30s')
            ->emptyStateHeading('Belum ada aktivitas');
    }

    /**
     * One line per entry, like a stock movement log. Shared with the
     * dashboard widget and the user history.
     *
     * @return list<Column>
     */
    public static function columns(bool $withUser = true, bool $searchable = true): array
    {
        return array_values(array_filter([
            TextColumn::make('id')
                ->label('No.')
                ->formatStateUsing(fn (ActivityLog $record) => $record->code())
                ->fontFamily(FontFamily::Mono)
                ->color('gray'),
            TextColumn::make('created_at')
                ->label('Waktu')
                ->dateTime('d/m/Y H:i:s')
                ->fontFamily(FontFamily::Mono)
                ->tooltip(fn (ActivityLog $record) => $record->created_at->diffForHumans())
                ->sortable(),
            $withUser ? TextColumn::make('user.name')
                ->label('Pengguna')
                ->weight(FontWeight::Medium)
                ->tooltip(fn (ActivityLog $record) => $record->user?->email)
                ->searchable($searchable ? ['name', 'email'] : false) : null,
            TextColumn::make('event')
                ->label('Aktivitas'),
            TextColumn::make('reference')
                ->label('Referensi')
                ->state(fn (ActivityLog $record) => $record->reference())
                ->fontFamily(FontFamily::Mono)
                ->placeholder('—'),
            TextColumn::make('template')
                ->label('Template')
                ->state(fn (ActivityLog $record) => $record->templateName())
                ->placeholder('—'),
            TextColumn::make('note')
                ->label('Keterangan')
                ->state(fn (ActivityLog $record) => $record->note())
                ->color('gray')
                ->placeholder('—'),
        ]));
    }
}
