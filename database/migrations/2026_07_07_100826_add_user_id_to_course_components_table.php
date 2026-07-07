<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_components', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('syllabus_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->dropColumn(['instructor_name', 'instructor_email', 'phone', 'office']);
        });
    }

    public function down(): void
    {
        Schema::table('course_components', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->string('instructor_name')->default('');
            $table->string('instructor_email')->default('');
            $table->string('phone')->nullable();
            $table->string('office')->nullable();
        });
    }
};
