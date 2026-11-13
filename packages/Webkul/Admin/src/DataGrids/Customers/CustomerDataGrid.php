<?php

namespace Webkul\Admin\DataGrids\Customers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\DataGrid\DataGrid;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Repositories\OrderRepository;

class CustomerDataGrid extends DataGrid
{
    /**
     * Index.
     *
     * @var string
     */
    protected $primaryColumn = 'customer_id';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected CustomerGroupRepository $customerGroupRepository) {}

    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $tablePrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('customers')
            ->leftJoin('addresses', function ($join) {
                $join->on('customers.id', '=', 'addresses.customer_id')
                    ->where('addresses.address_type', '=', 'customer');
            })
            ->leftJoin('orders', 'customers.id', '=', 'orders.customer_id')
            ->leftJoin('customer_groups', 'customers.customer_group_id', '=', 'customer_groups.id')
            ->leftJoin('sales_codes', 'customers.sales_code_id', '=', 'sales_codes.id')
            ->addSelect(
                'customers.id as customer_id',
                'customers.customer_code',
                'customers.email',
                'customers.line_id',
                'customers.phone',
                'customers.gender',
                'customers.date_of_birth',
                'customers.status',
                'customers.is_suspended',
                'customer_groups.name as group',
                'sales_codes.code as sales_code',
                'customers.channel_id',
            )
            ->addSelect(DB::raw('COUNT(DISTINCT '.$tablePrefix.'addresses.id) as address_count'))
            ->addSelect(DB::raw('COUNT(DISTINCT '.$tablePrefix.'orders.id) as order_count'))
            ->addSelect(DB::raw('CONCAT('.$tablePrefix.'customers.first_name, " ", '.$tablePrefix.'customers.last_name) as full_name'));

        /**
         * The address is joined above only to be counted, so a customer with
         * several addresses would otherwise contribute an arbitrary one. Each
         * field is read from the default address instead, falling back to the
         * oldest one when no address has been marked as default.
         */
        $addressFields = [
            'company_name' => 'address_company_name',
            'vat_id' => 'address_vat_id',
            'address' => 'address_street',
            'city' => 'address_city',
            'state' => 'address_state',
            'postcode' => 'address_postcode',
        ];

        foreach ($addressFields as $field => $alias) {
            $queryBuilder->addSelect(DB::raw(
                '(SELECT '.($field === 'address' ? "REPLACE($field, '\\n', ', ')" : $field)
                .' FROM '.$tablePrefix.'addresses'
                .' WHERE customer_id = '.$tablePrefix.'customers.id'
                ." AND address_type = 'customer'"
                .' ORDER BY default_address DESC, id ASC LIMIT 1'
                .') as '.$alias
            ));
        }

        /**
         * The export writes the stored value of every column, so the channel
         * and the country are resolved here rather than in a column closure,
         * which only runs for the listing.
         */
        $locale = app()->getLocale();

        $queryBuilder->selectRaw(
            '(SELECT COALESCE(channel_translations.name, channels.code)'
            .' FROM '.$tablePrefix.'channels as channels'
            .' LEFT JOIN '.$tablePrefix.'channel_translations as channel_translations'
            .' ON channel_translations.channel_id = channels.id AND channel_translations.locale = ?'
            .' WHERE channels.id = '.$tablePrefix.'customers.channel_id'
            .' LIMIT 1) as channel_name',
            [$locale]
        );

        $queryBuilder->selectRaw(
            '(SELECT COALESCE(country_translations.name, countries.name)'
            .' FROM '.$tablePrefix.'countries as countries'
            .' LEFT JOIN '.$tablePrefix.'country_translations as country_translations'
            .' ON country_translations.country_id = countries.id AND country_translations.locale = ?'
            .' WHERE countries.code = (SELECT country FROM '.$tablePrefix.'addresses'
            .' WHERE customer_id = '.$tablePrefix.'customers.id'
            ." AND address_type = 'customer'"
            .' ORDER BY default_address DESC, id ASC LIMIT 1)'
            .' LIMIT 1) as address_country_name',
            [$locale]
        );

        $queryBuilder->groupBy('customers.id');

        $this->addFilter('channel_id', 'customers.channel_id');
        $this->addFilter('customer_id', 'customers.id');
        $this->addFilter('customer_code', 'customers.customer_code');
        $this->addFilter('email', 'customers.email');
        $this->addFilter('full_name', DB::raw('CONCAT('.$tablePrefix.'customers.first_name, " ", '.$tablePrefix.'customers.last_name)'));
        $this->addFilter('group', 'customer_groups.name');
        $this->addFilter('phone', 'customers.phone');
        $this->addFilter('status', 'customers.status');

        return $queryBuilder;
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $allChannels = core()->getAllChannels();

        $this->addColumn([
            'index' => 'channel_id',
            'label' => trans('admin::app.customers.customers.index.datagrid.channel'),
            'type' => 'string',
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => collect($allChannels)
                ->map(fn ($channel) => ['label' => $channel->name, 'value' => $channel->id])
                ->values()
                ->toArray(),
            'sortable' => true,
            'exportable' => false,
            'closure' => function ($row) use ($allChannels) {
                $channel = $allChannels->firstWhere('id', $row->channel_id);

                return $channel ? $channel->name : '-';
            },
        ]);

        $this->addColumn([
            'index' => 'channel_name',
            'label' => trans('admin::app.customers.customers.index.datagrid.channel'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'customer_id',
            'label' => trans('admin::app.customers.customers.index.datagrid.id'),
            'type' => 'integer',
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'customer_code',
            'label' => trans('admin::app.customers.customers.index.datagrid.customer-code'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'sales_code',
            'label' => trans('admin::app.settings.users.index.create.sales-code'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'full_name',
            'label' => trans('admin::app.customers.customers.index.datagrid.name'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'email',
            'label' => trans('admin::app.customers.customers.index.datagrid.email'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'line_id',
            'label' => trans('admin::app.customers.customers.index.create.line-id'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'phone',
            'label' => trans('admin::app.customers.customers.index.datagrid.phone'),
            'type' => 'integer',
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('admin::app.customers.customers.index.datagrid.status'),
            'type' => 'boolean',
            'filterable' => true,
            'filterable_options' => [
                [
                    'label' => trans('admin::app.customers.customers.index.datagrid.active'),
                    'value' => 1,
                ],
                [
                    'label' => trans('admin::app.customers.customers.index.datagrid.inactive'),
                    'value' => 0,
                ],
            ],
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'gender',
            'label' => trans('admin::app.customers.customers.index.datagrid.gender'),
            'type' => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'date_of_birth',
            'label' => trans('admin::app.customers.customers.index.create.date-of-birth'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'group',
            'label' => trans('admin::app.customers.customers.index.datagrid.group'),
            'type' => 'string',
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => $this->customerGroupRepository->all(['name as label', 'name as value'])->toArray(),
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'is_suspended',
            'label' => trans('admin::app.customers.customers.index.datagrid.suspended'),
            'type' => 'boolean',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'revenue',
            'label' => trans('admin::app.customers.customers.index.datagrid.revenue'),
            'type' => 'integer',
            'exportable' => false,
            'closure' => function ($row) {
                return app(OrderRepository::class)->scopeQuery(function ($q) use ($row) {
                    return $q->whereNotIn('status', [Order::STATUS_CANCELED, Order::STATUS_CLOSED])
                        ->where('customer_id', $row->customer_id);
                })->sum('base_grand_total_invoiced');
            },
        ]);

        $this->addColumn([
            'index' => 'order_count',
            'label' => trans('admin::app.customers.customers.index.datagrid.order-count'),
            'type' => 'integer',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'address_count',
            'label' => trans('admin::app.customers.customers.index.datagrid.address-count'),
            'type' => 'integer',
            'sortable' => true,
        ]);

        /**
         * Everything the create form asks for also belongs in the export. The
         * listing already has its own fixed set of columns, so these are
         * hidden in the grid and only written to the exported file.
         */
        $this->addColumn([
            'index' => 'address_company_name',
            'label' => trans('admin::app.customers.customers.view.address.create.company-name'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'address_vat_id',
            'label' => trans('admin::app.customers.customers.view.address.create.vat-id'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'address_street',
            'label' => trans('admin::app.customers.customers.view.address.create.street-address'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'address_city',
            'label' => trans('admin::app.customers.customers.view.address.create.city'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'address_state',
            'label' => trans('admin::app.customers.customers.view.address.create.state'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'address_country_name',
            'label' => trans('admin::app.customers.customers.view.address.create.country'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'address_postcode',
            'label' => trans('admin::app.customers.customers.view.address.create.post-code'),
            'type' => 'string',
            'visibility' => false,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'icon' => 'icon-view',
            'title' => trans('admin::app.customers.customers.index.datagrid.view'),
            'method' => 'GET',
            'url' => function ($row) {
                return route('admin.customers.customers.view', $row->customer_id);
            },
        ]);

        $this->addAction([
            'icon' => 'icon-exit',
            'title' => trans('admin::app.customers.customers.index.datagrid.login-as-customer'),
            'method' => 'GET',
            'target' => 'blank',
            'url' => function ($row) {
                return route('admin.customers.customers.login_as_customer', $row->customer_id);
            },
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        if (bouncer()->hasPermission('customers.customers.delete')) {
            $this->addMassAction([
                'title' => trans('admin::app.customers.customers.index.datagrid.delete'),
                'method' => 'POST',
                'url' => route('admin.customers.customers.mass_delete'),
            ]);
        }

        if (bouncer()->hasPermission('customers.customers.edit')) {
            $this->addMassAction([
                'title' => trans('admin::app.customers.customers.index.datagrid.update-status'),
                'method' => 'POST',
                'url' => route('admin.customers.customers.mass_update'),
                'options' => [
                    [
                        'label' => trans('admin::app.customers.customers.index.datagrid.active'),
                        'value' => 1,
                    ],
                    [
                        'label' => trans('admin::app.customers.customers.index.datagrid.inactive'),
                        'value' => 0,
                    ],
                ],
            ]);
        }
    }
}
