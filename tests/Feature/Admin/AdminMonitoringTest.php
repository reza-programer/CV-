<?php

namespace Tests\Feature\Admin;

use App\Enums\ActivityEvent;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\ActivityLogsRelationManager;
use App\Filament\Widgets\ActivityChart;
use App\Filament\Widgets\LatestActivity;
use App\Filament\Widgets\OnlineUsers;
use App\Filament\Widgets\UsageOverview;
use App\Models\ActivityLog;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_users_cannot_see_the_activity_log(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/activity-logs')
            ->assertForbidden();
    }

    public function test_the_dashboard_shows_who_is_online_and_what_they_did(): void
    {
        $admin = User::factory()->admin()->create();
        $online = User::factory()->create(['name' => 'Sinta Online', 'last_seen_at' => now()->subMinute()]);
        $away = User::factory()->create(['name' => 'Budi Lama', 'last_seen_at' => now()->subDay()]);
        $resume = Resume::factory()->for($online)->create();
        ActivityLog::record($online, ActivityEvent::PdfDownloaded, $resume, ['locale' => 'en']);
        ActivityLog::record($away, ActivityEvent::LoggedIn);

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Ringkasan');

        Livewire::test(UsageOverview::class)->assertSee('Sedang online')->assertSee('PDF diunduh');
        Livewire::test(ActivityChart::class)->assertOk();
        Livewire::test(OnlineUsers::class)
            ->assertCanSeeTableRecords([$online])
            ->assertCanNotSeeTableRecords([$away]);
        Livewire::test(LatestActivity::class)
            ->assertSee('Sinta Online')
            ->assertSee('Mengunduh PDF')
            ->assertSee('English');
    }

    public function test_the_activity_log_and_user_history_are_read_only(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['name' => 'Rani Pengguna']);
        ActivityLog::record($user, ActivityEvent::Registered);

        $this->actingAs($admin);

        $this->get('/admin/activity-logs')->assertOk()->assertSee('Rani Pengguna')->assertSee('Mendaftar');
        $this->get('/admin/activity-logs/create')->assertNotFound();
        $this->get('/admin/users')->assertOk()->assertSee('Terakhir aktif');
        $this->get("/admin/users/{$user->id}/edit")->assertOk();

        Livewire::test(ActivityLogsRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->assertCanSeeTableRecords($user->activityLogs)
            ->assertSee('Mendaftar');
    }

    public function test_the_dashboard_page_lists_the_monitoring_widgets(): void
    {
        $this->assertSame(
            [UsageOverview::class, ActivityChart::class, OnlineUsers::class, LatestActivity::class],
            (new Dashboard)->getWidgets(),
        );
    }
}
