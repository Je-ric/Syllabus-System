<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('co_assessment_plans', function (Blueprint $table) {

            // Remove raw_score column
            $table->dropColumn('raw_score');

            // Add new columns
            $table->string('learning_outcomes')->after('tla');
            $table->string('topic')->after('learning_outcomes');
        });
    }

    public function down(): void
    {
        Schema::table('co_assessment_plans', function (Blueprint $table) {

            // Restore raw_score if rolled back
            $table->unsignedInteger('raw_score')->after('tla');

            // Remove newly added columns
            $table->dropColumn(['learning_outcomes', 'topic']);
        });
    }
};
