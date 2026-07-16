<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * cais_semesters
 *
 * Local sync copy of CAIS semester records.
 * external_id = semester_id from CAIS — used as the stable reference key.
 * academic_calendar_id = the matching CSMS academic calendar (nullable — not all CAIS
 *   semesters will have a corresponding CSMS calendar).
 * synced_at = last time this row was pulled from CAIS.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cais_semesters', function (Blueprint $table) {
            $table->id();

            // CAIS reference
            $table->unsignedBigInteger('external_id')->unique(); // semester_id from CAIS

            // Core fields from CAIS
            $table->string('name');                              // semester_name
            $table->unsignedTinyInteger('number')->nullable();  // semester_no (1 or 2)
            $table->string('year', 20)->nullable();             // semester_year e.g. "2024-2025"
            $table->string('status', 30)->nullable();           // semester_status: active, closed

            // Link to CSMS academic calendar (set manually or via admin mapping)
            $table->foreignId('academic_calendar_id')
                ->nullable()
                ->constrained('academic_calendars')
                ->nullOnDelete();

            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cais_semesters');
    }
};
