<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_review_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('syllabus_id')
                ->unique()
                ->constrained('syllabi')
                ->cascadeOnDelete();

            // Part B — classification chosen by the syllabus author
            $table->enum('classification', ['updating', 'revision'])->nullable();

            // Part A — course lead name (auto-filled from author, editable)
            $table->string('course_lead_name')->nullable();

            // Submission timestamp — set when author submits for review
            $table->timestamp('submitted_at')->nullable();

            // Part F — decision recorded by the CQI Committee Chair
            $table->enum('decision', [
                'approved_as_updating',
                'approved_as_revision',
                'approved_with_corrections',
                'returned_for_revision',
                'reclassified_as_revision',
            ])->nullable();
            $table->timestamp('decision_made_at')->nullable();
            $table->text('required_actions')->nullable();       // what the faculty must fix
            $table->date('target_compliance_date')->nullable(); // deadline for corrections

            // Part H — faculty response to required actions, and verification
            $table->text('part_h_faculty_response')->nullable();
            $table->foreignId('part_h_verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('part_h_verified_at')->nullable();

            // Part I — Chair recommendation and Dean final approval
            $table->foreignId('recommended_by_chair_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('recommended_by_chair_at')->nullable();

            $table->foreignId('approved_by_dean_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('approved_by_dean_at')->nullable();

            // Filing info — set after dean approval
            $table->timestamp('filed_at')->nullable();
            $table->enum('filing_type', ['updating_department', 'revision_oloi'])->nullable();

            // HTML snapshot of the complete F.003 form — frozen when saveAsDone() is called
            $table->longText('review_form_snapshot')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_review_forms');
    }
};
