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
        Schema::table('report_shares', function (Blueprint $table) {
            $table->timestamp('expires_at')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_shares', function (Blueprint $table) {
            $table->timestamp('expires_at')->useCurrent()->useCurrentOnUpdate()->change();
        });
    }
};
