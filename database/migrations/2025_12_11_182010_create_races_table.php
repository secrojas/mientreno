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
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // "Maratón de Buenos Aires"
            $table->decimal('distance', 8, 2); // 5, 10, 21.1, 42.2
            $table->date('date');
            $table->string('location')->nullable();
            $table->integer('target_time')->nullable(); // Objetivo en segundos
            $table->integer('actual_time')->nullable(); // Tiempo real en segundos
            $table->integer('position')->nullable(); // Posición general
            $table->text('notes')->nullable();
            $table->enum('status', ['upcoming', 'completed', 'dns', 'dnf'])->default('upcoming');
            // dns = Did Not Start, dnf = Did Not Finish
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('races');
    }
};
