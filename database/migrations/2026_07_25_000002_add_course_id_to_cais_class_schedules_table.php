<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cais_class_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('external_course_id')->nullable()->index()->after('external_department_id');
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete()->after('department_id');
        });
    }

    public function down(): void
    {
        Schema::table('cais_class_schedules', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn(['external_course_id', 'course_id']);
        });
    }
};
