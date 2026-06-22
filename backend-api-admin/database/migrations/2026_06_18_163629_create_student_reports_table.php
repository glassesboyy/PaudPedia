<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->enum('semester', ['1', '2']);
            $table->string('academic_year');
            $table->text('introduction_notes')->nullable();
            $table->text('closing_notes')->nullable();
            $table->text('recommendation')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'semester', 'academic_year'], 'uq_student_report_term');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};
