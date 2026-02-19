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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained('course_enrollments')->onDelete('cascade');
            $table->integer('score')->default(0); // jumlah jawaban benar
            $table->integer('total_questions')->default(0); // total pertanyaan
            $table->decimal('percentage', 5, 2)->default(0); // persentase skor (0-100.00)
            $table->boolean('is_passed')->default(false); // lulus atau tidak
            $table->timestamp('started_at')->nullable(); // waktu mulai
            $table->timestamp('completed_at')->nullable(); // waktu selesai
            $table->timestamps();
            
            // Indexes
            $table->index('quiz_id');
            $table->index('user_id');
            $table->index('enrollment_id');
            $table->index(['user_id', 'quiz_id']);
            $table->index('is_passed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
