<?php

namespace Webkul\Admin\DataGrids\Sales;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\DataGrid\DataGrid;
use Webkul\Sales\Models\OrderAddress;
use Webkul\Sales\Repositories\OrderRepository;

class OrderDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('orders')
            ->leftJoin('addresses as order_address_shipping', function ($leftJoin) {
                $leftJoin->on('order_address_shipping.order_id', '=', 'orders.id')
                    ->where('order_address_shipping.address_type', OrderAddress::ADDRESS_TYPE_SHIPPING);
            })
            ->leftJoin('addresses as order_address_billing', function ($leftJoin) {
                $leftJoin->on('order_address_billing.order_id', '=', 'orders.id')
                    ->where('order_address_billing.address_type', OrderAddress::ADDRESS_TYPE_BILLING);
            })
            ->leftJoin('order_payment', 'orders.id', '=', 'order_payment.order_id')
            ->select(
                'orders.id',
                DB::raw('GROUP_CONCAT('.DB::getTablePrefix().'order_payment.method SEPARATOR "|") as method'),
                'orders.increment_id',
                'orders.base_grand_total',
                'orders.created_at',
                'channel_name',
                'channel_id',
                'status',
                'orders.custom_status',
                'customer_email',
                'orders.cart_id as items',
                'orders.slip_path',
                DB::raw('CONCAT('.DB::getTablePrefix().'orders.customer_first_name, " ", '.DB::getTablePrefix().'orders.customer_last_name) as full_name'),
                DB::raw('CONCAT('.DB::getTablePrefix().'order_address_billing.city, ", ", '.DB::getTablePrefix().'order_address_billing.state,", ", '.DB::getTablePrefix().'order_address_billing.country) as location')
            )
            ->groupBy('orders.id');

        $this->addFilter('full_name', DB::raw('CONCAT('.DB::getTablePrefix().'orders.customer_first_name, " ", '.DB::getTablePrefix().'orders.customer_last_name)'));
        $this->addFilter('created_at', 'orders.created_at');
        $this->addFilter('status', 'orders.custom_status');

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
            'index' => 'increment_id',
            'label' => trans('admin::app.sales.orders.index.datagrid.order-id'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('admin::app.sales.orders.index.datagrid.status'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => [
                [
                    'label' => 'รอดำเนินการ',
                    'value' => 'รอดำเนินการ',
                ],
                [
                    'label' => 'รอยืนยันการชำระเงิน',
                    'value' => 'รอยืนยันการชำระเงิน',
                ],
                [
                    'label' => 'เก็บเงินปลายทาง',
                    'value' => 'เก็บเงินปลายทาง',
                ],
                [
                    'label' => 'ยืนยันการชำระเงินแล้ว',
                    'value' => 'ยืนยันการชำระเงินแล้ว',
                ],
                [
                    'label' => 'จัดส่ง',
                    'value' => 'จัดส่ง',
                ],
                [
                    'label' => 'เรียกเก็บเงินจากพนักงานขนส่ง',
                    'value' => 'เรียกเก็บเงินจากพนักงานขนส่ง',
                ],
                [
                    'label' => 'ยกเลิกออร์เดอร์',
                    'value' => 'ยกเลิกออร์เดอร์',
                ],
                [
                    'label' => 'คืนเงิน',
                    'value' => 'คืนเงิน',
                ],
                [
                    'label' => 'เสร็จสมบูรณ์',
                    'value' => 'เสร็จสมบูรณ์',
                ],
            ],
            'sortable' => true,
            'closure' => function ($row) {
                $status = $row->custom_status ?? 'รอดำเนินการ';
                $isCOD = ! empty($row->method) && str_contains($row->method, 'cashondelivery');

                if (empty($row->slip_path) && ! $isCOD) {
                    if (in_array($status, ['รอดำเนินการ', 'เสร็จสมบูรณ์', 'ยืนยันการชำระเงินแล้ว'])) {
                        $status = 'รอยืนยันการชำระเงิน';
                    }
                }

                switch ($status) {
                    case 'ยืนยันการชำระเงินแล้ว':
                    case 'เสร็จสมบูรณ์':
                        return '<p class="label-completed">'.$status.'</p>';

                    case 'จัดส่ง':
                    case 'เรียกเก็บเงินจากพนักงานขนส่ง':
                        return '<p class="label-processing">'.$status.'</p>';

                    case 'ยกเลิกออร์เดอร์':
                    case 'คืนเงิน':
                        return '<p class="label-canceled">'.$status.'</p>';

                    default: // 'รอดำเนินการ', 'เก็บเงินปลายทาง', 'รอยืนยันการชำระเงิน'
                        return '<p class="label-pending">'.$status.'</p>';
                }
            },
        ]);

        $this->addColumn([
            'index' => 'base_grand_total',
            'label' => trans('admin::app.sales.orders.index.datagrid.grand-total'),
            'type' => 'string',
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'method',
            'label' => trans('admin::app.sales.orders.index.datagrid.pay-via'),
            'type' => 'string',
            'closure' => function ($row) {
                return collect(explode('|', $row->method))
                    ->map(fn ($method) => core()->getConfigData('sales.payment_methods.'.$method.'.title'))
                    ->filter()
                    ->unique()
                    ->join(', ');
            },
        ]);

        $this->addColumn([
            'index' => 'channel_id',
            'label' => trans('admin::app.sales.orders.index.datagrid.channel-name'),
            'type' => 'string',
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => core()->getAllChannels()
                ->map(fn ($channel) => ['label' => $channel->name, 'value' => $channel->id])
                ->values()
                ->toArray(),
        ]);

        $this->addColumn([
            'index' => 'full_name',
            'label' => trans('admin::app.sales.orders.index.datagrid.customer'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        /**
         * Searchable dropdown sample. In testing phase.
         */
        $this->addColumn([
            'index' => 'customer_email',
            'label' => trans('admin::app.sales.orders.index.datagrid.email'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'location',
            'label' => trans('admin::app.sales.orders.index.datagrid.location'),
            'type' => 'string',
        ]);

        $this->addColumn([
            'index' => 'items',
            'label' => trans('admin::app.sales.orders.index.datagrid.items'),
            'type' => 'string',
            'exportable' => false,
            'closure' => function ($value) {
                $order = app(OrderRepository::class)->with('items')->find($value->id);

                return view('admin::sales.orders.items', compact('order'))->render();
            },
        ]);

        $this->addColumn([
            'index' => 'slip_path',
            'label' => app()->getLocale() === 'th' ? 'สลิป' : 'Slip',
            'type' => 'string',
            'exportable' => false,
            'closure' => function ($value) {
                if (empty($value->slip_path)) {
                    return '<p class="label-pending">'.(app()->getLocale() === 'th' ? 'รอชำระ' : 'Pending Payment').'</p>';
                }

                $url = Storage::url($value->slip_path);

                return '<div class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded cursor-pointer border border-gray-200 dark:border-gray-800 flex items-center justify-center overflow-hidden" onclick="window.showSlipModal(\''.$url.'\')">
                    <img class="h-full w-full object-cover" src="'.$url.'" alt="Slip">
                </div>';
            },
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('admin::app.sales.orders.index.datagrid.date'),
            'type' => 'date',
            'filterable' => true,
            'filterable_type' => 'date_range',
            'sortable' => true,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (bouncer()->hasPermission('sales.orders.view')) {
            $this->addAction([
                'icon' => 'icon-view',
                'title' => trans('admin::app.sales.orders.index.datagrid.view'),
                'method' => 'GET',
                'url' => function ($row) {
                    return route('admin.sales.orders.view', $row->id);
                },
            ]);
        }
    }
}
