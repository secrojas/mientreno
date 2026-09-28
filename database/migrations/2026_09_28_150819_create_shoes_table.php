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
        Schema::create('shoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('brand', 60);
            $table->string('model', 100);
            $table->string('nickname', 60)->nullable();
            $table->string('color', 7)->default('#2DE38E');
            $table->string('photo_path')->nullable();
            $table->string('usage')->nullable();
            $table->date('purchased_at')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('initial_km', 7, 2)->default(0);
            $table->unsignedSmallInteger('max_km')->default(700);
            $table->boolean('is_default')->default(false);
            $table->timestamp('retired_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shoes');
    }
};
