<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('program_eos', function (Blueprint $table) {
            $table->string('peo_code')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('program_eos', function (Blueprint $table) {
            $table->string('peo_code')->nullable(false)->change();
        });
    }
};
