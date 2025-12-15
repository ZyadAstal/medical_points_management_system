<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('username')->unique()->after('id');

            $table->foreignId('role_id')
                ->after('password')
                ->constrained('roles');

            $table->foreignId('medical_center_id')
                ->nullable()
                ->after('role_id')
                ->constrained('medical_centers');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['medical_center_id']);

            $table->dropColumn([
                'username',
                'role_id',
                'medical_center_id'
            ]);
        });
    }
};
