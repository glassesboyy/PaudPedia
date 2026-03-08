<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Optimizes the articles table for common public query patterns:
     * - Composite index for published article listing (most common query)
     * - Composite index for featured published articles
     * - Fulltext search index on title + excerpt (replaces LIKE %% on content)
     * - Pre-computed reading_time column (avoids strip_tags on every list row)
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Pre-computed reading time to avoid strip_tags() on content for every list item
            $table->unsignedSmallInteger('reading_time')->default(1)->after('view_count')
                ->comment('Pre-computed reading time in minutes');

            // Composite index: covers scopePublished() + orderBy published_at (most common query)
            $table->index(['is_published', 'published_at', 'deleted_at'], 'idx_articles_published_listing');

            // Composite index: covers featured + published queries
            $table->index(['is_featured', 'is_published', 'published_at'], 'idx_articles_featured_published');

            // Composite index: category + published (for byCategory queries)
            $table->index(['category_id', 'is_published', 'published_at'], 'idx_articles_category_published');
        });

        // Fulltext index for search — MySQL specific
        DB::statement('ALTER TABLE articles ADD FULLTEXT INDEX idx_articles_fulltext (title, excerpt)');

        // Backfill reading_time for existing articles
        DB::statement("
            UPDATE articles
            SET reading_time = GREATEST(1, CEIL(
                (LENGTH(REGEXP_REPLACE(content, '<[^>]*>', '')) - LENGTH(REPLACE(REGEXP_REPLACE(content, '<[^>]*>', ''), ' ', ''))) / 200
            ))
            WHERE content IS NOT NULL AND content != ''
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('idx_articles_published_listing');
            $table->dropIndex('idx_articles_featured_published');
            $table->dropIndex('idx_articles_category_published');
            $table->dropFullText('idx_articles_fulltext');
            $table->dropColumn('reading_time');
        });
    }
};
