<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_report_id')->constrained('student_reports')->onDelete('cascade');
            $table->foreignId('program_id')->constrained('development_programs')->onDelete('cascade');
            $table->text('narrative'); // The narrative paragraph for this development program
            $table->timestamps();

            $table->unique(['student_report_id', 'program_id'], 'uq_report_program');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_report_details');
    }
};
