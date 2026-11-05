<?php

namespace Webkul\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webkul\User\Contracts\SalesTerritory as SalesTerritoryContract;

class SalesTerritory extends Model implements SalesTerritoryContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the admins assigned to this territory.
     *
     * A territory belongs to one salesperson in practice, but the pivot keeps
     * the relation symmetrical with Admin::salesTerritories().
     *
     * @return BelongsToMany
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminProxy::modelClass(),
            'admin_sales_territories',
            'sales_territory_id',
            'admin_id'
        );
    }
}
