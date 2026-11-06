<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedInteger('sales_code_id')->nullable()->after('customer_code');
            $table->foreign('sales_code_id')->references('id')->on('sales_codes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['sales_code_id']);
            $table->dropColumn('sales_code_id');
        });
    }
};
