<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_reviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('syllabus_id')
                ->constrained('syllabi')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->timestamps();

            // Ensure a user can only be assigned once per syllabus
            $table->unique(['syllabus_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_reviewers');
    }
};
