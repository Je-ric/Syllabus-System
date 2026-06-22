<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('syllabus_references', function (Blueprint $table) {
            $table->enum('component_type', ['LEC', 'LAB'])->default('LEC')->after('syllabus_week_id');
        });

        Schema::table('syllabus_materials', function (Blueprint $table) {
            $table->enum('component_type', ['LEC', 'LAB'])->default('LEC')->after('syllabus_week_id');
        });
    }

    public function down(): void
    {
        Schema::table('syllabus_references', function (Blueprint $table) {
            $table->dropColumn('component_type');
        });

        Schema::table('syllabus_materials', function (Blueprint $table) {
            $table->dropColumn('component_type');
        });
    }
};
