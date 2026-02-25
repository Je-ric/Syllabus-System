<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('week_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('week_contents', 'course_outcome_id')) {
                $table->foreignId('course_outcome_id')
                    ->nullable()
                    ->after('component_type')
                    ->constrained('course_outcomes')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('week_contents', 'assessment_task')) {
                $table->text('assessment_task')->nullable()->after('learning_outcomes');
            }

            if (!Schema::hasColumn('week_contents', 'tla')) {
                $table->text('tla')->nullable()->after('topics');
            }
        });

        if (
            Schema::hasTable('co_assessment_plans')
            && Schema::hasColumn('week_contents', 'co_assessment_plan_id')
        ) {
            DB::table('week_contents')
                ->join('co_assessment_plans', 'co_assessment_plans.id', '=', 'week_contents.co_assessment_plan_id')
                ->update([
                    'week_contents.course_outcome_id' => DB::raw('co_assessment_plans.course_outcome_id'),
                    'week_contents.learning_outcomes' => DB::raw("COALESCE(NULLIF(week_contents.learning_outcomes, ''), co_assessment_plans.learning_outcomes)"),
                    'week_contents.assessment_task' => DB::raw("COALESCE(NULLIF(week_contents.assessment_task, ''), co_assessment_plans.assessment_name)"),
                    'week_contents.topics' => DB::raw("COALESCE(NULLIF(week_contents.topics, ''), co_assessment_plans.topic)"),
                    'week_contents.tla' => DB::raw("COALESCE(NULLIF(week_contents.tla, ''), co_assessment_plans.tla)"),
                ]);
        }

        Schema::table('week_contents', function (Blueprint $table) {
            if (Schema::hasColumn('week_contents', 'co_assessment_plan_id')) {
                $table->dropConstrainedForeignId('co_assessment_plan_id');
            }
        });

        Schema::dropIfExists('co_assessment_plans');
    }

    public function down(): void
    {
        Schema::create('co_assessment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_outcome_id')->constrained()->cascadeOnDelete();
            $table->string('assessment_name');
            $table->text('assessment_desc')->nullable();
            $table->string('tla');
            $table->string('learning_outcomes');
            $table->string('topic');
            $table->timestamps();
        });

        Schema::table('week_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('week_contents', 'co_assessment_plan_id')) {
                $table->foreignId('co_assessment_plan_id')
                    ->nullable()
                    ->after('component_type')
                    ->constrained('co_assessment_plans')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('week_contents', 'course_outcome_id')) {
                $table->dropConstrainedForeignId('course_outcome_id');
            }

            if (Schema::hasColumn('week_contents', 'assessment_task')) {
                $table->dropColumn('assessment_task');
            }

            if (Schema::hasColumn('week_contents', 'tla')) {
                $table->dropColumn('tla');
            }
        });
    }
};
