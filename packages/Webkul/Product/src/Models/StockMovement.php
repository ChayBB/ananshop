<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Inventory\Models\InventorySourceProxy;
use Webkul\Product\Contracts\StockMovement as StockMovementContract;
use Webkul\User\Models\AdminProxy;

class StockMovement extends Model implements StockMovementContract
{
    public const TYPE_RECEIVE = 'receive';

    public const TYPE_ISSUE = 'issue';

    public const TYPE_ADJUST = 'adjust';

    public const TYPE_WASTE = 'waste';

    protected $fillable = [
        'type',
        'product_id',
        'product_batch_id',
        'inventory_source_id',
        'qty',
        'reason',
        'notes',
        'reference_type',
        'reference_id',
        'user_id',
    ];

    /**
     * Get the product the movement belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * Get the batch the movement was applied to.
     */
    public function product_batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatchProxy::modelClass());
    }

    /**
     * Get the inventory source the movement happened in.
     */
    public function inventory_source(): BelongsTo
    {
        return $this->belongsTo(InventorySourceProxy::modelClass());
    }

    /**
     * Get the admin who recorded the movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(AdminProxy::modelClass());
    }
}
