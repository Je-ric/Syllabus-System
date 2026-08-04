<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('syllabus_reviewers', function (Blueprint $table) {
            // Role of this reviewer on the review form
            // chair  — Program CQI Committee Chair (sole reviewer for Updating track)
            // member — Committee member (Revision track only, up to 2 per form)
            $table->enum('role', ['chair', 'member'])
                ->default('member')
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('syllabus_reviewers', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
