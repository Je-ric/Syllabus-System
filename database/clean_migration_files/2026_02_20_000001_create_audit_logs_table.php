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
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('action');  // created, updated, deleted, login, approved, etc.
            $table->string('module');  // User, Syllabus, Course, etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // affected record id
            $table->text('description')->nullable();
            $table->timestamp('timestamp')->useCurrent();

            $table->timestamps();

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
