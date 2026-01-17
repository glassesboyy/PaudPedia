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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('aspect'); // ex: "Nilai Agama dan Moral", "Kognitif", "Bahasa"
            $table->text('description'); // ex: "Mampu berdoa sebelum dan sesudah kegiatan"
            $table->enum('scale', ['BB', 'MB', 'BSH', 'BSB']); // PAUD assessment scale
            $table->enum('semester', ['1', '2']);
            $table->string('academic_year'); // ex: "2024/2025"
            $table->text('notes')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('student_id');
            $table->index('semester');
            $table->index(['student_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
