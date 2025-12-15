<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dispenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medical_center_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(0); // الكمية المصروفة
            $table->integer('points_used')->default(0); // النقاط المخصومة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispenses');
    }
};
