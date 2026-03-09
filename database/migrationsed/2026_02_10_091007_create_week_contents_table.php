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
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('component_type', ['LEC', 'LAB']); // if the course is lecture-only, then all records will be 'LEC'
                                                                            // separate lec to lab
            $table->foreignId('co_assessment_plan_id')
                ->nullable()
                ->constrained('co_assessment_plans')
                ->nullOnDelete();

            $table->text('learning_outcomes');
            $table->text('topics');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('week_contents');
    }
};
