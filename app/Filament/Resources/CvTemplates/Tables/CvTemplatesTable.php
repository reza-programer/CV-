<?php

namespace App\Filament\Resources\CvTemplates\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CvTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('resumes'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->description(fn ($record) => $record->description)
                    ->wrap(),
                TextColumn::make('slug')
                    ->label('Kode')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('resumes_count')
                    ->label('Dipakai')
                    ->suffix(' CV')
                    ->numeric(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
