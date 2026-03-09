<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('co_assessment_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_outcome_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('assessment_name'); // Quiz, Exam, Project
            $table->text('assessment_desc')->nullable();

            $table->string('tla'); // Teaching-Learning Activity
            $table->unsignedInteger('raw_score');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('co_assessment_plans');
    }
};
