<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_histories', 'attendance_date')) {
                $table->date('attendance_date')->after('coordinate')->nullable()->index();
            }
        });

        // Backfill existing rows with their created_at date to maintain uniqueness
        DB::table('attendance_histories')
            ->whereNull('attendance_date')
            ->update(['attendance_date' => DB::raw('DATE(created_at)')]);

        Schema::table('attendance_histories', function (Blueprint $table) {
            $table->unique(['id_student', 'id_schedule', 'attendance_date'], 'attendance_unique_per_day');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->index(['code', 'id_class'], 'schedules_code_class_idx');
        });

        Schema::table('academic_periods', function (Blueprint $table) {
            $table->index('is_active', 'academic_periods_is_active_idx');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_histories', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_histories', 'attendance_date')) {
                $table->dropUnique('attendance_unique_per_day');
                $table->dropIndex('attendance_histories_attendance_date_index');
                $table->dropColumn('attendance_date');
            }
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('schedules_code_class_idx');
        });

        Schema::table('academic_periods', function (Blueprint $table) {
            $table->dropIndex('academic_periods_is_active_idx');
        });
    }
};
