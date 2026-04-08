<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\MedicalCenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    // تهيئة وتنظيف قاعدة البيانات الوهمية بعد كل مسار اختبار لضمان عدم تأثره بالذي قبله
    use RefreshDatabase;

    // حساب مدير النظام الذي سنستعمله لعمل هذه الاختبارات كونه يملك صلاحيات
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // زراعة الأدوار في قاعدة البيانات
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // إنشاء وتخزين حساب بمهام مدير النظام (SuperAdmin)
        $adminRole = Role::where('name', 'SuperAdmin')->first();
        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
    }

    // اختبار أن السوبر أدمن يمكنه إضافة مستخدم جديد للنظام (مثل طبيب)
    public function test_super_admin_can_add_new_user()
    {
        $doctorRole = Role::where('name', 'Doctor')->first();
        // مصفوفة تعبر عن البيانات المرسلة بطلب إضافة الطبيب
        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role_id' => $doctorRole->id,
        ];
        
        // الدخول كمدير نظام وإرسال طلب الإضافة الخفي للمنصة 
        $response = $this->actingAs($this->admin)
            ->post(route('superadmin.users.store'), $userData);
            
        // التأكد من التوجيه للصفحة الصحيحة وهي صفحة استعراض المستخدمين
        $response->assertRedirect(route('superadmin.users.index'));
        
        // التحقق الملموس من وجود المستخدم الجديد في قاعدة البيانات كما طُلب
        $this->assertDatabaseHas('users', [
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'role_id' => $doctorRole->id, 
        ]); 
    }

    // اختبار قدرة السوبر أدمن على تعديل صلاحيات وبيانات مستخدم
    public function test_super_admin_can_update_user() 
    {
        $doctorRole = Role::where('name', 'Doctor')->first();
        $pharmacistRole = Role::where('name', 'Pharmacist')->first();
        
        // إنشاء مستخدم جديد بشكل وهمي في قاعدة البيانات (طبيب) لتعديله لاحقا
        $user = User::factory()->create([
            'role_id' => $doctorRole->id,
        ]);
            
        // البيانات الجديدة التي سيتم التحديث إليها (سنغير اسمه وصلاحيته إلى صيدلي)
        $updateData = [
            'name' => 'Updated Name',
            'username' => 'updateduser',
            'email' => 'updated@example.com',
            'role_id' => $pharmacistRole->id, 
        ];
        
        // إرسال طلب التحديث بالنظام (غالباً باستخدام دالة PUT او PATCH)
        $response = $this->actingAs($this->admin)
            ->put(route('superadmin.users.update', $user), $updateData);
            
        // التحقق من التوجيه الصحيح
        $response->assertRedirect(route('superadmin.users.index'));
        
        // التأكد من أن التعديلات قد سُجلت فعليًا وتطبيقها في جداول النظام (قاعدة البيانات)
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'username' => 'updateduser',
            'role_id' => $pharmacistRole->id,
        ]);
    }

    // اختبار قدرة المدير على حذف مستخدم
    public function test_super_admin_can_delete_user()
    {
        // إنشاء مستخدم بشكل عشوائي ليكون الضحية (ليتم حذفه)
        $user = User::factory()->create();
        
        // إرسال طلب حذف (DELETE)
        $response = $this->actingAs($this->admin)
            ->delete(route('superadmin.users.destroy', $user));
            
        // التحقق من التوجيه الصحيح بعد الحذف
        $response->assertRedirect(route('superadmin.users.index'));
        
        // التأكد التام من أن المستخدم اختفى ولم يعد موجودًا بقاعدة البيانات إطلاقاً
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
