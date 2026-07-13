<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlToBannarsTable extends Migration
{
    public function up()
    {
        Schema::table('bannars', function (Blueprint $table) {
            $table->string('url')->nullable()->after('button_text');
        });
    }

    public function down()
    {
        Schema::table('bannars', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
}
