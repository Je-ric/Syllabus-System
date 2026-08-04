<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_review_checklist_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_form_id')
                ->constrained('syllabus_review_forms')
                ->cascadeOnDelete();
            $table->foreignId('reviewer_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Part E — which section the criterion belongs to
            // A = Document Control, B = OBE Alignment,
            // C_updating = Updating-specific, C_revision = Revision-specific
            $table->enum('section', ['A', 'B', 'C_updating', 'C_revision']);

            // Criterion code matching ReviewCriteria definitions
            // e.g. A1, A2, B1, B2, CU1, CU2, CR1, CR2…
            $table->string('criterion_code', 10);

            // The reviewer's response for this criterion
            $table->enum('response', ['satisfied', 'not_satisfied', 'not_applicable'])->nullable();

            // Optional written comment per criterion
            $table->text('comments')->nullable();

            $table->timestamps();

            // One response per reviewer per criterion per form
            $table->unique(['review_form_id', 'reviewer_user_id', 'criterion_code'], 'unique_reviewer_criterion');
            $table->index(['review_form_id', 'reviewer_user_id'], 'idx_rf_checklist_reviewer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_review_checklist_responses');
    }
};
