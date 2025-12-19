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
        Schema::table('training_groups', function (Blueprint $table) {
            // Cambiar schedule de string a json para mejor estructura
            $table->json('schedule')->nullable()->change();

            // Agregar nivel del grupo
            $table->string('level')->nullable()->after('description'); // beginner, intermediate, advanced

            // Agregar número máximo de miembros (null = ilimitado)
            $table->integer('max_members')->nullable()->after('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_groups', function (Blueprint $table) {
            // Revertir schedule a string
            $table->string('schedule')->nullable()->change();

            // Eliminar campos agregados
            $table->dropColumn(['level', 'max_members']);
        });
    }
};
