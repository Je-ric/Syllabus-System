<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * cais_teaching_loads
 *
 * Local sync copy of CAIS teaching load records.
 * A teaching load = one faculty user assigned to one class schedule in a semester.
 *
 * external_id          = teaching_load_id from CAIS
 * external_*_id        = raw CAIS IDs kept for reference if the local FK is null
 * user_id              = FK to CSMS users — matched via users.cais_user_id
 * cais_semester_id     = FK to cais_semesters (local copy)
 * cais_class_schedule_id = FK to cais_class_schedules (local copy)
 *
 * Used by: syllabus wizard step 2 to pre-fill course component details
 *          (instructor name, schedule, subject) from the faculty's active teaching load.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cais_teaching_loads', function (Blueprint $table) {
            $table->id();

            // CAIS reference
            $table->unsignedBigInteger('external_id')->unique(); // teaching_load_id from CAIS

            // Raw CAIS foreign IDs — kept even when local FKs are resolved
            $table->unsignedBigInteger('external_user_id')->nullable()->index();
            $table->unsignedBigInteger('external_semester_id')->nullable()->index();
            $table->unsignedBigInteger('external_schedule_id')->nullable()->index(); // schedId from CAIS

            // Local FK links
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('cais_semester_id')
                ->nullable()
                ->constrained('cais_semesters')
                ->nullOnDelete();

            $table->foreignId('cais_class_schedule_id')
                ->nullable()
                ->constrained('cais_class_schedules')
                ->nullOnDelete();

            $table->boolean('is_deleted')->default(false); // soft-delete flag from CAIS

            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cais_teaching_loads');
    }
};
