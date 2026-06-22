<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_consultation_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']);
            $table->string('time'); // e.g. "01:00 PM - 03:00 PM"
            $table->timestamps();
        });

        // Drop the old single-value column from course_components
        Schema::table('course_components', function (Blueprint $table) {
            $table->dropColumn('consultation_hours');
        });
    }

    public function down(): void
    {
        Schema::table('course_components', function (Blueprint $table) {
            $table->string('consultation_hours')->nullable()->after('office');
        });

        Schema::dropIfExists('user_consultation_hours');
    }
};
