<?php

namespace Webkul\Admin\DataGrids\Sales;

use Illuminate\Database\Query\Builder;

class CreditOrderDataGrid extends OrderDataGrid
{
    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = parent::prepareQueryBuilder();

        $queryBuilder->where('order_payment.method', 'credit');

        return $queryBuilder;
    }
}
