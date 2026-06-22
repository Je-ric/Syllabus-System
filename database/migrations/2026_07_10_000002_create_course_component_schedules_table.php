<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_component_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_component_id')
                ->constrained('course_components')
                ->cascadeOnDelete();
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
            $table->string('time'); // e.g. "07:30 AM - 09:00 AM"
            $table->timestamps();
        });

        // Drop the old single-value schedule column
        Schema::table('course_components', function (Blueprint $table) {
            $table->dropColumn('schedule');
        });
    }

    public function down(): void
    {
        Schema::table('course_components', function (Blueprint $table) {
            $table->text('schedule')->nullable()->after('class_hours');
        });

        Schema::dropIfExists('course_component_schedules');
    }
};
