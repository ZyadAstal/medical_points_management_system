<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    // استخدام هذه السمة (Trait) يقوم بإعادة ضبط قاعدة البيانات بعد كل اختبار لضمان عدم تداخل البيانات والحفاظ عليها نظيفة
    use RefreshDatabase;

    // دالة setUp تعمل تلقائياً قبل كل اختبار في هذا الملف لتهيئة البيئة المناسبة
    protected function setUp(): void
    {
        parent::setUp();
        // نقوم بتشغيل ملف الـ Seeder الخاص بالأدوار لإنشاء أدوار المستخدمين في قاعدة البيانات الوهمية الخاصة بالاختبار
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    // هذا الاختبار يتحقق من إمكانية تسجيل دخول المستخدم في النظام بنجاح عند إدخاله لبيانات صحيحة
    public function test_user_can_login_with_valid_credentials()
    {
        // نجلب دور مسؤول النظام (SuperAdmin) من قاعدة البيانات
        $role = Role::where('name', 'SuperAdmin')->first();
        // ننشئ مستخدم وهمي ببيانات محددة مسبقاً (اسم مستخدم admin وكلمة مرور مقبولة)
        $user = User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        // نقوم بمحاكاة إرسال طلب (POST) لمسار تسجيل الدخول مع اسم المستخدم وكلمة المرور في المتصفح أو التطبيق
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        // نتحقق من أن النظام قام بتوجيهنا بشكل صحيح لصفحة لوحة تحكم الأدمن بعد نجاح التسجيل
        $response->assertRedirect(route('superadmin.dashboard'));
        // نتحقق من أن هذا المستخدم أصبح "مسجل دخوله" بالفعل في جلسة النظام الحالية
        $this->assertAuthenticatedAs($user);
    }

    // هذا الاختبار يتحقق من منع الدخول (فشل العملية) عند إدخال بيانات أو كلمة مرور خاطئة
    public function test_user_cannot_login_with_invalid_credentials()
    {
        // نجلب دور الـ SuperAdmin
        $role = Role::where('name', 'SuperAdmin')->first();
        // ننشئ مستخدم وهمي بنفس الطريقة وبيانات صحيحة
        User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        // نرسل طلب تسجيل دخول لنفس المستخدم لكن بكلمة مرور خاطئة عمداً (wrong-password)
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        // نتحقق من أن الجلسة (Session) تم إرجاعها وبداخلها "إشعار خطأ يخص اسم المستخدم/كلمة المرور"
        $response->assertSessionHasErrors('username');
        // نتحقق من أن المستخدم ما زال يعتبر ببساطة "زائراً" (Guest) ولم يتم تسجيل دخوله ابداً
        $this->assertGuest();
    }
}
