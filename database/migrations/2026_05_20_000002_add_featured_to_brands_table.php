<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeaturedToBrandsTable extends Migration
{
    public function up()
    {
        Schema::table('brands', function (Blueprint $table) {
            // Only add if it doesn't already exist (the 'top' column is already there
            // and will be reused, so we only add 'featured' as a new dedicated column)
            if (!Schema::hasColumn('brands', 'featured')) {
                $table->tinyInteger('featured')->default(0)->after('top');
            }
        });
    }

    public function down()
    {
        Schema::table('brands', function (Blueprint $table) {
            if (Schema::hasColumn('brands', 'featured')) {
                $table->dropColumn('featured');
            }
        });
    }
}
