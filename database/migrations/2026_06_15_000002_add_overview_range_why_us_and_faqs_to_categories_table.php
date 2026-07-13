<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->longText('overview')->nullable()->after('short_description');
            $table->longText('our_range')->nullable()->after('overview');
            $table->longText('why_us')->nullable()->after('our_range');
            $table->json('faqs')->nullable()->after('why_us');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['overview', 'our_range', 'why_us', 'faqs']);
        });
    }
};
