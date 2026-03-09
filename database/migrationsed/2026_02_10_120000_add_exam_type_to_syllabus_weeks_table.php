<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('syllabus_weeks', function (Blueprint $table) {
            $table->string('exam_type')->nullable()->after('is_exam_week');
            $table->unique(['syllabus_id', 'exam_type']);
        });
    }

    public function down(): void
    {
        Schema::table('syllabus_weeks', function (Blueprint $table) {
            $table->dropUnique(['syllabus_id', 'exam_type']);
            $table->dropColumn('exam_type');
        });
    }
};
