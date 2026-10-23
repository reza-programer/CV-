<?php

namespace App\Filament\Resources\ActivityLogs\Pages;

use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use App\Filament\Support\CsvExport;
use App\Models\ActivityLog;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Ekspor CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(fn () => $this->export()),
        ];
    }

    /** Exports the rows matching the current filters and sort. */
    private function export(): StreamedResponse
    {
        $query = ActivityLogsTable::withUser($this->getFilteredSortedTableQuery() ?? ActivityLog::query());

        return CsvExport::download(
            'aktivitas-'.now()->format('Ymd-His').'.csv',
            ['No.', 'Waktu', 'Pengguna', 'Email', 'Aktivitas', 'Referensi', 'Template', 'Keterangan'],
            $query->lazy()->map(fn (ActivityLog $log) => [
                $log->code(),
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user?->name,
                $log->user?->email,
                $log->event->getLabel(),
                $log->reference(),
                $log->templateName(),
                $log->note(),
            ]),
        );
    }
}
