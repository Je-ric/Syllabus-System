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
        Schema::table('syllabus_review_forms', function (Blueprint $table) {
            $table->timestamp('part_h_faculty_response_updated_at')->nullable()->after('part_h_faculty_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('syllabus_review_forms', function (Blueprint $table) {
            $table->dropColumn('part_h_faculty_response_updated_at');
        });
    }
};
