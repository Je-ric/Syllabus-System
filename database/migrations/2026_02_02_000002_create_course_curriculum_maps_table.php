<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_curriculum_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('program_outcome_id')
                ->constrained('program_outcomes')
                ->cascadeOnDelete();

            // I = Introduced, E = Emphasized, D = Demonstrated
            $table->enum('ied', ['I', 'E', 'D']);

            $table->timestamps();

            // A course can map to a PO only once
            $table->unique(['course_id', 'program_outcome_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_curriculum_maps');
    }
};
