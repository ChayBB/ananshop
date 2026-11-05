<?php

namespace Webkul\User\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Webkul\User\Models\Admin;
use Webkul\User\Models\Role;
use Webkul\User\Models\SalesCode;
use Webkul\User\Models\SalesTerritory;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        Admin::class,
        Role::class,
        SalesCode::class,
        SalesTerritory::class,
    ];
}
