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
        Schema::create('medical_appointment_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('follow_up_appointment_id')->nullable()->constrained('medical_appointments')->nullOnDelete();
            $table->string('type');
            $table->string('description');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_appointment_tasks');
    }
};
