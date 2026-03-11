<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Optimise the courses table for public-facing list / filter queries.
     *
     * Mirrors the approach used in optimize_products_table_indexes:
     *  1. Composite indexes for common query patterns
     *  2. FULLTEXT index for keyword search
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Composite: published listing (covers base published() + orderBy created_at)
            $table->index(
                ['is_published', 'created_at', 'deleted_at'],
                'idx_courses_published_listing'
            );

            // Composite: price filtering on published courses
            $table->index(
                ['is_published', 'price'],
                'idx_courses_published_price'
            );

            // Composite: category filtering on published courses
            $table->index(
                ['is_published', 'category_id', 'created_at'],
                'idx_courses_published_category'
            );

            // Composite: level filtering on published courses
            $table->index(
                ['is_published', 'level', 'created_at'],
                'idx_courses_published_level'
            );
        });

        // FULLTEXT index for keyword search on title + description
        DB::statement(
            'ALTER TABLE courses ADD FULLTEXT INDEX idx_courses_fulltext (title, description)'
        );
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('idx_courses_published_listing');
            $table->dropIndex('idx_courses_published_price');
            $table->dropIndex('idx_courses_published_category');
            $table->dropIndex('idx_courses_published_level');
        });

        DB::statement('ALTER TABLE courses DROP INDEX idx_courses_fulltext');
    }
};
