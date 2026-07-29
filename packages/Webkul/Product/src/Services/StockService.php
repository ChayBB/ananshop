<?php

namespace Webkul\Product\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Product\Models\ProductBatchProxy;
use Webkul\Product\Models\ProductInventoryProxy;
use Webkul\Product\Models\StockMovement;
use Webkul\Product\Models\StockMovementProxy;
use Webkul\Product\Repositories\ProductRepository;

class StockService
{
    /**
     * Create a service instance.
     */
    public function __construct(protected ProductRepository $productRepository) {}

    /**
     * Receive a batch into a source.
     *
     * Expiry is derived from the product's shelf life unless one is passed
     * explicitly, and is then frozen on the batch - editing the shelf life
     * later must not move the expiry of stock already received.
     */
    public function receive(array $data)
    {
        return DB::transaction(function () use ($data) {
            $product = $this->productRepository->findOrFail($data['product_id']);

            $qty = (int) $data['qty'];

            $receivedAt = Carbon::parse($data['received_at'] ?? now());

            $expiredAt = $data['expired_at'] ?? null;

            if (! $expiredAt) {
                $shelfLifeDays = (int) ($product->shelf_life_days ?? 0);

                $expiredAt = $shelfLifeDays > 0
                    ? $receivedAt->copy()->addDays($shelfLifeDays)
                    : null;
            }

            $batch = ProductBatchProxy::modelClass()::create([
                'batch_number'        => $data['batch_number'] ?: $this->generateBatchNumber($receivedAt),
                'product_id'          => $product->id,
                'inventory_source_id' => $data['inventory_source_id'],
                'qty'                 => $qty,
                'initial_qty'         => $qty,
                'unit_cost'           => $data['unit_cost'] ?? null,
                'received_at'         => $receivedAt,
                'expired_at'          => $expiredAt,
            ]);

            $this->adjustSourceInventory($product->id, $data['inventory_source_id'], $qty);

            $this->recordMovement([
                'type'                => StockMovement::TYPE_RECEIVE,
                'product_id'          => $product->id,
                'product_batch_id'    => $batch->id,
                'inventory_source_id' => $data['inventory_source_id'],
                'qty'                 => $qty,
                'notes'               => $data['notes'] ?? null,
            ]);

            return $batch;
        });
    }

    /**
     * Issue stock using FEFO: consume the batch that expires soonest first.
     *
     * Returns the batches drawn from, so the caller can show what was picked.
     */
    public function issueFefo(int $productId, int $inventorySourceId, int $qty, array $reference = []): array
    {
        return DB::transaction(function () use ($productId, $inventorySourceId, $qty, $reference) {
            $remaining = $qty;

            $allocations = [];

            $batches = ProductBatchProxy::modelClass()::query()
                ->where('product_id', $productId)
                ->where('inventory_source_id', $inventorySourceId)
                ->fefo()
                ->lockForUpdate()
                ->get();

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $take = min($batch->qty, $remaining);

                $batch->decrement('qty', $take);

                $this->recordMovement([
                    'type'                => StockMovement::TYPE_ISSUE,
                    'product_id'          => $productId,
                    'product_batch_id'    => $batch->id,
                    'inventory_source_id' => $inventorySourceId,
                    'qty'                 => -$take,
                    'reference_type'      => $reference['type'] ?? null,
                    'reference_id'        => $reference['id'] ?? null,
                ]);

                $allocations[] = [
                    'batch_id'     => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'expired_at'   => $batch->expired_at?->toDateString(),
                    'qty'          => $take,
                ];

                $remaining -= $take;
            }

            /**
             * Stock that predates batch tracking has no batch to draw from,
             * so the shortfall is still recorded against the product. Without
             * this the ledger would drift away from the source inventory.
             */
            if ($remaining > 0) {
                $this->recordMovement([
                    'type'                => StockMovement::TYPE_ISSUE,
                    'product_id'          => $productId,
                    'product_batch_id'    => null,
                    'inventory_source_id' => $inventorySourceId,
                    'qty'                 => -$remaining,
                    'reason'              => 'unbatched',
                    'reference_type'      => $reference['type'] ?? null,
                    'reference_id'        => $reference['id'] ?? null,
                ]);

                $allocations[] = [
                    'batch_id'     => null,
                    'batch_number' => null,
                    'expired_at'   => null,
                    'qty'          => $remaining,
                ];
            }

            $this->adjustSourceInventory($productId, $inventorySourceId, -$qty);

            return $allocations;
        });
    }

    /**
     * Keep the source inventory that the storefront reads in step with the
     * batch ledger.
     */
    protected function adjustSourceInventory(int $productId, int $inventorySourceId, int $qty): void
    {
        $inventory = ProductInventoryProxy::modelClass()::firstOrCreate(
            [
                'product_id'          => $productId,
                'inventory_source_id' => $inventorySourceId,
                'vendor_id'           => 0,
            ],
            ['qty' => 0]
        );

        $inventory->update(['qty' => max($inventory->qty + $qty, 0)]);
    }

    /**
     * Write a row to the movement ledger.
     */
    protected function recordMovement(array $data)
    {
        return StockMovementProxy::modelClass()::create(array_merge([
            'user_id' => auth()->guard('admin')->id(),
        ], $data));
    }

    /**
     * Fallback batch number when none is supplied.
     */
    protected function generateBatchNumber(Carbon $receivedAt): string
    {
        return $receivedAt->format('Ymd').'-'.strtoupper(substr(uniqid(), -5));
    }
}
