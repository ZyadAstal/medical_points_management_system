<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medical_center_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('medicine_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('quantity')->default(0);

            $table->timestamps();

            $table->unique(['medical_center_id', 'medicine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
