<?php

namespace Webkul\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * @return HasMany
     */
    public function admins(): HasMany
    {
        return $this->hasMany(AdminProxy::modelClass());
    }
}
