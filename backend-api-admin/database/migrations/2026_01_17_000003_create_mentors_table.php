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
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable(); // ex: "M.Psi, Psikolog"
            $table->text('bio')->nullable();
            $table->text('photo_url')->nullable();
            $table->string('expertise')->nullable(); // ex: "Parenting Expert"
            $table->json('social_media')->nullable(); // {instagram: "", linkedin: ""}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentors');
    }
};
