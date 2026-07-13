<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Products table — cover the most common search/filter columns
        Schema::table('products', function (Blueprint $table) {
            $table->index(['published', 'category_id'], 'idx_products_published_category');
            $table->index(['published', 'brand_id'],    'idx_products_published_brand');
            $table->index('name',                       'idx_products_name');
            $table->index('slug',                       'idx_products_slug');
        });

        // Product stocks — price range filter + product lookup
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->index(['product_id', 'price'], 'idx_stocks_product_price');
            $table->index('sku',                   'idx_stocks_sku');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_published_category');
            $table->dropIndex('idx_products_published_brand');
            $table->dropIndex('idx_products_name');
            $table->dropIndex('idx_products_slug');
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropIndex('idx_stocks_product_price');
            $table->dropIndex('idx_stocks_sku');
        });
    }
};
