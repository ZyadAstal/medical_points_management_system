<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientPointsTest extends TestCase
{
    use RefreshDatabase;

    protected $receptionist;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $receptionRole = Role::where('name', 'Reception')->first();
        $this->receptionist = User::factory()->create([
            'role_id' => $receptionRole->id,
        ]);
    }

    public function test_receptionist_can_register_patient_with_points()
    {
        $patientRole = Role::where('name', 'Patient')->first();
        $patientData = [
            'username' => 'patient1',
            'password' => 'password123',
            'name' => 'Jane Doe',
            'national_id' => '1234567890',
            'phone' => '0599000000',
            'date_of_birth' => '1990-01-01',
            'address' => 'Gaza',
            'points' => 100, ];
        $response = $this->actingAs($this->receptionist)
            ->post(route('reception.patients.store'), $patientData);
        $response->assertRedirect(route('reception.patients.index'));
        $response->assertSessionHas('success', 'تم تسجيل المريض بنجاح');
        $this->assertDatabaseHas('patients', [
            'full_name' => 'Jane Doe',
            'national_id' => '1234567890',
            'points' => 100,]);}
    public function test_receptionist_can_update_patient_points()
    {
        $patient = Patient::factory()->create([
            'points' => 50,
        ]);
        $updateData = [
            'name' => $patient->full_name,
            'national_id' => $patient->national_id,
            'phone' => $patient->phone,
            'date_of_birth' => $patient->date_of_birth,
            'address' => $patient->address,
            'points' => 150, // Added 100 points
        ];

        $response = $this->actingAs($this->receptionist)
            ->put(route('reception.patients.update', $patient), $updateData);
        $response->assertRedirect(route('reception.patients.index'));
        $response->assertSessionHas('success', 'تم تحديث بيانات المريض');
        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'points' => 150,
        ]);
    }
}
