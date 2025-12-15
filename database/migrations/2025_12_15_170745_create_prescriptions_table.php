<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();

            // المريض المرتبط بالوصفة
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();

            // الطبيب الذي أصدر الوصفة
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();

            $table->text('notes')->nullable();
            $table->timestamp('issued_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
