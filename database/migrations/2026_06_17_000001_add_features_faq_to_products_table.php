<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Both columns already exist in production — this migration is a no-op safety guard.
class AddFeaturesFaqToProductsTable extends Migration
{
    public function up()
    {
        // faq already exists on products; features already exists on product_stocks.
        // No changes needed.
    }

    public function down()
    {
        // Nothing to roll back.
    }
}
