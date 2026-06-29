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
        Schema::table('syllabi', function (Blueprint $table) {
            // Add plain indexes to replace the FK-supporting role of the
            // composite unique index before dropping it
            $table->index('course_id');
            $table->index('academic_calendar_id');
            $table->dropUnique(['course_id', 'academic_calendar_id']);
        });
    }

    public function down(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            $table->unique(['course_id', 'academic_calendar_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['academic_calendar_id']);
        });
    }
};
