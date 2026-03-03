<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // College Goals
        Schema::create('college_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('college_goals_code');
            $table->text('goal_text');
            $table->timestamps();
        });

        // Department Objectives
        Schema::create('department_objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('dept_obj_code');
            $table->text('objective_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_objectives');
        Schema::dropIfExists('college_goals');
    }
};