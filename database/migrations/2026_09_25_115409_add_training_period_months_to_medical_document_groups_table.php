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
        Schema::table('medical_document_groups', function (Blueprint $table) {
            $table->unsignedTinyInteger('training_period_months')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_document_groups', function (Blueprint $table) {
            $table->dropColumn('training_period_months');
        });
    }
};
