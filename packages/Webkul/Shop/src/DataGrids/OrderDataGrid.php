<?php

namespace Webkul\Shop\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Sales\Models\Order;

class OrderDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('orders')
            ->leftJoin('order_payment', 'orders.id', '=', 'order_payment.order_id')
            ->addSelect(
                'orders.id',
                'orders.increment_id',
                'orders.custom_status',
                'orders.status',
                'orders.slip_path',
                'orders.created_at',
                'orders.grand_total',
                'orders.order_currency_code',
                'order_payment.method'
            )
            ->where('customer_id', auth()->guard('customer')->user()->id);

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
            'label' => trans('shop::app.customers.account.orders.order-id'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('shop::app.customers.account.orders.order-date'),
            'type' => 'date',
            'searchable' => true,
            'filterable' => true,
            'filterable_type' => 'date_range',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'grand_total',
            'label' => trans('shop::app.customers.account.orders.total'),
            'type' => 'integer',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
            'closure' => function ($row) {
                return core()->formatPrice($row->grand_total, $row->order_currency_code);
            },
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('shop::app.customers.account.orders.status.title'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => [
                [
                    'label' => trans('shop::app.customers.account.orders.status.options.processing'),
                    'value' => Order::STATUS_PROCESSING,
                ],
                [
                    'label' => trans('shop::app.customers.account.orders.status.options.completed'),
                    'value' => Order::STATUS_COMPLETED,
                ],
                [
                    'label' => trans('shop::app.customers.account.orders.status.options.canceled'),
                    'value' => Order::STATUS_CANCELED,
                ],
                [
                    'label' => trans('shop::app.customers.account.orders.status.options.closed'),
                    'value' => Order::STATUS_CLOSED,
                ],
                [
                    'label' => trans('shop::app.customers.account.orders.status.options.pending'),
                    'value' => Order::STATUS_PENDING,
                ],
                [
                    'label' => trans('shop::app.customers.account.orders.status.options.pending-payment'),
                    'value' => Order::STATUS_PENDING_PAYMENT,
                ],
                [
                    'label' => trans('shop::app.customers.account.orders.status.options.fraud'),
                    'value' => Order::STATUS_FRAUD,
                ],
            ],
            'sortable' => true,
            'closure' => function ($row) {
                $status = $row->custom_status ?? 'รอดำเนินการ';
                $badgeClass = 'label-pending';
                if (in_array($status, ['เสร็จสมบูรณ์', 'ยืนยันการชำระเงินแล้ว'])) {
                    $badgeClass = 'label-active';
                } elseif (in_array($status, ['จัดส่ง', 'เรียกเก็บเงินจากพนักงานขนส่ง'])) {
                    $badgeClass = 'label-processing';
                } elseif (in_array($status, ['ยกเลิกออร์เดอร์', 'คืนเงิน'])) {
                    $badgeClass = 'label-canceled';
                }

                return '<p class="'.$badgeClass.'">'.e($status).'</p>';
            },
        ]);

        $this->addColumn([
            'index' => 'slip_actions',
            'label' => app()->getLocale() === 'th' ? 'สลิป' : 'Slip',
            'type' => 'string',
            'searchable' => false,
            'filterable' => false,
            'sortable' => false,
            'exportable' => false,
            'closure' => function ($row) {
                if (! empty($row->slip_path)) {
                    return '<button
                        type="button"
                        class="flex items-center gap-1 rounded-md border border-zinc-200 bg-zinc-50 px-2 py-1 text-xs font-medium text-zinc-600 transition-all hover:bg-zinc-100"
                        onclick="window.showViewSlipModal(\'/storage/'.e($row->slip_path).'\', \''.e($row->increment_id).'\')"
                    >
                        <span class="icon-file text-sm"></span> ดูสลิป
                    </button>';
                }

                $status = $row->custom_status ?? 'รอดำเนินการ';

                $isPendingStatus = ! in_array($status, ['เสร็จสมบูรณ์', 'ยืนยันการชำระเงินแล้ว', 'จัดส่ง', 'เรียกเก็บเงินจากพนักงานขนส่ง', 'ยกเลิกออร์เดอร์', 'คืนเงิน']);

                if ($isPendingStatus && $row->method !== 'cashondelivery') {
                    return '<button
                        type="button"
                        class="flex items-center gap-1 rounded-md border border-orange-400 bg-orange-50 px-2 py-1 text-xs font-medium text-orange-600 transition-all hover:bg-orange-100"
                        onclick="window.showUploadSlipModal('.(int) $row->id.', \''.e($row->increment_id).'\')"
                    >
                        <span class="icon-upload text-sm"></span> อัปโหลดสลิป
                    </button>';
                }

                return '';
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
        $this->addAction([
            'icon' => 'icon-eye',
            'title' => trans('shop::app.customers.account.orders.action-view'),
            'method' => 'GET',
            'url' => function ($row) {
                return route('shop.customers.account.orders.view', $row->id);
            },
        ]);
    }
}
