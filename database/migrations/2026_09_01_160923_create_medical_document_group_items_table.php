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
        Schema::create('medical_document_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_document_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medical_document_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['medical_document_group_id', 'medical_document_id'], 'group_document_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_document_group_items');
    }
};
