<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_review_nature_of_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_form_id')
                ->constrained('syllabus_review_forms')
                ->cascadeOnDelete();

            // Part C — which checkbox the author selected
            // Updating track: schedule_calendar, faculty_contact, references_textbooks,
            //                 typographical_formatting, minor_administrative, other_updating
            // Revision track: stakeholder_feedback, cqi_findings, policy_curricular,
            //                 accreditation_qa, change_in_cos_po_mapping,
            //                 change_in_grading_assessments_content, other_revision
            $table->enum('change_type', [
                // Updating
                'schedule_calendar',
                'faculty_contact',
                'references_textbooks',
                'typographical_formatting',
                'minor_administrative',
                'other_updating',
                // Revision
                'stakeholder_feedback',
                'cqi_findings',
                'policy_curricular',
                'accreditation_qa',
                'change_in_cos_po_mapping',
                'change_in_grading_assessments_content',
                'other_revision',
            ]);

            $table->timestamps();

            // Each change type can only be selected once per form, uq means = unique
            $table->unique(['review_form_id', 'change_type'], 'uq_rf_change_type');
            $table->index('review_form_id', 'idx_rf_nature_form_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_review_nature_of_changes');
    }
};
