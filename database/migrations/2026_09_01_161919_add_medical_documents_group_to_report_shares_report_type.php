<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE report_shares MODIFY COLUMN report_type ENUM('weekly', 'monthly', 'medical', 'medical_documents_group') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE report_shares MODIFY COLUMN report_type ENUM('weekly', 'monthly', 'medical') NOT NULL");
    }
};
