<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('development_programs')->onDelete('cascade');
            $table->string('name'); // e.g. "Terbiasa mengucapkan kata-kata pujian"
            $table->integer('order')->default(0); // For sorting indicators
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_indicators');
    }
};
