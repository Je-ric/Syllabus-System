<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_materials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('syllabus_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('material_name'); // e.g. "Week 1 Slides"
            $table->string('url'); // Resource link

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_materials');
    }
};
