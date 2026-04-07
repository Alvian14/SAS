<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('table_report_disrepancy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_student')->constrained('students')->onDelete('cascade');
            $table->string('student_name');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade'); // admin or teacher who report the discrepancy
            $table->foreignId('id_attendance_history')->constrained('attendance_histories')->onDelete('cascade'); // for specific attendance record that has discrepancy
            // $table->foreignId('id_schedule')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('id_class')->constrained('clases')->onDelete('cascade');
            $table->enum('disrepancy_type', ['terlambat', 'hp_tidak_tersedia', 'izin', 'pulang_awal', 'alasan_lain'])->nullable(); // jenis ketidaksesuaian
            $table->text('description')->nullable();
            $table->boolean('marked_as_resolved')->default(false);
            $table->date('attendance_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_report_disrepancy');
    }
};
