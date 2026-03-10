<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Optimise the webinars table for public-facing list / filter queries.
     *
     * Mirrors the approach used in optimize_articles_table_indexes:
     *  1. Composite indexes for common query patterns
     *  2. FULLTEXT index for keyword search
     */
    public function up(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            // Composite: active listing (covers the base active() + orderBy scheduled_at)
            $table->index(
                ['is_active', 'scheduled_at', 'deleted_at'],
                'idx_webinars_active_listing'
            );

            // Composite: price filtering on active webinars
            $table->index(
                ['is_active', 'price'],
                'idx_webinars_active_price'
            );

            // Composite: mentor filtering on active webinars ordered by schedule
            $table->index(
                ['is_active', 'mentor_id', 'scheduled_at'],
                'idx_webinars_active_mentor_schedule'
            );
        });

        // FULLTEXT index for keyword search on title + description
        DB::statement(
            'ALTER TABLE webinars ADD FULLTEXT INDEX idx_webinars_fulltext (title, description)'
        );
    }

    public function down(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->dropIndex('idx_webinars_active_listing');
            $table->dropIndex('idx_webinars_active_price');
            $table->dropIndex('idx_webinars_active_mentor_schedule');
            $table->dropFullText('idx_webinars_fulltext');
        });
    }
};
