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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('period_start')->nullable();
            $table->integer('period_end')->nullable();
            $table->string('subject');
            $table->enum('reason', ['sakit', 'izin', 'dispen', 'lainnya']);
            $table->text('information')->nullable(true);
            $table->string('evidence');
            $table->enum('status', ['proses', 'diterima', 'ditolak']);
            $table->string('feedback')->nullable(true); // feedback message from admin
            $table->timestamp('date_permission'); // 

            // relationship foreign key
            $table->foreignId('id_student')->constrained('students')->onDelete('cascade');

            // timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
