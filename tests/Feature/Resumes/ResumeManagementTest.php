<?php

namespace Tests\Feature\Resumes;

use App\Enums\ExperienceType;
use App\Enums\ParagraphAlign;
use App\Models\CvTemplate;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ResumeManagementTest extends TestCase
{
    use RefreshDatabase, ResumeTestPayload;

    public function test_guests_cannot_reach_resume_routes(): void
    {
        $resume = Resume::factory()->create();

        $this->get(route('resumes.edit', $resume))->assertRedirect(route('login'));
        $this->putJson(route('resumes.update', $resume), $this->payload())->assertUnauthorized();
        $this->get(route('resumes.pdf', $resume))->assertRedirect(route('login'));
    }

    public function test_dashboard_lists_only_the_users_own_resumes(): void
    {
        $user = User::factory()->create();
        $own = Resume::factory()->for($user)->create(['title' => 'Milik saya']);
        Resume::factory()->create(['title' => 'Milik orang lain']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('resumes/Index')
                ->has('resumes', 1)
                ->where('resumes.0.id', $own->id)
                ->where('resumes.0.document.title', 'Milik saya')
                ->has('templates', 100));
    }

    public function test_recommended_templates_come_first_and_are_flagged(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('templates.0.slug', 'professional')
                ->where('templates.0.recommended', true)
                ->where('templates.8.recommended', false)
                ->where('templates', fn ($templates) => collect($templates)->where('recommended', true)->count() === 8));

        // Every template in the catalogue has a row, and every row a layout.
        $this->assertEqualsCanonicalizing(CvTemplate::SLUGS, CvTemplate::query()->pluck('slug')->all());
    }

    public function test_creating_a_resume_prefills_account_details_and_opens_the_editor(): void
    {
        $user = User::factory()->create(['name' => 'Rina Wulandari', 'email' => 'rina@example.com']);

        $response = $this->actingAs($user)->post(route('resumes.store'), [
            'title' => 'CV Analis',
            'template' => 'modern',
            'locale' => 'en',
        ]);

        $resume = $user->resumes()->sole();
        $response->assertRedirect(route('resumes.edit', $resume));

        $this->assertSame('CV Analis', $resume->title);
        $this->assertSame('modern', $resume->template->slug);
        $this->assertSame('Rina Wulandari', $resume->full_name);
        $this->assertSame('rina@example.com', $resume->email);
        $this->assertNull($resume->summary);
    }

    public function test_inactive_templates_cannot_be_chosen_for_new_resumes(): void
    {
        CvTemplate::query()->where('slug', 'compact')->update(['is_active' => false]);

        $this->actingAs(User::factory()->create())
            ->post(route('resumes.store'), ['template' => 'compact', 'locale' => 'id'])
            ->assertSessionHasErrors('template');
    }

    public function test_other_users_cannot_open_or_change_a_resume(): void
    {
        $resume = Resume::factory()->create();
        $intruder = User::factory()->create();

        $this->actingAs($intruder);

        $this->get(route('resumes.edit', $resume))->assertForbidden();
        $this->putJson(route('resumes.update', $resume), $this->payload())->assertForbidden();
        $this->delete(route('resumes.destroy', $resume))->assertForbidden();
        $this->post(route('resumes.duplicate', $resume))->assertForbidden();
        $this->get(route('resumes.pdf', $resume))->assertForbidden();
        $this->get(route('resumes.photo.show', $resume))->assertForbidden();
        $this->postJson(route('resumes.assist', $resume), ['task' => 'summary'])->assertForbidden();

        $this->assertModelExists($resume);
    }

    public function test_the_editor_receives_the_full_document(): void
    {
        $resume = Resume::factory()->withContent()->create();

        $this->actingAs($resume->user)
            ->get(route('resumes.edit', $resume))
            ->assertInertia(fn (Assert $page) => $page
                ->component('resumes/Edit')
                ->where('resume.id', $resume->id)
                ->has('resume.experiences', 3)
                ->has('resume.activities', 2)
                ->has('resume.section_order', 9)
                ->where('assistantEnabled', false));
    }

    public function test_saving_replaces_the_document_content(): void
    {
        $resume = Resume::factory()->withContent()->create();

        $payload = $this->payload(['template' => 'professional', 'paragraph_align' => 'justify']);
        $payload['section_order'] = ['education', 'experience'];

        $this->actingAs($resume->user)
            ->putJson(route('resumes.update', $resume), $payload)
            ->assertOk()
            ->assertJsonStructure(['saved_at']);

        $resume->refresh();

        $this->assertSame('professional', $resume->template->slug);
        $this->assertSame(ParagraphAlign::Justify, $resume->paragraph_align);
        $this->assertSame('Dimas Prasetyo', $resume->full_name);
        // Missing sections are appended so the stored order is always complete.
        $this->assertSame(['education', 'experience', 'summary', 'internship', 'organization', 'projects', 'skills', 'certifications', 'languages'], $resume->section_order);

        $work = $resume->workExperiences()->sole();
        $this->assertSame(['Membangun API pelacakan paket.', 'Menulis pengujian otomatis.'], $work->highlights);

        $activity = $resume->activities()->sole();
        $this->assertSame(ExperienceType::Organization, $activity->type);

        $this->assertCount(1, $resume->educations);
        $this->assertCount(0, $resume->projects);
        $this->assertCount(0, $resume->certifications);
        $this->assertSame('PHP, Laravel, MySQL', $resume->skillGroups()->sole()->skills);
    }

    public function test_current_positions_do_not_keep_an_end_date(): void
    {
        $resume = Resume::factory()->create();

        $payload = $this->payload();
        $payload['experiences'][0]['is_current'] = true;
        $payload['experiences'][0]['end_date'] = '2019-01';

        $this->actingAs($resume->user)->putJson(route('resumes.update', $resume), $payload)->assertOk();

        $work = $resume->workExperiences()->sole();
        $this->assertTrue($work->is_current);
        $this->assertNull($work->end_date);
    }

    public function test_invalid_content_is_rejected_with_field_errors_in_indonesian(): void
    {
        $resume = Resume::factory()->create();

        $payload = $this->payload([
            'paragraph_align' => 'center',
            'personal' => ['email' => 'bukan-email', 'phone' => 'telepon saya', 'website' => 'javascript:alert(1)'],
        ]);
        $payload['experiences'][0]['end_date'] = '2020-01';
        $payload['educations'][0]['start_date'] = '2015-13';
        $payload['experiences'][0]['highlights'] = array_fill(0, 13, 'Poin');

        $response = $this->actingAs($resume->user)->putJson(route('resumes.update', $resume), $payload);

        $response->assertUnprocessable()->assertJsonValidationErrors([
            'paragraph_align',
            'personal.email',
            'personal.phone',
            'personal.website',
            'experiences.0.end_date',
            'educations.0.start_date',
            'experiences.0.highlights',
        ]);

        $this->assertSame(
            'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            $response->json('errors')['experiences.0.end_date'][0],
        );
        $this->assertStringContainsString('email', $response->json('errors')['personal.email'][0]);
        $this->assertNotSame('Dimas Prasetyo', $resume->refresh()->full_name);
    }

    public function test_a_partial_payload_cannot_wipe_sections(): void
    {
        $resume = Resume::factory()->withContent()->create();
        $payload = $this->payload();
        unset($payload['experiences']);

        $this->actingAs($resume->user)
            ->putJson(route('resumes.update', $resume), $payload)
            ->assertJsonValidationErrors('experiences');

        $this->assertCount(3, $resume->workExperiences);
    }

    public function test_duplicating_copies_every_section(): void
    {
        $resume = Resume::factory()->withContent()->create(['title' => 'CV Utama']);

        $this->actingAs($resume->user)
            ->post(route('resumes.duplicate', $resume))
            ->assertRedirect(route('dashboard'));

        $copy = Resume::query()->whereKeyNot($resume->id)->sole();

        $this->assertSame('Salinan CV Utama', $copy->title);
        $this->assertSame($resume->user_id, $copy->user_id);
        $this->assertCount(3, $copy->workExperiences);
        $this->assertSame($resume->workExperiences->first()->highlights, $copy->workExperiences->first()->highlights);
        $this->assertCount($resume->languages()->count(), $copy->languages);
    }

    public function test_deleting_removes_the_resume_and_its_sections(): void
    {
        $resume = Resume::factory()->withContent()->create();

        $this->actingAs($resume->user)
            ->delete(route('resumes.destroy', $resume))
            ->assertRedirect(route('dashboard'));

        $this->assertModelMissing($resume);
        $this->assertDatabaseCount('resume_experiences', 0);
        $this->assertDatabaseCount('resume_educations', 0);
    }

    public function test_assistant_is_unavailable_without_an_api_key(): void
    {
        config(['cvforge.ai.api_key' => null]);
        $resume = Resume::factory()->create();

        $this->actingAs($resume->user)
            ->postJson(route('resumes.assist', $resume), ['task' => 'summary'])
            ->assertNotFound();
    }
}
