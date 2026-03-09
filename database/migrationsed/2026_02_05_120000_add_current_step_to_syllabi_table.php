<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            $table->string('current_step')->default('academic_calendar')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            $table->dropColumn('current_step');
        });
    }
};
