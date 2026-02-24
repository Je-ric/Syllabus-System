<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_references', function (Blueprint $table) {
            $table->id();

            $table->foreignId('syllabus_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('reference_text'); // Full citation text

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_references');
    }
};
