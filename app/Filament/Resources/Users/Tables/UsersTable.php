<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\ActivityEvent;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => self::withCounts($query))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn (User $record) => self::code($record))
                    ->fontFamily(FontFamily::Mono)
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->weight(FontWeight::Medium)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->color('gray')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('role')
                    ->label('Peran')
                    ->state(fn (User $record) => $record->is_admin ? 'Admin' : 'Pengguna'),
                TextColumn::make('resumes_count')
                    ->label('CV')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('pdf_downloads_count')
                    ->label('PDF')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('last_seen_at')
                    ->label('Terakhir aktif')
                    ->since()
                    ->dateTimeTooltip('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->date('d/m/Y')
                    ->fontFamily(FontFamily::Mono)
                    ->sortable(),
            ])
            ->filters([
                Filter::make('online')
                    ->label('Sedang online')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('last_seen_at', '>=', now()->subMinutes(User::ONLINE_MINUTES))),
                TernaryFilter::make('is_admin')->label('Admin'),
            ])
            ->poll('30s')
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->modalDescription('Akun beserta semua CV dan foto milik pengguna ini akan dihapus permanen.')
                    ->hidden(fn (User $record) => $record->is(Auth::user())),
            ]);
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public static function withCounts(Builder $query): Builder
    {
        return $query->withCount([
            'resumes',
            'activityLogs as pdf_downloads_count' => fn (Builder $query) => $query->where('event', ActivityEvent::PdfDownloaded),
        ]);
    }

    /** User number as shown in the admin, e.g. "USR-00007". */
    public static function code(User $user): string
    {
        return sprintf('USR-%05d', $user->id);
    }
}
