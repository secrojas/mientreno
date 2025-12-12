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
        Schema::table('workouts', function (Blueprint $table) {
            // Estado del entrenamiento: planificado, completado o saltado
            $table->enum('status', ['planned', 'completed', 'skipped'])
                ->default('completed')
                ->after('user_id');

            // Distancia planificada (para comparar con la real)
            $table->decimal('planned_distance', 8, 2)->nullable()->after('distance');

            // Razón por la que se saltó el entreno (opcional)
            $table->string('skip_reason', 255)->nullable()->after('notes');

            // Hacer nullable campos que solo aplican para workouts completados
            $table->integer('duration')->nullable()->change();
            $table->integer('avg_pace')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workouts', function (Blueprint $table) {
            $table->dropColumn(['status', 'planned_distance', 'skip_reason']);

            // Revertir nullable (esto podría fallar si hay datos null)
            $table->integer('duration')->nullable(false)->change();
            $table->integer('avg_pace')->nullable(false)->change();
        });
    }
};
