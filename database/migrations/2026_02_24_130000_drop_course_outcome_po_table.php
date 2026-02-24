<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('course_outcome_po');
        // Schema::dropIfExists('course_outcomes_po');
    }

    public function down(): void
    {
        if (!Schema::hasTable('course_outcome_po')) {
            Schema::create('course_outcome_po', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_outcome_id')->constrained()->cascadeOnDelete();
                $table->foreignId('program_outcome_id')->constrained()->cascadeOnDelete();
                $table->enum('ied', ['I', 'E', 'D']);
                $table->timestamps();
                $table->unique(['course_outcome_id', 'program_outcome_id']);
            });
        }
    }
};
