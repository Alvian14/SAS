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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('day_of_week');
            $table->integer('period_start');
            $table->integer('period_end');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('code');
            $table->timestamps();

            // Relationship
            $table->foreignId('id_class')->constrained('clases')->onDelete('cascade');
            $table->foreignId('id_subject')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('id_teacher')->constrained('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
