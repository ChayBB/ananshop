<?php

namespace Webkul\Admin\DataGrids\Sales;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Sales\Models\OrderProxy;

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

        $queryBuilder->addSelect('orders.customer_id');

        $queryBuilder->where('order_payment.method', 'credit');

        return $queryBuilder;
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        parent::prepareColumns();

        /**
         * Replace the shared slip column so credit orders without a slip yet
         * show an upload trigger instead of a plain pending label.
         */
        $this->setColumns(array_values(array_filter(
            $this->getColumns(),
            fn ($column) => $column->getIndex() !== 'slip_path'
        )));

        $this->addColumn([
            'index' => 'slip_path',
            'label' => app()->getLocale() === 'th' ? 'สลิป' : 'Slip',
            'type' => 'string',
            'exportable' => false,
            'closure' => function ($row) {
                if (! empty($row->slip_path)) {
                    $url = Storage::url($row->slip_path);

                    return '<div class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded cursor-pointer border border-gray-200 dark:border-gray-800 flex items-center justify-center overflow-hidden" onclick="window.showSlipModal(\''.$url.'\')">
                        <img class="h-full w-full object-cover" src="'.$url.'" alt="Slip">
                    </div>';
                }

                return '<button type="button" class="label-pending cursor-pointer" onclick="window.showUploadSlipModal('.$row->id.')">
                    อัปโหลดสลิป
                </button>';
            },
        ]);

        $this->addColumn([
            'index' => 'credit_status',
            'label' => 'เครดิตที่ใช้ไปแล้ว/เครดิตที่เหลือ',
            'type' => 'string',
            'exportable' => false,
            'closure' => function ($row) {
                if (empty($row->customer_id)) {
                    return '-';
                }

                $customer = app(CustomerRepository::class)->find($row->customer_id);

                if (! $customer || ! $customer->group) {
                    return '-';
                }

                $approvedCredit = $customer->group->credit_rounds ?? 0;

                $orderModel = OrderProxy::modelClass();

                // เครดิตที่ยืนยันการชำระเงินแล้ว: only orders whose payment has actually
                // been confirmed (or completed) consume the customer's approved credit.
                $confirmedCredit = $orderModel::where('customer_id', $customer->id)
                    ->whereIn('custom_status', ['ยืนยันการชำระเงินแล้ว', 'เสร็จสมบูรณ์'])
                    ->whereHas('payment', fn ($query) => $query->where('method', 'credit'))
                    ->count();

                // เครดิตที่ใช้ได้ = เครดิตที่ได้รับอนุมัติ - เครดิตที่ยืนยันการชำระเงินแล้ว
                $availableCredit = max($approvedCredit - $confirmedCredit, 0);

                return '<p class="text-gray-600 dark:text-gray-300">ยืนยันการชำระเงินแล้ว: '.$confirmedCredit.' รอบ</p>
                    <p class="text-gray-600 dark:text-gray-300">เหลือ: '.$availableCredit.' รอบ</p>';
            },
        ]);
    }
}
