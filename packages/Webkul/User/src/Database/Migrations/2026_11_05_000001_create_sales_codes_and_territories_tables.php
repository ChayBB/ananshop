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
        Schema::create('sales_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('sales_territories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->integer('sales_code_id')->unsigned()->nullable()->after('role_id');
            $table->integer('sales_territory_id')->unsigned()->nullable()->after('sales_code_id');

            $table->foreign('sales_code_id')->references('id')->on('sales_codes')->onDelete('set null');
            $table->foreign('sales_territory_id')->references('id')->on('sales_territories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['sales_code_id']);
            $table->dropForeign(['sales_territory_id']);
            $table->dropColumn(['sales_code_id', 'sales_territory_id']);
        });

        Schema::dropIfExists('sales_territories');
        Schema::dropIfExists('sales_codes');
    }
};
