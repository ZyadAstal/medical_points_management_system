<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            // النقطة الطبية
            $table->foreignId('medical_center_id')
                ->constrained()
                ->cascadeOnDelete();

            // الدواء
            $table->foreignId('medicine_id')
                ->constrained()
                ->cascadeOnDelete();

            // الكمية المتوفرة
            $table->integer('quantity')->default(0);

            $table->timestamps();

            // تأكد من عدم تكرار نفس الدواء في نفس المركز
            $table->unique(['medical_center_id', 'medicine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
