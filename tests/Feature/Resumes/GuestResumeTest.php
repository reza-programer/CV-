<?php

namespace Tests\Feature\Resumes;

use App\Enums\ActivityEvent;
use App\Http\Controllers\Resumes\GuestResumeController;
use App\Models\ActivityLog;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GuestResumeTest extends TestCase
{
    use RefreshDatabase, ResumeTestPayload;

    public function test_visitors_can_try_the_editor_without_an_account(): void
    {
        $this->get(route('demo.show', ['template' => 'wave']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('resumes/Try')
                ->where('draft', null)
                ->where('template', 'wave')
                ->has('templates', 100));
    }

    public function test_unknown_and_inactive_templates_are_ignored(): void
    {
        CvTemplate::query()->where('slug', 'compact')->update(['is_active' => false]);

        $this->get(route('demo.show', ['template' => 'compact']))
            ->assertInertia(fn (Assert $page) => $page->where('template', null)->has('templates', 99));

        $this->get(route('demo.show', ['template' => 'tidak-ada']))
            ->assertInertia(fn (Assert $page) => $page->where('template', null));
    }

    public function test_signed_in_users_go_to_their_own_cvs_instead(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('demo.show'))->assertRedirect(route('dashboard'));
        $this->putJson(route('demo.update'), $this->payload())->assertRedirect(route('dashboard'));

        $this->assertFalse(session()->has(GuestResumeController::SESSION_KEY));
    }

    public function test_drafts_are_validated_and_kept_in_the_session(): void
    {
        $this->putJson(route('demo.update'), $this->payload(['personal' => ['email' => 'bukan-email']]))
            ->assertJsonValidationErrors('personal.email');

        $this->assertFalse(session()->has(GuestResumeController::SESSION_KEY));

        $this->putJson(route('demo.update'), $this->payload())
            ->assertOk()
            ->assertJsonStructure(['saved_at']);

        $this->get(route('demo.show'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('draft.personal.full_name', 'Dimas Prasetyo')
                ->where('draft.experiences.0.highlights.0', 'Membangun API pelacakan paket.'));
    }

    public function test_drafts_cannot_use_inactive_templates(): void
    {
        CvTemplate::query()->where('slug', 'compact')->update(['is_active' => false]);

        $this->putJson(route('demo.update'), $this->payload(['template' => 'compact']))
            ->assertJsonValidationErrors('template');
    }

    public function test_signing_up_turns_the_draft_into_the_first_cv(): void
    {
        $this->putJson(route('demo.update'), $this->payload(['title' => 'CV Coba', 'template' => 'wave']))->assertOk();

        $this->get(route('register'))->assertInertia(fn (Assert $page) => $page->where('guestDraft', true));

        $response = $this->post(route('register.store'), [
            'name' => 'Rina Wulandari',
            'email' => 'rina@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::query()->where('email', 'rina@example.com')->sole();
        $resume = $user->resumes()->sole();

        $response->assertRedirect(route('resumes.edit', $resume));
        $this->assertSame('CV Coba', $resume->title);
        $this->assertSame('wave', $resume->template->slug);
        $this->assertSame('Dimas Prasetyo', $resume->full_name);
        $this->assertSame(['Membangun API pelacakan paket.', 'Menulis pengujian otomatis.'], $resume->workExperiences()->sole()->highlights);
        $this->assertCount(1, $resume->educations);
        $this->assertTrue(ActivityLog::query()->where('resume_id', $resume->id)->where('event', ActivityEvent::ResumeCreated)->exists());
        $this->assertFalse(session()->has(GuestResumeController::SESSION_KEY));
    }

    public function test_logging_in_saves_the_draft_and_fills_empty_details_from_the_account(): void
    {
        $user = User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);

        $this->putJson(route('demo.update'), $this->payload(['personal' => ['full_name' => '', 'email' => '']]))->assertOk();

        $this->get(route('login'))->assertInertia(fn (Assert $page) => $page->where('guestDraft', true));

        $response = $this->post(route('login.store'), ['email' => 'budi@example.com', 'password' => 'password']);

        $resume = $user->resumes()->sole();
        $response->assertRedirect(route('resumes.edit', $resume));
        $this->assertSame('Budi Santoso', $resume->full_name);
        $this->assertSame('budi@example.com', $resume->email);
        $this->assertSame('Backend Engineer', $resume->headline);
    }

    public function test_signing_up_without_a_draft_creates_nothing(): void
    {
        $this->get(route('register'))->assertInertia(fn (Assert $page) => $page->where('guestDraft', false));

        $this->post(route('register.store'), [
            'name' => 'Sari',
            'email' => 'sari@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseCount('resumes', 0);
    }
}
