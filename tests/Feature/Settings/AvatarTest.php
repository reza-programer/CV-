<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_an_uploaded_photo_is_cropped_square_and_stored_privately(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->image('foto.png', 1200, 800)])
            ->assertRedirect(route('profile.edit'));

        $path = $user->refresh()->avatar_path;
        $this->assertNotNull($path);
        Storage::disk('local')->assertExists($path);

        [$width, $height, $type] = getimagesizefromstring(Storage::disk('local')->get($path));
        $this->assertSame([320, 320, IMAGETYPE_JPEG], [$width, $height, $type]);

        $this->get(route('profile.edit'))
            ->assertInertia(fn (Assert $page) => $page->where('auth.user.avatar_url', fn (string $url) => str_starts_with($url, '/settings/avatar?v=')));
    }

    public function test_replacing_the_photo_removes_the_old_file(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->image('a.jpg', 400, 400)]);
        $first = $user->refresh()->avatar_path;

        $this->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->image('b.jpg', 400, 400)]);

        Storage::disk('local')->assertMissing($first);
        Storage::disk('local')->assertExists($user->refresh()->avatar_path);
    }

    public function test_only_images_of_a_reasonable_size_are_accepted(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf')])
            ->assertSessionHasErrors('avatar');
        $this->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->image('kecil.jpg', 50, 50)])
            ->assertSessionHasErrors(['avatar' => 'Foto minimal 100 × 100 piksel.']);

        $this->assertNull($user->refresh()->avatar_path);
    }

    public function test_the_photo_can_be_removed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->image('a.jpg', 400, 400)]);
        $path = $user->refresh()->avatar_path;

        $this->delete(route('avatar.destroy'))->assertRedirect(route('profile.edit'));

        $this->assertNull($user->refresh()->avatar_path);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_the_photo_is_served_only_to_its_owner(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->image('a.jpg', 400, 400)]);

        $this->get(route('avatar.show'))->assertOk()->assertHeader('Content-Type', 'image/jpeg');

        // Another account only ever reaches its own (missing) photo.
        $this->actingAs(User::factory()->create())->get(route('avatar.show'))->assertNotFound();

        auth()->logout();
        $this->get(route('avatar.show'))->assertRedirect(route('login'));
    }

    public function test_deleting_the_account_removes_the_photo(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('avatar.store'), ['avatar' => UploadedFile::fake()->image('a.jpg', 400, 400)]);
        $path = $user->refresh()->avatar_path;

        $this->delete(route('profile.destroy'), ['password' => 'password'])->assertRedirect('/');

        $this->assertModelMissing($user);
        Storage::disk('local')->assertMissing($path);
    }
}
