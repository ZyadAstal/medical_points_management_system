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
        Schema::table('dispenses', function (Blueprint $table) {
            // Adding pharmacist_id as foreign key to users table
            // Nullable for safety with existing data, though ideally required
            $table->foreignId('pharmacist_id')->nullable()->after('medical_center_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispenses', function (Blueprint $table) {
            $table->dropForeign(['pharmacist_id']);
            $table->dropColumn('pharmacist_id');
        });
    }
};
