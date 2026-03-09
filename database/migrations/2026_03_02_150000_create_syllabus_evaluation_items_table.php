<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_evaluation_items', function (Blueprint $table) {
            $table->id();

            // Optional but beginner-friendly: easy filtering without extra joins.
            $table->foreignId('syllabus_id')
                ->constrained('syllabi')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->foreignId('week_content_id')
                ->constrained('week_contents')
                ->cascadeOnDelete()
                ->unique();

            // For rows that are not tied to a CO (e.g., MVGO).
            $table->string('outcome_label')->nullable();
            // User-selectable for regular weeks; exam rows are forced to 'exam' automatically.
            $table->enum('kind', ['activity', 'quiz', 'exam'])->nullable();
            // Only relevant when kind = exam (auto-filled based on syllabus_weeks.exam_type).
            $table->enum('exam_type', ['first_term', 'second_term', 'final_term'])->nullable();

            // Weight (%) only.
            $table->unsignedSmallInteger('weight')->nullable();

            $table->timestamps();

            $table->index(['syllabus_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_evaluation_items');
    }
};
