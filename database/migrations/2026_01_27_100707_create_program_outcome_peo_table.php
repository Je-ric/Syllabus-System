<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // One PO → many PEOs
    // One PEO → many POs
    
    public function up(): void
    {
        Schema::create('program_outcome_peo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_outcome_id')
                ->constrained('program_outcomes')
                ->cascadeOnDelete();

            $table->foreignId('program_peo_id')
                ->constrained('program_education_objectives')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['program_outcome_id', 'program_peo_id']);
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
