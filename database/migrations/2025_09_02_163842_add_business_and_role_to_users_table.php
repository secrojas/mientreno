<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('business_id')
                ->nullable()
                ->constrained('businesses')
                ->nullOnDelete();

            $table->string('role')->default('user');

            $table->dropUnique('users_email_unique');
            $table->unique(['business_id', 'email'], 'users_business_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_business_email_unique');
            $table->unique('email', 'users_email_unique');

            $table->dropConstrainedForeignId('business_id');
            $table->dropColumn('role');
        });
    }
};
