<?php

namespace Webkul\Product\Listeners;

use Webkul\Product\Models\StockMovement;
use Webkul\Product\Services\StockService;

class Shipment
{
    /**
     * Create a listener instance.
     */
    public function __construct(protected StockService $stockService) {}

    /**
     * When a shipment is packed, draw the shipped quantity from batches using
     * FEFO. Bagisto lowers product_inventories at order placement, not here, so
     * FEFO at shipment time only decides which physical batches the quantity
     * left in and writes the movement ledger - it must not touch the source
     * inventory, which is already correct by this point.
     */
    public function afterCreate($shipment): void
    {
        foreach ($shipment->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            $qty = (int) $item->qty;

            if ($qty <= 0) {
                continue;
            }

            $this->stockService->consumeBatchesFefo(
                $item->product_id,
                $shipment->inventory_source_id,
                $qty,
                [
                    'type' => StockMovement::TYPE_ISSUE,
                    'reference_type' => 'shipment',
                    'reference_id' => $shipment->id,
                ]
            );
        }
    }
}
