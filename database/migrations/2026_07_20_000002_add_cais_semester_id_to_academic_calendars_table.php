<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('academic_calendars', 'cais_semester_id')) {
            Schema::table('academic_calendars', function (Blueprint $table) {
                $table->unsignedBigInteger('cais_semester_id')->nullable()->index()->after('end_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('academic_calendars', 'cais_semester_id')) {
            Schema::table('academic_calendars', function (Blueprint $table) {
                $table->dropIndex(['cais_semester_id']);
                $table->dropColumn('cais_semester_id');
            });
        }
    }
};
