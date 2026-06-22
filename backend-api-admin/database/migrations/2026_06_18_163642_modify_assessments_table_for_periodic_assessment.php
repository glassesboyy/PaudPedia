<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First drop the old string columns and add the new FK and month fields
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['aspect', 'description']);
            
            // Add new relational and specific columns
            $table->foreignId('indicator_id')->nullable()->after('student_id')->constrained('development_indicators')->onDelete('cascade');
            $table->string('assessment_month')->nullable()->after('indicator_id'); // e.g. "2024-01"
            
            // We can add a unique constraint to ensure a student only gets 1 assessment per indicator per month
            $table->unique(['student_id', 'indicator_id', 'assessment_month'], 'uq_assessment_monthly');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['indicator_id']);
            $table->dropUnique('uq_assessment_monthly');
            $table->dropColumn(['indicator_id', 'assessment_month']);
            
            $table->string('aspect')->after('student_id');
            $table->text('description')->after('aspect');
        });
    }
};
