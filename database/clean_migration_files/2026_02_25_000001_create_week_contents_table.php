<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('week_contents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('syllabus_week_id')
                ->constrained('syllabus_weeks')
                ->cascadeOnDelete();

            // 'LEC' or 'LAB'; lecture-only courses always use 'LEC'
            $table->enum('component_type', ['LEC', 'LAB']);

            // Course outcome linked to this week's content
            $table->foreignId('course_outcome_id')
                ->nullable()
                ->constrained('course_outcomes')
                ->nullOnDelete();

            // Required fields - saved as 'N/A' when user leaves blank
            $table->text('learning_outcomes');   // Unit Learning Outcomes
            $table->text('topics');               // Topics

            // Optional fields
            $table->text('assessment_task')->nullable(); // Assessment Task
            $table->text('tla')->nullable();             // Teaching & Learning Activities

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('week_contents');
    }
};
