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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('race_id')->nullable()->constrained()->onDelete('set null'); // Si está asociado a una carrera

            $table->enum('type', ['race', 'distance', 'pace', 'frequency']);
            // race: Completar una carrera en X tiempo
            // distance: Correr X km por semana/mes
            // pace: Mejorar pace promedio a X:XX/km
            // frequency: Entrenar X veces por semana

            $table->string('title'); // "Correr 10K sub 50 minutos"
            $table->text('description')->nullable();

            // Valores objetivo (JSON flexible según el tipo)
            $table->json('target_value');
            // Ejemplos:
            // race: {"time": 3000, "race_id": 1}
            // distance: {"distance": 50, "period": "week"}
            // pace: {"pace": 300}
            // frequency: {"sessions": 4, "period": "week"}

            $table->date('target_date')->nullable(); // Fecha límite
            $table->date('start_date')->nullable(); // Fecha de inicio

            $table->enum('status', ['active', 'completed', 'abandoned', 'paused'])->default('active');

            // Progreso calculado
            $table->json('progress')->nullable();
            // Ejemplos:
            // {"current_value": 45, "percentage": 90}

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('target_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
