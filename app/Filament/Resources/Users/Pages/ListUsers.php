<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\Tables\UsersTable;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Support\CsvExport;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Ekspor CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(fn () => $this->export()),
            CreateAction::make(),
        ];
    }

    /** Exports the users matching the current filters and sort. */
    private function export(): StreamedResponse
    {
        $query = UsersTable::withCounts($this->getFilteredSortedTableQuery() ?? User::query());

        return CsvExport::download(
            'pengguna-'.now()->format('Ymd-His').'.csv',
            ['ID', 'Nama', 'Email', 'Peran', 'CV', 'PDF diunduh', 'Terakhir aktif', 'Terdaftar'],
            $query->lazy()->map(fn (User $user) => [
                UsersTable::code($user),
                $user->name,
                $user->email,
                $user->is_admin ? 'Admin' : 'Pengguna',
                (int) $user->getAttribute('resumes_count'),
                (int) $user->getAttribute('pdf_downloads_count'),
                $user->last_seen_at?->format('Y-m-d H:i:s'),
                $user->created_at->format('Y-m-d H:i:s'),
            ]),
        );
    }
}
