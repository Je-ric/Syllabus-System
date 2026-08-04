<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complete_syllabi', function (Blueprint $table) {
            // HTML snapshot of the F.003 review form frozen at version save time.
            // Stored as a file path pointing to the saved HTML file on disk,
            // mirroring how pdf_path / abridged_path / evaluation_path work.
            $table->string('review_form_path')->nullable()->after('evaluation_path');
            $table->string('checksum_review_form', 64)->nullable()->after('checksum_evaluation');
        });
    }

    public function down(): void
    {
        Schema::table('complete_syllabi', function (Blueprint $table) {
            $table->dropColumn(['review_form_path', 'checksum_review_form']);
        });
    }
};
