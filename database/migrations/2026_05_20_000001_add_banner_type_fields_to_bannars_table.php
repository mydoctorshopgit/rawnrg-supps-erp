<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannerTypeFieldsToBannarsTable extends Migration
{
    public function up()
    {
        Schema::table('bannars', function (Blueprint $table) {
            // 'simple' or 'product'
            $table->string('banner_type')->default('simple')->after('status');
            // Product banner fields
            $table->string('sku')->nullable()->after('banner_type');
            $table->string('product_title')->nullable()->after('sku');
            $table->decimal('price', 10, 2)->nullable()->after('product_title');
            $table->decimal('vat', 10, 2)->nullable()->after('price');
            $table->string('button_text')->nullable()->after('vat');
        });
    }

    public function down()
    {
        Schema::table('bannars', function (Blueprint $table) {
            $table->dropColumn(['banner_type', 'sku', 'product_title', 'price', 'vat', 'button_text']);
        });
    }
}
