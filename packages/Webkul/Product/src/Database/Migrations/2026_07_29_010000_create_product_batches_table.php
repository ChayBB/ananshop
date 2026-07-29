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
        Schema::create('product_batches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('batch_number');
            $table->integer('product_id')->unsigned();
            $table->integer('inventory_source_id')->unsigned();

            /**
             * qty is what is left in the batch; initial_qty is what was
             * received, kept so consumption can be reported on later.
             */
            $table->integer('qty')->default(0);
            $table->integer('initial_qty')->default(0);

            $table->decimal('unit_cost', 12, 4)->nullable();

            $table->date('received_at');

            /**
             * Derived from the product's shelf life at receiving time, but
             * stored per batch so changing the shelf life later cannot
             * silently move the expiry of stock already on the shelf.
             */
            $table->date('expired_at')->nullable();

            $table->timestamps();

            /**
             * FEFO reads this constantly: oldest expiry first, and batches
             * that still have stock left.
             */
            $table->index(['product_id', 'expired_at', 'qty'], 'product_batches_fefo_index');

            $table->unique(['product_id', 'inventory_source_id', 'batch_number'], 'product_batches_unique');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('inventory_source_id')->references('id')->on('inventory_sources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
