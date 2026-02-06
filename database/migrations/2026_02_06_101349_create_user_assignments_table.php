<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('college_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Contextual responsibility
            $table->enum('context', ['faculty', 'chair', 'dean']);

            $table->timestamps();

            /**
             * Prevent duplicate assignments in same scope
             * Example: same user cannot be chair of same department twice
             */
            $table->unique([
                'user_id',
                'college_id',
                'department_id',
                'context'
            ], 'user_assignments_unique_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_assignments');
    }
};
