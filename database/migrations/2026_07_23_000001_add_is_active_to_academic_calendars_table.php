<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('academic_calendars', 'is_active')) {
            Schema::table('academic_calendars', function (Blueprint $table) {
                $table->boolean('is_active')->default(false)->after('cais_semester_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('academic_calendars', 'is_active')) {
            Schema::table('academic_calendars', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
