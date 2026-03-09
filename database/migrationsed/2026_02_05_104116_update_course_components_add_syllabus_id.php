<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_components', function (Blueprint $table) {
            // Add syllabus_id FK
            $table->foreignId('syllabus_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            // Optional: 
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }

    public function down(): void
    {
        Schema::table('course_components', function (Blueprint $table) {
            // Re-add course_id if rolling back
            $table->foreignId('course_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            $table->dropForeign(['syllabus_id']);
            $table->dropColumn('syllabus_id');
        });
    }
};
