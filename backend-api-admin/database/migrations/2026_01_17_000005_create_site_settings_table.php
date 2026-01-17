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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // ex: "site_logo", "contact_email"
            $table->text('value')->nullable(); // JSON for complex values
            $table->string('type')->default('string'); // string, json, boolean, integer
            $table->string('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
