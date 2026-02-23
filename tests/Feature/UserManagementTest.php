<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\MedicalCenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $adminRole = Role::where('name', 'SuperAdmin')->first();
        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
    }
    public function test_super_admin_can_add_new_user(){
        $doctorRole = Role::where('name', 'Doctor')->first();
        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role_id' => $doctorRole->id,];
        $response = $this->actingAs($this->admin)
            ->post(route('superadmin.users.store'), $userData);
        $response->assertRedirect(route('superadmin.users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'role_id' => $doctorRole->id, ]); }
    public function test_super_admin_can_update_user() {
        $doctorRole = Role::where('name', 'Doctor')->first();
        $pharmacistRole = Role::where('name', 'Pharmacist')->first();
        $user = User::factory()->create([
            'role_id' => $doctorRole->id,]);
        $updateData = [
            'name' => 'Updated Name',
            'username' => 'updateduser',
            'email' => 'updated@example.com',
            'role_id' => $pharmacistRole->id, ];
        $response = $this->actingAs($this->admin)
            ->put(route('superadmin.users.update', $user), $updateData);
        $response->assertRedirect(route('superadmin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'username' => 'updateduser',
            'role_id' => $pharmacistRole->id,]);}
    public function test_super_admin_can_delete_user(){
        $user = User::factory()->create();
        $response = $this->actingAs($this->admin)
            ->delete(route('superadmin.users.destroy', $user));
        $response->assertRedirect(route('superadmin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
