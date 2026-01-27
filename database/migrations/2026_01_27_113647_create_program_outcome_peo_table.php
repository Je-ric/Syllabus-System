<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_outcome_peo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_outcome_id')
                ->constrained('program_outcomes')
                ->cascadeOnDelete();

            $table->foreignId('program_eo_id') // updated to new table name
                ->constrained('program_eos')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['program_outcome_id', 'program_eo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_outcome_peo');
    }
};
