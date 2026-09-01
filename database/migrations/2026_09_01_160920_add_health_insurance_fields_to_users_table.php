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
        Schema::table('users', function (Blueprint $table) {
            $table->string('health_insurance_provider')->nullable()->after('bio');
            $table->string('health_insurance_plan')->nullable()->after('health_insurance_provider');
            $table->string('health_insurance_member_number')->nullable()->after('health_insurance_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['health_insurance_provider', 'health_insurance_plan', 'health_insurance_member_number']);
        });
    }
};
