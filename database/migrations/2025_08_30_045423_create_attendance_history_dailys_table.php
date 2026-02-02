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
        Schema::create('attendance_history_dailys', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['tepat_waktu', 'terlambat']);
            $table->string('picture');
            $table->timestamps();
            // Relationship
            $table->foreignId('id_student')->constrained('students')->onDelete('cascade');
            $table->foreignId('id_class')->constrained('clases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_history_dailys');
    }
};
