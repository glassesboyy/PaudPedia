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
        Schema::create('school_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role_type', ['headmaster', 'teacher', 'parent']);
            $table->boolean('is_active')->default(true);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            
            // Indexes
            $table->index(['school_id', 'user_id', 'role_type']);
            $table->index('role_type');
            $table->index('is_active');
            
            // Unique constraint: one user can have one role per school
            $table->unique(['school_id', 'user_id', 'role_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_members');
    }
};
