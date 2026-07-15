<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds CAIS foreign ID columns alongside existing local FKs.
 * Local FKs are NOT dropped here — that happens in a later phase
 * once data has been migrated and the local tables are confirmed redundant.
 */
return new class extends Migration
{
    public function up(): void
    {
        // colleges table — add cais_college_id
        Schema::table('colleges', function (Blueprint $table) {
            $table->unsignedBigInteger('cais_college_id')->nullable()->unique()->after('id');
        });

        // departments table — add cais_department_id + cais_college_id
        Schema::table('departments', function (Blueprint $table) {
            $table->unsignedBigInteger('cais_department_id')->nullable()->unique()->after('id');
            $table->unsignedBigInteger('cais_college_id')->nullable()->index()->after('cais_department_id');
        });

        // college_goals — add cais_college_id alongside existing college_id FK
        Schema::table('college_goals', function (Blueprint $table) {
            $table->unsignedBigInteger('cais_college_id')->nullable()->index()->after('college_id');
        });

        // department_objectives — add cais_department_id alongside existing department_id FK
        Schema::table('department_objectives', function (Blueprint $table) {
            $table->unsignedBigInteger('cais_department_id')->nullable()->index()->after('department_id');
        });

        // program_departments — add cais_department_id alongside existing department_id FK
        Schema::table('program_departments', function (Blueprint $table) {
            $table->unsignedBigInteger('cais_department_id')->nullable()->index()->after('department_id');
        });

        // user_assignments — add cais_college_id + cais_department_id alongside existing FKs
        Schema::table('user_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('cais_college_id')->nullable()->index()->after('college_id');
            $table->unsignedBigInteger('cais_department_id')->nullable()->index()->after('department_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_assignments', function (Blueprint $table) {
            $table->dropIndex(['cais_college_id']);
            $table->dropIndex(['cais_department_id']);
            $table->dropColumn(['cais_college_id', 'cais_department_id']);
        });

        Schema::table('program_departments', function (Blueprint $table) {
            $table->dropIndex(['cais_department_id']);
            $table->dropColumn('cais_department_id');
        });

        Schema::table('department_objectives', function (Blueprint $table) {
            $table->dropIndex(['cais_department_id']);
            $table->dropColumn('cais_department_id');
        });

        Schema::table('college_goals', function (Blueprint $table) {
            $table->dropIndex(['cais_college_id']);
            $table->dropColumn('cais_college_id');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropUnique(['cais_department_id']);
            $table->dropIndex(['cais_college_id']);
            $table->dropColumn(['cais_department_id', 'cais_college_id']);
        });

        Schema::table('colleges', function (Blueprint $table) {
            $table->dropUnique(['cais_college_id']);
            $table->dropColumn('cais_college_id');
        });
    }
};
