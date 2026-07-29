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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->increments('id');

            /**
             * receive | issue | adjust | waste
             */
            $table->string('type');

            $table->integer('product_id')->unsigned();
            $table->integer('product_batch_id')->unsigned()->nullable();
            $table->integer('inventory_source_id')->unsigned();

            /**
             * Signed: positive adds stock, negative removes it, so a running
             * balance is just a SUM over this column.
             */
            $table->integer('qty');

            $table->string('reason')->nullable();
            $table->text('notes')->nullable();

            /**
             * What caused the movement, e.g. a shipment. Left loose on
             * purpose so this ledger does not have to know about every
             * entity that might move stock.
             */
            $table->string('reference_type')->nullable();
            $table->integer('reference_id')->unsigned()->nullable();

            $table->integer('user_id')->unsigned()->nullable();

            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_batch_id')->references('id')->on('product_batches')->onDelete('set null');
            $table->foreign('inventory_source_id')->references('id')->on('inventory_sources')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
