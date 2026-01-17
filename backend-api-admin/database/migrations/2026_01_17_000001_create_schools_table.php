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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('npsn', 20)->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('logo_url')->nullable();
            
            // Subscription
            $table->enum('subscription_plan', ['free', 'pro'])->default('free');
            $table->timestamp('subscription_started_at')->nullable();
            $table->timestamp('subscription_ended_at')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('subscription_plan');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
