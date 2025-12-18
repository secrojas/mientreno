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
        Schema::table('businesses', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable()->after('slug');
            $table->string('level')->nullable()->after('description'); // principiante, intermedio, avanzado
            $table->json('schedule')->nullable()->after('level'); // Días y horarios de entrenamientos
            $table->boolean('is_active')->default(true)->after('schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn(['owner_id', 'description', 'level', 'schedule', 'is_active']);
        });
    }
};
