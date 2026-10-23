<?php

namespace Tests\Feature\Resumes;

use App\Models\Resume;
use App\Models\User;
use App\Services\ResumeTranslator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class ResumeTranslateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // A fake key enables the route; the unreachable base URL guarantees
        // that nothing in these tests can reach the real API.
        config([
            'cvforge.ai.api_key' => 'test-key',
            'cvforge.ai.base_url' => 'http://127.0.0.1:1',
        ]);
    }

    public function test_translation_is_unavailable_without_an_api_key(): void
    {
        config(['cvforge.ai.api_key' => null]);
        $resume = Resume::factory()->create();

        $this->actingAs($resume->user)
            ->postJson(route('resumes.translate', $resume), ['target' => 'en'])
            ->assertNotFound();
    }

    public function test_other_users_cannot_translate_a_resume(): void
    {
        $resume = Resume::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('resumes.translate', $resume), ['target' => 'en'])
            ->assertForbidden();
    }

    public function test_the_target_language_is_validated_before_calling_the_api(): void
    {
        $this->partialMock(ResumeTranslator::class, fn (MockInterface $mock) => $mock->shouldNotReceive('translate'));
        $resume = Resume::factory()->create();

        $this->actingAs($resume->user)
            ->postJson(route('resumes.translate', $resume), ['target' => 'fr'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['target' => 'Bahasa tujuan harus Bahasa Indonesia atau English.']);

        $this->postJson(route('resumes.translate', $resume))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['target' => 'Pilih bahasa tujuan terjemahan.']);
    }

    public function test_a_resume_without_free_text_returns_no_items_without_calling_the_api(): void
    {
        $resume = Resume::factory()->create(['headline' => null, 'summary' => null]);

        $this->actingAs($resume->user)
            ->postJson(route('resumes.translate', $resume), ['target' => 'en'])
            ->assertOk()
            ->assertExactJson(['items' => []]);
    }
}
