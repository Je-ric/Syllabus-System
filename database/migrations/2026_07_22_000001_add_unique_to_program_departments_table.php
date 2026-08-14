<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_departments', function (Blueprint $table) {
            $table->unique(['program_id', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::table('program_departments', function (Blueprint $table) {
            $table->dropUnique(['program_id', 'department_id']);
        });
    }
};
