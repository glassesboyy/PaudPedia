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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->string('name'); // ex: "Kelas A", "Kelas B"
            $table->string('level')->nullable(); // ex: "TK A", "TK B", "KB"
            $table->integer('capacity')->nullable();
            $table->string('academic_year')->nullable(); // ex: "2024/2025"
            $table->timestamps();
            
            // Indexes
            $table->index('school_id');
            $table->index('homeroom_teacher_id');
            
            // Unique: class name per school per academic year
            $table->unique(['school_id', 'name', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
