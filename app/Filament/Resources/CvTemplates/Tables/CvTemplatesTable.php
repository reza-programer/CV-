<?php

namespace App\Filament\Resources\CvTemplates\Tables;

use Filament\Actions\EditAction;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
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
            ->striped()
            ->columns([
                TextColumn::make('slug')
                    ->label('Kode')
                    ->fontFamily(FontFamily::Mono),
                TextColumn::make('name')
                    ->label('Nama')
                    ->weight(FontWeight::Medium),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->color('gray')
                    ->limit(70)
                    ->tooltip(fn ($record) => $record->description),
                TextColumn::make('resumes_count')
                    ->label('Dipakai')
                    ->numeric()
                    ->alignEnd(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
            ]);
    }
}
