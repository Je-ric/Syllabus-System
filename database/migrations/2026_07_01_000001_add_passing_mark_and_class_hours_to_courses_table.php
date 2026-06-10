<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('passing_mark', 5, 2)->default(60.00)->after('has_lec_lab');
            $table->string('lec_class_hours')->nullable()->after('passing_mark');
            $table->string('lab_class_hours')->nullable()->after('lec_class_hours');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['passing_mark', 'lec_class_hours', 'lab_class_hours']);
        });
    }
};
