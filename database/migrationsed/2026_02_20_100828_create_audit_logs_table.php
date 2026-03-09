<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // What happened
            $table->string('action');
            // created, updated, deleted, login, approved, rejected, etc.

            // What model/module
            $table->string('module');
            // User, Syllabus, Course, etc.

            // Affected record
            $table->unsignedBigInteger('reference_id')->nullable();

            // Human explanation
            $table->text('description')->nullable();

            // When it happened
            $table->timestamp('timestamp')->useCurrent();

            $table->timestamps();

            // INDEXES (important for filtering logs)
            $table->index('user_id');
            $table->index('module');
            $table->index('reference_id');
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
