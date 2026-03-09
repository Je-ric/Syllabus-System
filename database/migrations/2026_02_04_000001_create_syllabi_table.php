<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('academic_calendar_id')
                ->nullable()
                ->constrained('academic_calendars')
                ->nullOnDelete();

            $table->enum('status', ['draft', 'under_review', 'for_revision', 'approved'])
                ->default('draft');

            // Tracks which step of the syllabus wizard the user is on
            $table->string('current_step')->default('academic_calendar');

            $table->foreignId('prepared_by')   // faculty user / authenticated user
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('concurred_by')  // chair
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('approved_by')   // dean
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // One syllabus per course per academic calendar
            $table->unique(['course_id', 'academic_calendar_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabi');
    }
};
