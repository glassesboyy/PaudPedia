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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name'); // if user_id null
            $table->string('title')->nullable(); // ex: "Kepala Sekolah TK Melati"
            $table->text('content');
            $table->integer('rating')->default(5); // 1-5
            $table->text('photo_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('is_featured');
            $table->index('is_approved');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
