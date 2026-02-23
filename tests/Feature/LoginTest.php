<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $role = Role::where('name', 'SuperAdmin')->first();
        $user = User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('superadmin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $role = Role::where('name', 'SuperAdmin')->first();
        User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }
}
