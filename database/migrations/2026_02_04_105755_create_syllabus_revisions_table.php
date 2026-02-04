<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_revisions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('syllabus_id')
                ->constrained('syllabi')
                ->cascadeOnDelete();

            $table->unsignedInteger('revision_no')->default(0);
            $table->date('revision_date');
            $table->string('implementation_semester'); // ex. "1st Sem 2025-2026"
            $table->text('highlights')->nullable();
            $table->text('contributors')->nullable();

            $table->timestamps();

            $table->unique(['syllabus_id', 'revision_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_revisions');
    }
};
