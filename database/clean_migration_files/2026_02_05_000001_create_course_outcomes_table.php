<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('syllabus_id')->constrained('syllabi')->cascadeOnDelete();
            $table->string('co_code'); // CO1, CO2, CO3...
            $table->text('description');
            $table->timestamps();

            $table->unique(['syllabus_id', 'co_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_outcomes');
    }
};
