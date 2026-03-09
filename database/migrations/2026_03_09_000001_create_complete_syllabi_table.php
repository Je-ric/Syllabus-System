<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complete_syllabi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('syllabus_id')->constrained('syllabi')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('academic_year');
            $table->string('semester');
            $table->string('pdf_path');
            $table->unsignedInteger('version');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('checksum')->nullable();
            $table->timestamps();

            $table->unique(['syllabus_id', 'version']);
            $table->index(['course_id', 'academic_year', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complete_syllabi');
    }
};
