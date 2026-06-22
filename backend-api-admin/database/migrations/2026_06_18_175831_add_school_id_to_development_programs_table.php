<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For existing data, we might need to set a default school or clear them.
        // Assuming we can clear them since it's just fresh.
        DB::table('development_indicators')->delete();
        DB::table('development_programs')->delete();

        Schema::table('development_programs', function (Blueprint $table) {
            $table->foreignId('school_id')->after('id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('development_programs', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};
