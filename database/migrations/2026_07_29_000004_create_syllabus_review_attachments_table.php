<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabus_review_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_form_id')
                ->constrained('syllabus_review_forms')
                ->cascadeOnDelete();

            // Part D — documentary attachments checklist
            $table->enum('attachment_type', [
                'draft_syllabus',
                'cqi_report',
                'feedback_summary',
                'policy_memo',
                'mapping_evidence',
                'other',
            ]);
            $table->boolean('is_submitted')->default(false);

            // Only used when attachment_type = 'other'
            $table->string('other_label')->nullable();

            $table->timestamps();

            // Each attachment type can only appear once per form
            $table->unique(['review_form_id', 'attachment_type'], 'uq_rf_attachment_type');
            $table->index('review_form_id', 'idx_rf_attachments_form_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabus_review_attachments');
    }
};
