<?php

namespace Tests\Feature\Admin;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Resumes\ResumeTestPayload;
use Tests\TestCase;

class ActivityTrackingTest extends TestCase
{
    use RefreshDatabase;
    use ResumeTestPayload;

    public function test_registration_is_recorded_and_admins_are_notified(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/register', [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
            'password' => 'password-yang-panjang',
            'password_confirmation' => 'password-yang-panjang',
        ])->assertRedirect();

        $user = User::query()->where('email', 'baru@example.com')->firstOrFail();

        $this->assertSame(
            [ActivityEvent::Registered],
            $user->activityLogs()->pluck('event')->all(),
            'Signing up records the registration only, not an extra sign-in.',
        );
        $this->assertSame(1, $admin->notifications()->count());
        $this->assertStringContainsString('Pengguna Baru', $admin->notifications()->first()->data['title']);
    }

    public function test_sign_in_is_recorded(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertTrue($user->activityLogs()->where('event', ActivityEvent::LoggedIn)->exists());
    }

    public function test_admins_land_on_the_admin_panel_after_signing_in(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect('/admin');
    }

    public function test_an_inertia_sign_in_leaves_for_the_admin_panel_with_a_full_page_visit(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'], ['X-Inertia' => 'true'])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', url('/admin'));
    }

    public function test_cv_actions_are_recorded_without_cv_content(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/resumes', ['title' => 'Lamaran rahasia', 'template' => 'professional', 'locale' => 'id'])->assertRedirect();
        $resume = $user->resumes()->firstOrFail();

        $this->putJson("/resumes/{$resume->id}", $this->payload())->assertOk();
        $this->putJson("/resumes/{$resume->id}", $this->payload())->assertOk();

        $this->postJson("/resumes/{$resume->id}/photo", [
            'photo' => UploadedFile::fake()->image('foto.jpg', 600, 800),
        ])->assertOk();

        $this->post("/resumes/{$resume->id}/duplicate")->assertRedirect();
        $this->delete("/resumes/{$resume->id}")->assertRedirect();

        $events = $user->activityLogs()->orderBy('id')->pluck('event')->all();

        $this->assertSame([
            ActivityEvent::ResumeCreated,
            ActivityEvent::ResumeEdited,
            ActivityEvent::PhotoUploaded,
            ActivityEvent::ResumeDuplicated,
            ActivityEvent::ResumeDeleted,
        ], $events, 'Repeated autosaves within the edit window count as one edit.');

        $created = $user->activityLogs()->where('event', ActivityEvent::ResumeCreated)->firstOrFail();
        $this->assertSame(['template' => 'professional'], $created->meta);
        $this->assertStringNotContainsString('rahasia', ActivityLog::query()->get()->toJson());

        $deleted = $user->activityLogs()->where('event', ActivityEvent::ResumeDeleted)->firstOrFail();
        $this->assertNull($deleted->resume_id, 'The entry outlives the deleted CV.');
        $this->assertSame('Classic ATS', $deleted->detail(), 'The test payload switched the CV to Classic.');
    }

    public function test_an_edit_is_recorded_again_after_the_edit_window(): void
    {
        $user = User::factory()->create();
        $resume = Resume::factory()->for($user)->create();
        $this->actingAs($user);

        $this->putJson("/resumes/{$resume->id}", $this->payload())->assertOk();
        $this->travel(ActivityLog::EDIT_WINDOW_MINUTES + 1)->minutes();
        $this->putJson("/resumes/{$resume->id}", $this->payload())->assertOk();

        $this->assertSame(2, $user->activityLogs()->where('event', ActivityEvent::ResumeEdited)->count());
    }

    public function test_last_seen_is_updated_at_most_once_a_minute(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/dashboard')->assertOk();
        $first = $user->fresh()->last_seen_at;
        $this->assertNotNull($first);
        $this->assertTrue($user->fresh()->isOnline());

        $this->travel(30)->seconds();
        $this->get('/dashboard')->assertOk();
        $this->assertTrue($first->equalTo($user->fresh()->last_seen_at));

        $this->travel(2)->minutes();
        $this->get('/dashboard')->assertOk();
        $this->assertTrue($user->fresh()->last_seen_at->gt($first));
    }
}
