<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Each territory belongs to exactly one sales code in the source data, so
     * the mapping is kept on the territory itself.
     */
    public function up(): void
    {
        Schema::table('sales_territories', function (Blueprint $table) {
            $table->integer('sales_code_id')->unsigned()->nullable()->after('name');

            $table->foreign('sales_code_id')->references('id')->on('sales_codes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_territories', function (Blueprint $table) {
            $table->dropForeign(['sales_code_id']);
            $table->dropColumn('sales_code_id');
        });
    }
};
