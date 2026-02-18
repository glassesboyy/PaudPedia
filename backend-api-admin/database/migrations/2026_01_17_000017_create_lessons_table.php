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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('content_type', ['video', 'pdf', 'text']); // removed 'quiz'
            $table->text('video_url')->nullable(); // untuk tipe video (YouTube embed URL)
            $table->string('pdf_file')->nullable(); // untuk tipe pdf (file upload path)
            $table->longText('text_content')->nullable(); // untuk tipe text (rich text editor)
            $table->integer('duration_minutes')->nullable();
            $table->integer('order')->default(0); // sequence
            $table->timestamps();
            
            // Indexes
            $table->index('module_id');
            $table->index(['module_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
