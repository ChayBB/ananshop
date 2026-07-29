<?php

namespace Webkul\Admin\DataGrids\Catalog;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class ProductBatchDataGrid extends DataGrid
{
    /**
     * Index.
     *
     * @var string
     */
    protected $primaryColumn = 'batch_id';

    /**
     * Default sort: whatever expires soonest needs attention first, which is
     * the same order FEFO consumes stock in.
     *
     * @var string
     */
    protected $sortColumn = 'expired_at';

    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('product_batches')
            ->select(
                'product_batches.id as batch_id',
                'product_batches.batch_number',
                'product_batches.qty',
                'product_batches.initial_qty',
                'product_batches.received_at',
                'product_batches.expired_at',
                'products.sku',
                'inventory_sources.name as source_name',
                DB::raw('DATEDIFF('.DB::getTablePrefix().'product_batches.expired_at, CURDATE()) as days_to_expiry')
            )
            ->leftJoin('products', 'product_batches.product_id', '=', 'products.id')
            ->leftJoin('inventory_sources', 'product_batches.inventory_source_id', '=', 'inventory_sources.id');

        $this->addFilter('batch_id', 'product_batches.id');
        $this->addFilter('batch_number', 'product_batches.batch_number');
        $this->addFilter('sku', 'products.sku');
        $this->addFilter('qty', 'product_batches.qty');
        $this->addFilter('expired_at', 'product_batches.expired_at');
        $this->addFilter('received_at', 'product_batches.received_at');

        return $queryBuilder;
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index' => 'batch_number',
            'label' => trans('admin::app.catalog.batches.index.datagrid.batch-number'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'sku',
            'label' => trans('admin::app.catalog.batches.index.datagrid.sku'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'source_name',
            'label' => trans('admin::app.catalog.batches.index.datagrid.source'),
            'type' => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'qty',
            'label' => trans('admin::app.catalog.batches.index.datagrid.qty-remaining'),
            'type' => 'integer',
            'filterable' => true,
            'sortable' => true,
            'closure' => function ($row) {
                return $row->qty.' / '.$row->initial_qty;
            },
        ]);

        $this->addColumn([
            'index' => 'received_at',
            'label' => trans('admin::app.catalog.batches.index.datagrid.received-at'),
            'type' => 'date',
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'expired_at',
            'label' => trans('admin::app.catalog.batches.index.datagrid.expired-at'),
            'type' => 'date',
            'filterable' => true,
            'sortable' => true,
            'closure' => function ($row) {
                if (! $row->expired_at) {
                    return '<span class="text-gray-400">-</span>';
                }

                $days = (int) $row->days_to_expiry;

                /**
                 * Depleted batches are history, so they are not flagged as
                 * expiring however old they are.
                 */
                if ($row->qty <= 0) {
                    return '<span class="text-gray-400">'.$row->expired_at.'</span>';
                }

                if ($days < 0) {
                    return '<span class="badge badge-md badge-danger">'.$row->expired_at.'</span>';
                }

                if ($days <= 3) {
                    return '<span class="badge badge-md badge-warning">'.$row->expired_at.'</span>';
                }

                return $row->expired_at;
            },
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (bouncer()->hasPermission('catalog.batches.delete')) {
            $this->addAction([
                'icon' => 'icon-delete',
                'title' => trans('admin::app.catalog.batches.index.datagrid.delete'),
                'method' => 'DELETE',
                'url' => function ($row) {
                    return route('admin.catalog.batches.delete', $row->batch_id);
                },
            ]);
        }
    }
}
