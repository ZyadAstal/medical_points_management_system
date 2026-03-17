<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $blueprint->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $blueprint->foreignId('medical_center_id')->constrained('medical_centers')->onDelete('cascade');
            $blueprint->string('status')->default('waiting'); // waiting, in_progress, completed, cancelled
            $blueprint->integer('priority')->default(0);
            $blueprint->date('visit_date');
            $blueprint->text('notes')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
