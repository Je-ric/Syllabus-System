<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('syllabus_id')
                ->constrained('syllabi')
                ->cascadeOnDelete();
            $table->enum('type', ['LEC', 'LAB']);
            $table->string('class_hours'); // drop?
            $table->text('schedule')->nullable(); // comma-separated days/times

            $table->string('instructor_name');
            $table->string('instructor_email');
            $table->string('phone')->nullable();
            $table->string('office')->nullable();
            $table->string('consultation_hours')->nullable();

            // $table->enum('performance_standard', ['50%', '60%', '75%'])->default('50%');
            $table->decimal('performance_standard', 5, 2)->default(50.00); // passing mark

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_components');
    }
};
