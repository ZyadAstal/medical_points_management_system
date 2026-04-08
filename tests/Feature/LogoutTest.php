<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    // تنظيف قاعدة البيانات بعد كل اختبار
    use RefreshDatabase;

    // تجهيز البيئة قبل تشغيل الاختبارات
    protected function setUp(): void
    {
        parent::setUp();
        // زراعة الأدوار افتراضيا لاستخدامها
        $this->seed(\Database\Seeders\RoleSeeder::class); 
    }

    // اختبار قدرة المستخدم على تسجيل الخروج بنجاح
    public function test_user_can_logout()
    {
        // إنشاء مستخدم وهمي بصلاحية سوبر أدمن
        $user = User::factory()->create([
            'role_id' => Role::where('name', 'SuperAdmin')->first()->id,
        ]);

        // نتصرف كأننا هذا المستخدم (actingAs) حيث يتم اعتباره داخل النظام, ثم ونرسل طلب تسجيل خروج للخادم
        $response = $this->actingAs($user)->post('/logout');

        // التأكد من أن النظام قام بتوجيهنا للصفحة الرئيسية أو صفحة تسجيل الدخول
        $response->assertRedirect('/');
        // التأكد من أن المستخدم أصبح مجرد زائر غير مسجل دخوله حالياً (Guest)
        $this->assertGuest();
    }
}
