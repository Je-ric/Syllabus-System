<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('syllabus_references', function (Blueprint $table) {
            $table->foreignId('syllabus_week_id')
                ->nullable()
                ->after('syllabus_id')
                ->constrained('syllabus_weeks')
                ->nullOnDelete();
        });

        Schema::table('syllabus_materials', function (Blueprint $table) {
            $table->foreignId('syllabus_week_id')
                ->nullable()
                ->after('syllabus_id')
                ->constrained('syllabus_weeks')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('syllabus_references', function (Blueprint $table) {
            $table->dropForeign(['syllabus_week_id']);
            $table->dropColumn('syllabus_week_id');
        });

        Schema::table('syllabus_materials', function (Blueprint $table) {
            $table->dropForeign(['syllabus_week_id']);
            $table->dropColumn('syllabus_week_id');
        });
    }
};
