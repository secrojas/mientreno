<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cambiar el default de la columna role de 'user' a 'runner'
            $table->string('role')->default('runner')->change();
        });

        // Actualizar registros existentes con role='user' a role='runner'
        DB::table('users')
            ->where('role', 'user')
            ->update(['role' => 'runner']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revertir el default a 'user'
            $table->string('role')->default('user')->change();
        });
    }
};
