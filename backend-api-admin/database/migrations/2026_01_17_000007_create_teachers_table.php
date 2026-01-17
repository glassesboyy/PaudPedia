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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('nip')->nullable();
            $table->string('specialization')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['school_id', 'user_id']);
            $table->index('user_id');
            
            // Unique: one teacher profile per user per school
            $table->unique(['school_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
