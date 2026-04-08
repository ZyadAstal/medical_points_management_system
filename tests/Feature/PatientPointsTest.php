<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientPointsTest extends TestCase
{
    // تنظيف قاعدة البيانات الوهمية بعد الاختبارات
    use RefreshDatabase;

    // متغير لتخزين حساب موظف الاستقبال والطبيب
    protected $receptionist;
    protected $doctor;

    // دالة التحضير، تُنفذ قبل بدء أي اختبار
    protected function setUp(): void
    {
        parent::setUp();
        // زراعة الأدوار في قاعدة البيانات
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // إنشاء مركز طبي وهمي
        $medicalCenter = \App\Models\MedicalCenter::create([
            'name' => 'Test Medical Center',
        ]);

        // إنشاء مستخدم بصلاحية موظف استقبال
        $receptionRole = Role::where('name', 'Reception')->first();
        $this->receptionist = User::factory()->create([
            'role_id' => $receptionRole->id,
            'medical_center_id' => $medicalCenter->id,
        ]);

        // إنشاء مستخدم بصلاحية طبيب
        $doctorRole = Role::where('name', 'Doctor')->first();
        $this->doctor = User::factory()->create([
            'role_id' => $doctorRole->id,
            'medical_center_id' => $medicalCenter->id,
        ]);
    }

    // اختبار قدرة موظف الاستقبال على تسجيل مريض جديد وإضافة نقاط له للرصيد
    public function test_receptionist_can_register_patient_with_points()
    {
        $patientRole = Role::where('name', 'Patient')->first();
        // تجهيز بيانات المريض الوهمية التي سيتم تسليمها وإرسالها للنظام
        $patientData = [
            'username' => 'patient1',
            'password' => 'password123',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'national_id' => '1234567890',
            'phone' => '0599000000',
            'date_of_birth' => '1990-01-01',
            'address' => 'Gaza',
            'doctor_id' => $this->doctor->id,
            'priority' => 0,
            'points' => 100, // منح المريض 100 نقطة عند التسجيل الأول
        ];
        
        // محاكاة تسجيل دخول موظف الاستقبال (actingAs) ثم إرسال طلب (POST) لمسار إضافة المريض
        $response = $this->actingAs($this->receptionist)
            ->post(route('reception.patients.store'), $patientData);
            
        // التأكد من التوجيه لصفحة قائمة الانتظار بعد إتمام العملية بنجاح
        $response->assertRedirect(route('reception.visits.waiting'));
        // التأكد من ظهور رسالة النجاح في إشعارات الشاشة (Session)
        $response->assertSessionHas('success', 'تم تسجيل المريض وإضافته لقائمة الانتظار بنجاح');
        
        // التحقق الخطير والمهم: التأكد من أن بيانات المريض والنقاط قد حُفظت فعلاً داخل جداول قاعدة البيانات
        $this->assertDatabaseHas('patients', [
            'full_name' => 'Jane Doe',
            'national_id' => '1234567890',
            'points' => 100,
        ]);
    }

    // اختبار قدرة موظف الاستقبال على تعديل نقاط المريض (إضافة أو خصم نقاط)
    public function test_receptionist_can_update_patient_points()
    {
        // إنشاء مريض وهمي يمتلك 50 نقطة مسبقاً في قاعدة البيانات
        $patient = Patient::factory()->create([
            'points' => 50,
        ]);
        
        // تجهيز البيانات الجديدة المرسلة للتحديث
        $updateData = [
            'full_name' => $patient->full_name,
            'national_id' => $patient->national_id,
            'phone' => $patient->phone,
            'date_of_birth' => $patient->date_of_birth,
            'address' => $patient->address,
            'points' => 150, // رصيد النقاط الجديد المرغوب (تمت زيادة 100 نقطة للـ 50 القديمة)
        ];

        // إرسال طلب التحديث من الموظف (PUT) 
        // نستخدم from() لتحديد الصفحة السابقة لكي يعمل redirect()->back()
        $response = $this->actingAs($this->receptionist)
            ->from(route('reception.patients.index'))
            ->put(route('reception.patients.update', $patient), $updateData);
            
        // التحقق من إعادة التوجيه لجدول المرضى (الصفحة السابقة) ورسالة التأكيد
        $response->assertRedirect(route('reception.patients.index'));
        $response->assertSessionHas('success', 'تم تحديث بيانات المريض بنجاح');
        
        // التأكد من أن قاعدة البيانات قد تحدثت وتغيرت نقاط هذا المريض لتصبح 150 فعلياً
        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'points' => 150,
        ]);
    }
}
