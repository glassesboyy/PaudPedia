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
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['spp', 'tabungan']); // SPP or Savings
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->string('month'); // ex: "2024-01" for grouping SPP by month
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('student_id');
            $table->index('type');
            $table->index('month');
            $table->index('is_paid');
            $table->index(['student_id', 'type']);
            $table->index(['student_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};
