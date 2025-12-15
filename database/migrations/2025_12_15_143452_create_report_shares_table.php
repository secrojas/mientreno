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
        Schema::create('report_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('report_type', ['weekly', 'monthly']);
            $table->unsignedInteger('year');
            $table->unsignedTinyInteger('period'); // week (1-53) or month (1-12)
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamps();

            // Índices para mejor performance
            $table->index(['token', 'expires_at']);
            $table->index(['user_id', 'report_type', 'year', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_shares');
    }
};
