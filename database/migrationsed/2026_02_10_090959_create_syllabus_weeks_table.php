<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_weeks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('syllabus_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('week_no');
            $table->date('start_date');
            $table->date('end_date');

            $table->boolean('is_exam_week')->default(false);

            $table->timestamps();

            $table->unique(['syllabus_id', 'week_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_weeks');
    }
};
