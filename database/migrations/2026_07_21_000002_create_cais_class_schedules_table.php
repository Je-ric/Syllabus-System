<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * cais_class_schedules
 *
 * Local sync copy of CAIS class schedule records (subject offerings per semester).
 * NOTE: "course" in CAIS = a subject offering/section, NOT a curriculum course.
 *       This is completely separate from the CSMS `courses` table.
 *
 * external_id        = schedId from CAIS
 * external_*_id      = raw CAIS IDs kept for reference if the local FK is null
 * cais_semester_id   = FK to cais_semesters (local copy)
 * course_id          = FK to CSMS courses — nullable, matched by subject_code vs course_code
 * department_id      = FK to CSMS departments — nullable, matched via cais_department_id
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cais_class_schedules', function (Blueprint $table) {
            $table->id();

            // CAIS reference
            $table->unsignedBigInteger('external_id')->unique(); // schedId from CAIS

            // Raw CAIS foreign IDs — kept even when local FKs are resolved
            $table->unsignedBigInteger('external_semester_id')->nullable()->index();
            $table->unsignedBigInteger('external_department_id')->nullable()->index();

            // Local FK links
            $table->foreignId('cais_semester_id')
                ->nullable()
                ->constrained('cais_semesters')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            // Subject details
            $table->string('subject_code', 50)->nullable();
            $table->string('subject_title')->nullable();
            $table->decimal('units', 5, 2)->nullable();
            $table->string('section', 50)->nullable();
            $table->string('room', 100)->nullable();
            $table->string('time', 100)->nullable();       // e.g. "MWF 7:30-8:30"
            $table->string('class_type', 30)->nullable();  // LEC, LAB
            $table->string('lab_type', 30)->nullable();

            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cais_class_schedules');
    }
};
