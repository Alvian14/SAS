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
        Schema::create('attendance_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('period_number');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha']);
            $table->timestamps();

            // Relationship
            $table->foreignId('id_student')->constrained('students')->onDelete('cascade');
            $table->foreignId('id_schedule')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('id_class')->constrained('clases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_histories');
    }
};
