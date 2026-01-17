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
        Schema::create('parent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('email'); // unique per school
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('phone', 20);
            $table->string('father_occupation')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['school_id', 'user_id']);
            $table->index('email');
            
            // Unique: email per school
            $table->unique(['school_id', 'email']);
            
            // Unique: one parent profile per user per school
            $table->unique(['school_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_profiles');
    }
};
