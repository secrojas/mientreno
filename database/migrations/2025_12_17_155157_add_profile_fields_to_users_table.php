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
            $table->string('avatar')->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('avatar');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('birth_date');
            $table->decimal('weight', 5, 2)->nullable()->comment('Peso en kg')->after('gender');
            $table->integer('height')->nullable()->comment('Altura en cm')->after('weight');
            $table->text('bio')->nullable()->after('height');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'birth_date', 'gender', 'weight', 'height', 'bio']);
        });
    }
};
