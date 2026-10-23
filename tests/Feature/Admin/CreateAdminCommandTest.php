<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_admin_account(): void
    {
        $this->artisan('cvforge:admin', ['email' => 'Admin.Baru@Example.com'])
            ->expectsQuestion('Nama', 'Admin Baru')
            ->expectsQuestion('Kata sandi', 'rahasia-panjang-123')
            ->assertSuccessful();

        $admin = User::query()->where('email', 'admin.baru@example.com')->firstOrFail();

        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('rahasia-panjang-123', $admin->password));
        $this->assertNotNull($admin->email_verified_at);
    }

    public function test_it_promotes_an_existing_account(): void
    {
        $user = User::factory()->create(['email' => 'lama@example.com']);

        $this->artisan('cvforge:admin', ['email' => 'lama@example.com'])
            ->expectsConfirmation('Akun lama@example.com sudah ada. Jadikan admin?', 'yes')
            ->assertSuccessful();

        $this->assertTrue($user->fresh()->is_admin);
    }

    public function test_it_rejects_an_invalid_email(): void
    {
        $this->artisan('cvforge:admin', ['email' => 'bukan-email'])->assertFailed();

        $this->assertSame(0, User::query()->count());
    }
}
