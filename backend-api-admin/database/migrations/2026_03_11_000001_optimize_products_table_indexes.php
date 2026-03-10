<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Optimise the products table for public-facing list / filter queries.
     *
     * Mirrors the approach used in optimize_webinars_table_indexes:
     *  1. Composite indexes for common query patterns
     *  2. FULLTEXT index for keyword search
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Composite: active listing (covers base active() + orderBy created_at)
            $table->index(
                ['is_active', 'created_at', 'deleted_at'],
                'idx_products_active_listing'
            );

            // Composite: price filtering on active products
            $table->index(
                ['is_active', 'price'],
                'idx_products_active_price'
            );

            // Composite: category filtering on active products
            $table->index(
                ['is_active', 'category_id', 'created_at'],
                'idx_products_active_category'
            );
        });

        // FULLTEXT index for keyword search on title + description
        DB::statement(
            'ALTER TABLE products ADD FULLTEXT INDEX idx_products_fulltext (title, description)'
        );
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_active_listing');
            $table->dropIndex('idx_products_active_price');
            $table->dropIndex('idx_products_active_category');
            $table->dropFullText('idx_products_fulltext');
        });
    }
};
