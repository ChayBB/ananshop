<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Inventory\Models\InventorySourceProxy;
use Webkul\Product\Contracts\ProductBatch as ProductBatchContract;

class ProductBatch extends Model implements ProductBatchContract
{
    protected $fillable = [
        'batch_number',
        'product_id',
        'inventory_source_id',
        'qty',
        'initial_qty',
        'unit_cost',
        'received_at',
        'expired_at',
    ];

    protected $casts = [
        'received_at' => 'date',
        'expired_at'  => 'date',
    ];

    /**
     * Get the product that owns the batch.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * Get the inventory source holding the batch.
     */
    public function inventory_source(): BelongsTo
    {
        return $this->belongsTo(InventorySourceProxy::modelClass());
    }

    /**
     * Get the movements recorded against the batch.
     */
    public function stock_movements(): HasMany
    {
        return $this->hasMany(StockMovementProxy::modelClass());
    }

    /**
     * Batches still holding stock, ordered the way FEFO consumes them:
     * soonest expiry first, with undated batches last so dated stock is
     * always used up before stock of unknown age.
     */
    public function scopeFefo(Builder $query): Builder
    {
        return $query->where('qty', '>', 0)
            ->orderByRaw('expired_at is null, expired_at asc')
            ->orderBy('received_at')
            ->orderBy('id');
    }

    /**
     * Whether the batch is past its expiry date.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    /**
     * Days remaining before the batch expires, null when it has no date.
     */
    public function getDaysToExpiryAttribute(): ?int
    {
        if (! $this->expired_at) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->expired_at->startOfDay(), false);
    }
}
