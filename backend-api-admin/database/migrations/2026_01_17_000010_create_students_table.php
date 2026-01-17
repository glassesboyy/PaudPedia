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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->foreignId('parent_profile_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('nisn')->nullable(); // Nomor Induk Siswa Nasional
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->text('address')->nullable();
            $table->text('photo_url')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->enum('status', ['active', 'graduated', 'transferred'])->default('active');
            $table->timestamps();
            
            // Indexes
            $table->index('school_id');
            $table->index('class_id');
            $table->index('parent_profile_id');
            $table->index('status');
            $table->index(['school_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
