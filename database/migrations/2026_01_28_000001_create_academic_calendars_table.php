<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('academic_year'); // e.g. 2025-2026
            $table->enum('semester', ['1st', '2nd']);
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('cais_semester_id')->nullable()->index();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_calendars');
    }
};
