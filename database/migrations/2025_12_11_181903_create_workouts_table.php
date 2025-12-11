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
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('training_group_id')->nullable();
            $table->unsignedBigInteger('race_id')->nullable();

            $table->date('date');
            $table->enum('type', ['easy_run', 'intervals', 'tempo', 'long_run', 'recovery', 'race']);
            $table->decimal('distance', 8, 2); // Kilómetros (ej: 10.50 km)
            $table->integer('duration'); // Segundos (ej: 3600 = 1 hora)
            $table->integer('avg_pace')->nullable(); // Segundos por km (calculado)
            $table->integer('avg_heart_rate')->nullable(); // BPM
            $table->integer('elevation_gain')->nullable(); // Metros
            $table->tinyInteger('difficulty')->default(3); // 1-5
            $table->text('notes')->nullable();
            $table->json('weather')->nullable(); // {temp: 25, conditions: 'sunny'}
            $table->json('route')->nullable(); // GPS data, mapa, etc.
            $table->boolean('is_race')->default(false);

            $table->timestamps();

            // Índices para búsquedas frecuentes
            $table->index(['user_id', 'date']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
