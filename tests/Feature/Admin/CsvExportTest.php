<?php

namespace Tests\Feature\Admin;

use App\Enums\ActivityEvent;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Support\CsvExport;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_download_the_activity_log_and_users_as_csv(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 23)->setTime(10, 0));
        $this->actingAs(User::factory()->admin()->create());
        ActivityLog::record(User::factory()->create(), ActivityEvent::Registered);

        Livewire::test(ListActivityLogs::class)
            ->callAction('export')
            ->assertFileDownloaded('aktivitas-20261023-100000.csv');

        Livewire::test(ListUsers::class)
            ->callAction('export')
            ->assertFileDownloaded('pengguna-20261023-100000.csv');
    }

    public function test_cells_that_spreadsheets_would_run_as_formulas_are_neutralised(): void
    {
        $response = CsvExport::download('x.csv', ['Nama', 'Email'], [
            ['=HYPERLINK("http://example.com")', 'a@example.com'],
            ['Budi', '-1+1'],
        ]);

        ob_start();
        $response->sendContent();
        $csv = (string) ob_get_clean();

        $this->assertStringStartsWith("\u{FEFF}Nama,Email", $csv);
        $this->assertStringContainsString('"\'=HYPERLINK(""http://example.com"")",a@example.com', $csv);
        $this->assertStringContainsString("Budi,'-1+1", $csv);
    }
}
