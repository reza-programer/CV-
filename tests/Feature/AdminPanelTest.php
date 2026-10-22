<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_users_cannot_open_the_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admins_can_manage_users_and_templates(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin')->assertOk();
        $this->get('/admin/users')->assertOk()->assertSee('Pengguna');
        $this->get('/admin/cv-templates')->assertOk()->assertSee('Classic ATS');
        $this->get('/admin/cv-templates/create')->assertNotFound();
    }
}
