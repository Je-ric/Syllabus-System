<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE academic_calendar_events
            MODIFY COLUMN type
            ENUM('holiday','exam','break','non_teaching','other')
            NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE academic_calendar_events
            MODIFY COLUMN type
            ENUM('holiday','exam','break','other')
            NOT NULL
        ");
    }
};
