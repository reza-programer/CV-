<?php

namespace Tests\Feature\Resumes;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResumePhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_an_uploaded_photo_is_cropped_to_three_by_four_and_stored_privately(): void
    {
        $resume = Resume::factory()->create();

        $response = $this->actingAs($resume->user)
            ->postJson(route('resumes.photo.store', $resume), [
                'photo' => UploadedFile::fake()->image('foto.png', 1200, 900),
            ])
            ->assertOk();

        $path = $resume->refresh()->photo_path;
        $this->assertNotNull($path);
        Storage::disk('local')->assertExists($path);
        $this->assertStringContainsString("/resumes/{$resume->id}/photo?v=", $response->json('photo_url'));

        [$width, $height, $type] = getimagesizefromstring(Storage::disk('local')->get($path));
        $this->assertSame([450, 600, IMAGETYPE_JPEG], [$width, $height, $type]);
    }

    public function test_only_the_owner_can_view_the_photo(): void
    {
        $resume = Resume::factory()->create();
        $this->actingAs($resume->user)->postJson(route('resumes.photo.store', $resume), [
            'photo' => UploadedFile::fake()->image('foto.jpg', 600, 800),
        ]);

        $this->actingAs($resume->user)
            ->get(route('resumes.photo.show', $resume))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');

        $this->actingAs(User::factory()->create())
            ->get(route('resumes.photo.show', $resume))
            ->assertForbidden();
    }

    public function test_replacing_and_removing_a_photo_deletes_old_files(): void
    {
        $resume = Resume::factory()->create();
        $this->actingAs($resume->user);

        $this->postJson(route('resumes.photo.store', $resume), ['photo' => UploadedFile::fake()->image('a.jpg', 600, 800)]);
        $first = $resume->refresh()->photo_path;

        $this->postJson(route('resumes.photo.store', $resume), ['photo' => UploadedFile::fake()->image('b.jpg', 600, 800)]);
        $second = $resume->refresh()->photo_path;

        Storage::disk('local')->assertMissing($first);
        Storage::disk('local')->assertExists($second);

        $this->deleteJson(route('resumes.photo.destroy', $resume))->assertOk()->assertJson(['photo_url' => null]);

        Storage::disk('local')->assertMissing($second);
        $this->assertNull($resume->refresh()->photo_path);
    }

    public function test_small_or_non_image_files_are_rejected(): void
    {
        $resume = Resume::factory()->create();
        $this->actingAs($resume->user);

        $this->postJson(route('resumes.photo.store', $resume), ['photo' => UploadedFile::fake()->image('kecil.jpg', 120, 120)])
            ->assertJsonValidationErrors(['photo' => 'Foto minimal 200 × 200 piksel.']);

        $this->postJson(route('resumes.photo.store', $resume), ['photo' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf')])
            ->assertJsonValidationErrors('photo');

        $this->assertNull($resume->refresh()->photo_path);
    }

    public function test_deleting_a_resume_or_account_removes_photo_files(): void
    {
        $resume = Resume::factory()->create();
        $other = Resume::factory()->for($resume->user)->create();
        $this->actingAs($resume->user);

        foreach ([$resume, $other] as $item) {
            $this->postJson(route('resumes.photo.store', $item), ['photo' => UploadedFile::fake()->image('a.jpg', 600, 800)]);
        }

        $first = $resume->refresh()->photo_path;
        $second = $other->refresh()->photo_path;

        $this->delete(route('resumes.destroy', $resume));
        Storage::disk('local')->assertMissing($first);

        $resume->user->delete();
        Storage::disk('local')->assertMissing($second);
        $this->assertModelMissing($other);
    }

    public function test_duplicating_copies_the_photo_to_a_new_file(): void
    {
        $resume = Resume::factory()->create();
        $this->actingAs($resume->user);
        $this->postJson(route('resumes.photo.store', $resume), ['photo' => UploadedFile::fake()->image('a.jpg', 600, 800)]);

        $this->post(route('resumes.duplicate', $resume));

        $copy = Resume::query()->whereKeyNot($resume->id)->sole();
        $this->assertNotNull($copy->photo_path);
        $this->assertNotSame($resume->refresh()->photo_path, $copy->photo_path);
        Storage::disk('local')->assertExists($copy->photo_path);
    }
}
