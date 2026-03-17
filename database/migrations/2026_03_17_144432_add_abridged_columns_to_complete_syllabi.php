<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complete_syllabi', function (Blueprint $table) {
            $table->string('abridged_path')->nullable()->after('pdf_path');
            $table->string('checksum_abridged', 64)->nullable()->after('checksum');
            
            $table->string('evaluation_path')->nullable()->after('pdf_path');
            $table->string('checksum_evaluation', 64)->nullable()->after('checksum');
        });
    }

    public function down(): void
    {
        Schema::table('complete_syllabi', function (Blueprint $table) {
            $table->dropColumn(['abridged_path', 'checksum_abridged', 'evaluation_path', 'checksum_evaluation']);
        });
    }
};