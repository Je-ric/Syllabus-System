<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('course_code');
            $table->string('course_title');
            $table->text('course_description')->nullable();
            $table->unsignedTinyInteger('credit_units');
            $table->boolean('has_lec_lab')->default(false);

            // Curriculum placement
            $table->unsignedTinyInteger('year_level'); // 1–4 or 5
            $table->unsignedTinyInteger('semester');   // 1 or 2

            // Requisites
            $table->string('prerequisite')->nullable();
            $table->string('corequisite')->nullable();

            // Admin
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->unsignedTinyInteger('version')->default(1);
            $table->foreignId('created_by')
                    ->constrained('users')
                    ->cascadeOnDelete();

            $table->timestamps();

            // Prevent duplicate course codes within a program
            $table->unique(['program_id', 'course_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
