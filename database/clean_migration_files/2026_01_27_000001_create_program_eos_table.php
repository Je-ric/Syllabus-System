<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_eos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('peo_code')->nullable();
            $table->text('peo_text');
            $table->timestamps();

            $table->unique(['program_id', 'peo_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_eos');
    }
};
