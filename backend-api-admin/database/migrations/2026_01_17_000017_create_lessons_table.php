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
            $table->enum('content_type', ['video', 'pdf', 'quiz', 'text']);
            $table->text('content_url')->nullable(); // YouTube embed URL or file URL
            $table->integer('duration_minutes')->nullable();
            $table->integer('order')->default(0); // sequence
            $table->boolean('is_preview')->default(false); // free preview
            $table->timestamps();
            
            // Indexes
            $table->index('module_id');
            $table->index(['module_id', 'order']);
            $table->index('is_preview');
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
