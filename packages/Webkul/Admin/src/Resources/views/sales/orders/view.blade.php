<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.sales.orders.view.title', ['order_id' => $order->increment_id])
    </x-slot>

    <!-- Header -->
    <div class="grid">
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            {!! view_render_event('bagisto.admin.sales.order.title.before', ['order' => $order]) !!}

            <div class="flex items-center gap-2.5">
                <p class="text-xl font-bold leading-6 text-gray-800 dark:text-white">
                    @lang('admin::app.sales.orders.view.title', ['order_id' => $order->increment_id])
                </p>

                <!-- Custom Status Badge (Red bar / Green completed / Blue credit-pending) -->
                @php
                    $customStatus = $order->custom_status ?? 'รอดำเนินการ';
                    $isCreditPending = false;

                    if (empty($order->slip_path) && $order->payment->method === 'credit') {
                        if (in_array($customStatus, ['รอดำเนินการ', 'เสร็จสมบูรณ์', 'ยืนยันการชำระเงินแล้ว'])) {
                            $customStatus = 'ลูกค้าเครดิต รอยืนยันการชำระเงิน';
                            $isCreditPending = true;
                        }
                    }

                    $badgeStyle = match (true) {
                        $isCreditPending => 'background-color: #1e40af;', // Dark blue for credit customers awaiting confirmation
                        $customStatus === 'เสร็จสมบูรณ์' => 'background-color: #16a34a;', // Green for 'เสร็จสมบูรณ์'
                        default => 'background-color: #dc2626;', // Red for everything else
                    };
                @endphp

                <span 
                    style="{{ $badgeStyle }}" 
                    class="text-white text-sm font-semibold py-0.5 px-3 rounded-[35px] mx-1.5 shadow-sm"
                >
                    {{ $customStatus }}
                </span>
            </div>

            {!! view_render_event('bagisto.admin.sales.order.title.after', ['order' => $order]) !!}

            <!-- Back Button -->
            <a
                href="{{ route('admin.sales.orders.index') }}"
                class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
            >
                @lang('admin::app.account.edit.back-btn')
            </a>
        </div>
    </div>

    <div class="mt-5 flex-wrap items-center justify-between gap-x-1 gap-y-2">
        <div class="flex gap-1.5">
            {!! view_render_event('bagisto.admin.sales.order.page_action.before', ['order' => $order]) !!}

            @if (
                $order->canReorder()
                && bouncer()->hasPermission('sales.orders.create')
                && core()->getConfigData('sales.order_settings.reorder.admin')
            )
                <a
                    href="{{ route('admin.sales.orders.reorder', $order->id) }}"
                    class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    <span class="icon-cart text-2xl"></span>

                    @lang('admin::app.sales.orders.view.reorder')
                </a>
            @endif

            @if (
                $order->canInvoice()
                && bouncer()->hasPermission('sales.invoices.create')
                && $order->payment->method !== 'paypal_standard'
            )
                @include('admin::sales.invoices.create')
            @endif


            {!! view_render_event('bagisto.admin.sales.order.page_action.after', ['order' => $order]) !!}
        </div>

        <!-- Green Mark Actions Bar -->
        <div class="mt-4 flex flex-wrap gap-2 items-center border-t pt-4 dark:border-gray-800">
            <span class="text-gray-600 dark:text-gray-400 font-semibold mr-2 text-sm">จัดการสถานะ (เพิ่มเติม):</span>
            
            <form action="{{ route('admin.sales.orders.update_custom_status', $order->id) }}" method="POST" class="flex gap-2 flex-wrap">
                @csrf
                <button 
                    type="submit" 
                    name="action" 
                    value="cod" 
                    class="transparent-button px-3 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled($order->payment->method === 'cashondelivery' || $order->custom_status === 'เก็บเงินปลายทาง' || !empty($order->slip_path) || in_array($order->custom_status, ['ยืนยันการชำระเงินแล้ว', 'เสร็จสมบูรณ์', 'ยกเลิกออร์เดอร์', 'คืนเงิน']))
                    @if ($order->payment->method === 'cashondelivery' || $order->custom_status === 'เก็บเงินปลายทาง') 
                        title="เป็นออร์เดอร์เก็บเงินปลายทางอยู่แล้ว" 
                    @elseif (!empty($order->slip_path)) 
                        title="ไม่สามารถเปลี่ยนเป็นเก็บเงินปลายทางได้เนื่องจากลูกค้าอัปโหลดสลิปแล้ว" 
                    @elseif (in_array($order->custom_status, ['ยืนยันการชำระเงินแล้ว', 'เสร็จสมบูรณ์', 'ยกเลิกออร์เดอร์', 'คืนเงิน']))
                        title="ไม่สามารถเปลี่ยนสถานะเป็นเก็บเงินปลายทางได้สำหรับสถานะปัจจุบัน"
                    @endif
                >
                    <span class="icon-sales text-xl"></span>
                    เก็บเงินปลายทาง
                </button>

                <button 
                    type="submit" 
                    name="action" 
                    value="confirm_payment" 
                    class="transparent-button px-3 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled(empty($order->slip_path) || in_array($order->custom_status, ['ยืนยันการชำระเงินแล้ว', 'เสร็จสมบูรณ์', 'ยกเลิกออร์เดอร์', 'คืนเงิน']))
                    @if (empty($order->slip_path))
                        title="ไม่สามารถยืนยันการชำระเงินได้เนื่องจากยังไม่มีการอัปโหลดสลิป"
                    @elseif (in_array($order->custom_status, ['ยืนยันการชำระเงินแล้ว', 'เสร็จสมบูรณ์']))
                        title="ยืนยันการชำระเงินเรียบร้อยแล้ว" 
                    @elseif (in_array($order->custom_status, ['ยกเลิกออร์เดอร์', 'คืนเงิน']))
                        title="ไม่สามารถยืนยันการชำระเงินได้เนื่องจากออร์เดอร์ถูกยกเลิกหรือคืนเงินแล้ว"
                    @endif
                >
                    <span class="icon-checked text-xl"></span>
                    ยืนยันการชำระเงิน
                </button>

                @if (bouncer()->hasPermission('sales.shipments.create'))
                    @php
                        $isShipDisabled = in_array($order->custom_status, ['จัดส่ง', 'จัดส่งโดยเครดิต', 'เรียกเก็บเงินจากพนักงานขนส่ง', 'เสร็จสมบูรณ์', 'ยกเลิกออร์เดอร์', 'คืนเงิน']);
                        $shipTitle = '';
                        if (in_array($order->custom_status, ['จัดส่ง', 'จัดส่งโดยเครดิต', 'เรียกเก็บเงินจากพนักงานขนส่ง', 'เสร็จสมบูรณ์'])) {
                            $shipTitle = 'จัดส่งสินค้าเรียบร้อยแล้ว';
                        } elseif (in_array($order->custom_status, ['ยกเลิกออร์เดอร์', 'คืนเงิน'])) {
                            $shipTitle = 'ไม่สามารถจัดส่งได้เนื่องจากออร์เดอร์ถูกยกเลิกหรือคืนเงินแล้ว';
                        }
                    @endphp
                    <div @if ($shipTitle) title="{{ $shipTitle }}" @endif>
                        @include('admin::sales.shipments.create', ['disabled' => $isShipDisabled])
                    </div>
                @endif

                <button 
                    type="submit" 
                    name="action" 
                    value="cancel" 
                    class="transparent-button px-3 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled(in_array($order->custom_status, ['ยกเลิกออร์เดอร์', 'คืนเงิน', 'เสร็จสมบูรณ์']))
                    @if ($order->custom_status === 'ยกเลิกออร์เดอร์') 
                        title="ยกเลิกออร์เดอร์เรียบร้อยแล้ว" 
                    @elseif (in_array($order->custom_status, ['คืนเงิน', 'เสร็จสมบูรณ์']))
                        title="ไม่สามารถยกเลิกออร์เดอร์ที่เสร็จสมบูรณ์หรือคืนเงินแล้วได้"
                    @endif
                >
                    <span class="icon-cancel text-xl"></span>
                    ยกเลิกออร์เดอร์
                </button>

                <button 
                    type="submit" 
                    name="action" 
                    value="refund" 
                    class="transparent-button px-3 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled(in_array($order->custom_status, ['คืนเงิน', 'ยกเลิกออร์เดอร์']))
                    @if ($order->custom_status === 'คืนเงิน') 
                        title="คืนเงินเรียบร้อยแล้ว" 
                    @elseif ($order->custom_status === 'ยกเลิกออร์เดอร์')
                        title="ไม่สามารถคืนเงินออร์เดอร์ที่ถูกยกเลิกแล้วได้"
                    @endif
                >
                    <span class="icon-refund text-xl"></span>
                    คืนเงิน
                </button>
            </form>
        </div>

        @php
            $hasCustomerRestrictedItem = $order->items->contains(
                fn ($item) => ! $item->isCancelableByCustomer()
            );
        @endphp

        @if ($hasCustomerRestrictedItem)
            <div class="mt-4 flex items-start gap-3 rounded-md border border-amber-300 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-900/20 dark:text-amber-200">
                <span class="icon-warning mt-0.5 text-lg"></span>

                <div>
                    <p class="font-semibold">
                        @lang('admin::app.sales.orders.view.booking-cancellation-not-allowed.title')
                    </p>

                    <p class="text-xs">
                        @lang('admin::app.sales.orders.view.booking-cancellation-not-allowed.description')
                    </p>
                </div>
            </div>
        @endif

        <!-- Order details -->
        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Left Component -->
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                {!! view_render_event('bagisto.admin.sales.order.left_component.before', ['order' => $order]) !!}

                <div class="box-shadow rounded bg-white dark:bg-gray-900">
                    <div class="flex justify-between p-4">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('Order Items') ({{ count($order->items) }})
                        </p>

                        <p class="text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.sales.orders.view.grand-total', ['grand_total' => core()->formatBasePrice($order->base_grand_total)])
                        </p>
                    </div>

                    <!-- Order items -->
                    <div class="grid">
                        {!! view_render_event('bagisto.admin.sales.order.list.before', ['order' => $order]) !!}

                        @foreach ($order->items as $item)
                            {!! view_render_event('bagisto.admin.sales.order.list.item.before', ['order' => $order, 'item' => $item]) !!}

                            <div class="flex justify-between gap-2.5 border-b border-slate-300 px-4 py-6 dark:border-gray-800">
                                <div class="flex gap-2.5">
                                    @if($item?->product?->base_image_url)
                                        <img
                                            class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded"
                                            src="{{ $item?->product->base_image_url }}"
                                        >
                                    @else
                                        <div class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert">
                                            <img src="{{ bagisto_asset('images/product-placeholders/front.svg') }}">

                                            <p class="absolute bottom-1.5 w-full text-center text-[6px] font-semibold text-gray-400">
                                                @lang('admin::app.sales.invoices.view.product-image')
                                            </p>
                                        </div>
                                    @endif

                                    <div class="grid place-content-start gap-1.5">
                                        <p
                                            class="break-all text-base font-semibold text-gray-800 dark:text-white"
                                            v-pre
                                        >
                                            {{ $item->name }}
                                        </p>

                                        <div class="flex flex-col place-items-start gap-1.5">
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.amount-per-unit', [
                                                    'amount' => core()->formatBasePrice($item->base_price),
                                                    'qty'    => $item->qty_ordered,
                                                ])
                                            </p>

                                            @if (isset($item->additional['attributes']))
                                                @foreach ($item->additional['attributes'] as $attribute)
                                                    <p
                                                        class="text-gray-600 dark:text-gray-300"
                                                        v-pre
                                                    >
                                                        @if (
                                                            ! isset($attribute['attribute_type'])
                                                            || $attribute['attribute_type'] !== 'file'
                                                        )
                                                            {{ $attribute['attribute_name'] }} : {{ $attribute['option_label'] }}
                                                        @else
                                                            {{ $attribute['attribute_name'] }} :

                                                            <a
                                                                href="{{ Storage::url($attribute['option_label']) }}"
                                                                class="text-blue-600 hover:underline"
                                                                download="{{ File::basename($attribute['option_label']) }}"
                                                            >
                                                                {{ File::basename($attribute['option_label']) }}
                                                            </a>
                                                        @endif
                                                    </p>
                                                @endforeach
                                            @endif

                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.sku', ['sku' => $item->getTypeInstance()->getOrderedItem($item)->sku ])
                                            </p>

                                            <p class="text-gray-600 dark:text-gray-300">
                                                {{ $item->qty_ordered ? trans('admin::app.sales.orders.view.item-ordered', ['qty_ordered' => $item->qty_ordered]) : '' }}

                                                {{ $item->qty_invoiced ? trans('admin::app.sales.orders.view.item-invoice', ['qty_invoiced' => $item->qty_invoiced]) : '' }}

                                                {{ $item->qty_shipped ? trans('admin::app.sales.orders.view.item-shipped', ['qty_shipped' => $item->qty_shipped]) : '' }}

                                                {{ $item->qty_refunded ? trans('admin::app.sales.orders.view.item-refunded', ['qty_refunded' => $item->qty_refunded]) : '' }}

                                                {{ $item->qty_canceled ? trans('admin::app.sales.orders.view.item-canceled', ['qty_canceled' => $item->qty_canceled]) : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid place-content-start gap-1">
                                    <div class="">
                                        <p class="flex items-center justify-end gap-x-1 text-base font-semibold text-gray-800 dark:text-white">
                                            {{ core()->formatBasePrice($item->base_total + $item->base_tax_amount - $item->base_discount_amount) }}
                                        </p>
                                    </div>

                                    <div class="flex flex-col place-items-start items-end gap-1.5">
                                        @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.price', ['price' => core()->formatBasePrice($item->base_price_incl_tax)])
                                            </p>
                                        @elseif (core()->getConfigData('sales.taxes.sales.display_prices') == 'both')
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.price-excl-tax', ['price' => core()->formatBasePrice($item->base_price)])
                                            </p>

                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.price-incl-tax', ['price' => core()->formatBasePrice($item->base_price_incl_tax)])
                                            </p>
                                        @else
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.price', ['price' => core()->formatBasePrice($item->base_price)])
                                            </p>
                                        @endif

                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.tax', [
                                                'percent' => number_format($item->tax_percent, 2) . '%',
                                                'tax'     => core()->formatBasePrice($item->base_tax_amount)
                                            ])
                                        </p>

                                        @if ($order->base_discount_amount > 0)
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.discount', ['discount' => core()->formatBasePrice($item->base_discount_amount)])
                                            </p>
                                        @endif

                                        @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.sub-total', ['sub_total' => core()->formatBasePrice($item->base_total_incl_tax)])
                                            </p>
                                        @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.sub-total-excl-tax', ['sub_total' => core()->formatBasePrice($item->base_total)])
                                            </p>

                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.sub-total-incl-tax', ['sub_total' => core()->formatBasePrice($item->base_total_incl_tax)])
                                            </p>
                                        @else
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.sub-total', ['sub_total' => core()->formatBasePrice($item->base_total)])
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {!! view_render_event('bagisto.admin.sales.order.list.item.after', ['order' => $order, 'item' => $item]) !!}
                        @endforeach

                        {!! view_render_event('bagisto.admin.sales.order.list.after', ['order' => $order]) !!}
                    </div>

                    <div class="mt-4 flex flex-auto justify-between p-4 items-start gap-4">
                        <!-- Left Side: Payment Slip Details -->
                        @if ($order->slip_path)
                            <div class="flex flex-col gap-2">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    หลักฐานการชำระเงิน
                                </p>
                                <div class="relative h-[150px] max-h-[150px] w-[150px] rounded cursor-pointer border border-gray-200 dark:border-gray-800 flex items-center justify-center overflow-hidden hover:opacity-90 transition-all shadow-md" onclick="window.showSlipModal('{{ \Illuminate\Support\Facades\Storage::url($order->slip_path) }}')">
                                    <img class="h-full w-full object-cover" src="{{ \Illuminate\Support\Facades\Storage::url($order->slip_path) }}" alt="Slip">
                                </div>
                            </div>
                        @elseif ($order->payment->method === 'cashondelivery')
                            <div class="flex flex-col gap-2">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    หลักฐานการชำระเงิน
                                </p>
                                @if (in_array($order->custom_status, ['ยืนยันการชำระเงินแล้ว', 'เสร็จสมบูรณ์']))
                                    <span class="label-completed text-sm px-2.5 py-1 w-max rounded">
                                        เก็บเงินปลายทางเรียบร้อยแล้ว
                                    </span>
                                @else
                                    <span class="label-pending text-sm px-2.5 py-1 w-max rounded">
                                        รอชำระเงินปลายทาง
                                    </span>

                                    <button
                                        type="button"
                                        class="w-max cursor-pointer rounded-md border border-gray-300 px-2.5 py-1.5 text-sm font-medium text-gray-600 transition-all hover:bg-gray-100 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800"
                                        onclick="window.showUploadSlipModal()"
                                    >
                                        อัปโหลดสลิป
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="flex flex-col gap-2">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    หลักฐานการชำระเงิน
                                </p>
                                @if (in_array($order->custom_status, ['ยืนยันการชำระเงินแล้ว', 'เสร็จสมบูรณ์']))
                                    <span class="label-completed text-sm px-2.5 py-1 w-max rounded">
                                        ยืนยันการชำระเงินแล้ว
                                    </span>
                                @else
                                    <span class="label-pending text-sm px-2.5 py-1 w-max rounded">
                                        รอชำระ (ยังไม่ได้อัปโหลดสลิป)
                                    </span>

                                    <button
                                        type="button"
                                        class="w-max cursor-pointer rounded-md border border-gray-300 px-2.5 py-1.5 text-sm font-medium text-gray-600 transition-all hover:bg-gray-100 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800"
                                        onclick="window.showUploadSlipModal()"
                                    >
                                        อัปโหลดสลิป
                                    </button>
                                @endif
                            </div>
                        @endif

                        <div class="grid max-w-max gap-2 text-sm">

                            {!! view_render_event('bagisto.admin.sales.order.view.subtotal.before') !!}

                            <!-- Sub Total -->
                            @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                <div class="flex w-full justify-between gap-x-5">
                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.summary-sub-total-incl-tax')
                                    </p>

                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice($order->base_sub_total_incl_tax) }}
                                    </p>
                                </div>
                            @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                <div class="flex w-full justify-between gap-x-5">
                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.summary-sub-total-excl-tax')
                                    </p>

                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice($order->base_sub_total) }}
                                    </p>
                                </div>

                                <div class="flex w-full justify-between gap-x-5">
                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.summary-sub-total-incl-tax')
                                    </p>

                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice($order->base_sub_total_incl_tax) }}
                                    </p>
                                </div>
                            @else
                                <div class="flex w-full justify-between gap-x-5">
                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.summary-sub-total')
                                    </p>

                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice($order->base_sub_total) }}
                                    </p>
                                </div>
                            @endif

                            {!! view_render_event('bagisto.admin.sales.order.view.subtotal.after') !!}

                            {!! view_render_event('bagisto.admin.sales.order.view.shipping.before') !!}

                            <!-- Shipping And Handling -->
                            @if ($haveStockableItems = $order->haveStockableItems())
                                @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                    <div class="flex w-full justify-between gap-x-5">
                                        <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.shipping-and-handling-incl-tax')
                                        </p>

                                        <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                            {{ core()->formatBasePrice($order->base_shipping_amount_incl_tax) }}
                                        </p>
                                    </div>
                                @elseif (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                                    <div class="flex w-full justify-between gap-x-5">
                                        <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.shipping-and-handling-excl-tax')
                                        </p>

                                        <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                            {{ core()->formatBasePrice($order->base_shipping_amount) }}
                                        </p>
                                    </div>

                                    <div class="flex w-full justify-between gap-x-5">
                                        <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.shipping-and-handling-incl-tax')
                                        </p>

                                        <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                            {{ core()->formatBasePrice($order->base_shipping_amount_incl_tax) }}
                                        </p>
                                    </div>
                                @else
                                    <div class="flex w-full justify-between gap-x-5">
                                        <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.shipping-and-handling')
                                        </p>

                                        <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                            {{ core()->formatBasePrice($order->base_shipping_amount) }}
                                        </p>
                                    </div>
                                @endif
                            @endif

                            {!! view_render_event('bagisto.admin.sales.order.view.shipping.after') !!}

                            {!! view_render_event('bagisto.admin.sales.order.view.tax-amount.before') !!}

                            <!-- Tax Amount -->
                            <div class="flex w-full justify-between gap-x-5">
                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.summary-tax')
                                </p>

                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    {{ core()->formatBasePrice($order->base_tax_amount) }}
                                </p>
                            </div>

                            {!! view_render_event('bagisto.admin.sales.order.view.tax-amount.after') !!}

                            {!! view_render_event('bagisto.admin.sales.order.view.discount.before') !!}

                            <!-- Discount -->
                            <div class="flex w-full justify-between gap-x-5">
                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.summary-discount')
                                </p>

                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    {{ core()->formatBasePrice($order->base_discount_amount) }}
                                </p>
                            </div>

                            {!! view_render_event('bagisto.admin.sales.order.view.discount.after') !!}

                            {!! view_render_event('bagisto.admin.sales.order.view.grand-total.before') !!}

                            <!-- Grand Total -->
                            <div class="flex w-full justify-between gap-x-5">
                                <p class="text-base font-semibold !leading-5 text-gray-800 dark:text-white">
                                    @lang('admin::app.sales.orders.view.summary-grand-total')
                                </p>

                                <p class="text-base font-semibold !leading-5 text-gray-800 dark:text-white">
                                    {{ core()->formatBasePrice($order->base_grand_total) }}
                                </p>
                            </div>

                            {!! view_render_event('bagisto.admin.sales.order.view.grand-total.after') !!}

                            {!! view_render_event('bagisto.admin.sales.order.view.total-paid.before') !!}

                            <!-- Total Paid -->
                            <div class="flex w-full justify-between gap-x-5">
                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.total-paid')
                                </p>

                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    {{ core()->formatBasePrice($order->base_grand_total_invoiced) }}
                                </p>
                            </div>

                            {!! view_render_event('bagisto.admin.sales.order.view.total-paid.after') !!}

                            {!! view_render_event('bagisto.admin.sales.order.view.total-refunded.before') !!}

                            <!-- Total Refund -->
                            <div class="flex w-full justify-between gap-x-5">
                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.total-refund')
                                </p>

                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    {{ core()->formatBasePrice($order->base_grand_total_refunded) }}
                                </p>
                            </div>

                            {!! view_render_event('bagisto.admin.sales.order.view.total-refunded.after') !!}

                            {!! view_render_event('bagisto.admin.sales.order.view.total-due.before') !!}

                            <!-- Total Due -->
                            <div class="flex w-full justify-between gap-x-5 font-semibold">
                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.total-due')
                                </p>

                                @if($order->status !== 'canceled')
                                    <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice($order->base_total_due) }}
                                    </p>
                                @else
                                    <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice(0.00) }}
                                    </p>
                                @endif
                            </div>

                            {!! view_render_event('bagisto.admin.sales.order.view.total-due.after') !!}

                        </div>
                    </div>
                </div>

                <!-- Customer's comment form -->
                <div class="box-shadow rounded bg-white dark:bg-gray-900">
                    <p class="p-4 pb-0 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('admin::app.sales.orders.view.comments')
                    </p>

                    <x-admin::form action="{{ route('admin.sales.orders.comment', $order->id) }}">
                        <div class="p-4">
                            <div class="mb-2.5">
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.control
                                        type="textarea"
                                        id="comment"
                                        name="comment"
                                        rules="required"
                                        :label="trans('admin::app.sales.orders.view.comments')"
                                        :placeholder="trans('admin::app.sales.orders.view.write-your-comment')"
                                        rows="3"
                                    />

                                    <x-admin::form.control-group.error control-name="comment" />
                                </x-admin::form.control-group>
                            </div>

                            <div class="flex items-center justify-between">
                                <label
                                    class="flex w-max cursor-pointer select-none items-center gap-1 p-1.5"
                                    for="customer_notified"
                                >
                                    <input
                                        type="checkbox"
                                        name="customer_notified"
                                        id="customer_notified"
                                        value="1"
                                        class="peer hidden"
                                    >

                                    <span
                                        class="icon-uncheckbox peer-checked:icon-checked cursor-pointer rounded-md text-2xl peer-checked:text-blue-600"
                                        role="button"
                                        tabindex="0"
                                    >
                                    </span>

                                    <p class="flex cursor-pointer items-center gap-x-1 font-semibold text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100">
                                        @lang('admin::app.sales.orders.view.notify-customer')
                                    </p>
                                </label>

                                <button
                                    type="submit"
                                    class="secondary-button"
                                    aria-label="{{ trans('admin::app.sales.orders.view.submit-comment') }}"
                                >
                                    @lang('admin::app.sales.orders.view.submit-comment')
                                </button>
                            </div>
                        </div>
                    </x-admin::form>

                    <span class="block w-full border-b dark:border-gray-800"></span>

                    <!-- Comment List -->
                    @foreach ($order->comments()->orderBy('id', 'desc')->get() as $comment)
                        <div class="grid gap-1.5 p-4">
                            <p 
                                class="break-all text-base leading-6 text-gray-800 dark:text-white"
                                v-pre
                            >
                                {{ $comment->comment }}
                            </p>

                            <!-- Notes List Title and Time -->
                            <p class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                                @if ($comment->customer_notified)
                                    <span class="icon-done h-fit rounded-full bg-blue-100 text-2xl text-blue-600"></span>

                                    @lang('admin::app.sales.orders.view.customer-notified', ['date' => core()->formatDate($comment->created_at, 'Y-m-d H:i:s a')])
                                @else
                                    <span class="icon-cancel-1 h-fit rounded-full bg-red-100 text-2xl text-red-600"></span>

                                    @lang('admin::app.sales.orders.view.customer-not-notified', ['date' => core()->formatDate($comment->created_at, 'Y-m-d H:i:s a')])
                                @endif
                            </p>
                        </div>

                        <span class="block w-full border-b dark:border-gray-800"></span>
                    @endforeach
                </div>

                {!! view_render_event('bagisto.admin.sales.order.left_component.after', ['order' => $order]) !!}
            </div>

            <!-- Right Component -->
            <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                {!! view_render_event('bagisto.admin.sales.order.right_component.before', ['order' => $order]) !!}

                <!-- Customer and address information -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.orders.view.customer')
                        </p>
                    </x-slot>

                    <x-slot:content v-pre>
                        <div class="{{ $order->billing_address ? 'pb-4' : '' }}">
                            <div class="flex flex-col gap-1.5">
                                <p 
                                    class="font-semibold text-gray-800 dark:text-white"
                                    v-pre
                                >
                                    {{ $order->customer_full_name }}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.customer_full_name.after', ['order' => $order]) !!}

                                <p
                                    class="text-gray-600 dark:text-gray-300"
                                    v-pre
                                >
                                    {{ $order->customer_email }}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.customer_email.after', ['order' => $order]) !!}

                                <p 
                                    class="text-gray-600 dark:text-gray-300"
                                    v-pre
                                >
                                    @lang('admin::app.sales.orders.view.customer-group') : {{ $order->is_guest ? core()->getGuestCustomerGroup()?->name : ($order->customer->group->name ?? '') }}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.customer_group.after', ['order' => $order]) !!}
                            </div>
                        </div>

                        <!-- Billing Address -->
                        @if ($order->billing_address)
                            <span class="block w-full border-b dark:border-gray-800"></span>

                            <div class="{{ $order->shipping_address ? 'pb-4' : '' }}">

                                <div class="flex items-center justify-between">
                                    <p class="py-4 text-base font-semibold text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.billing-address')
                                    </p>
                                </div>

                                @include ('admin::sales.address', ['address' => $order->billing_address])

                                {!! view_render_event('bagisto.admin.sales.order.billing_address.after', ['order' => $order]) !!}
                            </div>
                        @endif

                        <!-- Shipping Address -->
                        @if ($order->shipping_address)
                            <span class="block w-full border-b dark:border-gray-800"></span>

                            <div class="flex items-center justify-between">
                                <p class="py-4 text-base font-semibold text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.shipping-address')
                                </p>
                            </div>

                            @include ('admin::sales.address', ['address' => $order->shipping_address])

                            {!! view_render_event('bagisto.admin.sales.order.shipping_address.after', ['order' => $order]) !!}
                        @endif
                    </x-slot>
                </x-admin::accordion>

                <!-- Order Information -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.orders.view.order-information')
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div class="flex w-full justify-start gap-5">
                            <div class="flex flex-col gap-y-1.5">
                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.order-date')
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.order-status')
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.channel')
                                </p>
                            </div>

                            <div class="flex flex-col gap-y-1.5">
                                {!! view_render_event('bagisto.admin.sales.order.created_at.before', ['order' => $order]) !!}

                                <!-- Order Date -->
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{core()->formatDate($order->created_at) }}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.created_at.after', ['order' => $order]) !!}

                                <!-- Order Status -->
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{$order->status_label}}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.status_label.after', ['order' => $order]) !!}

                                <!-- Order Channel -->
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{$order->channel_name}}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.channel_name.after', ['order' => $order]) !!}
                            </div>
                        </div>
                    </x-slot>
                </x-admin::accordion>

                <!-- Payment and Shipping Information-->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.orders.view.payment-and-shipping')
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div>
                            <!-- Payment method -->
                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ core()->getConfigData('sales.payment_methods.' . $order->payment->method . '.title') }}
                            </p>

                            <p class="text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.orders.view.payment-method')
                            </p>

                            <!-- Currency -->
                            <p 
                                class="pt-4 font-semibold text-gray-800 dark:text-white"
                                v-pre
                            >
                                {{ $order->order_currency_code }}
                            </p>

                            <p class="text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.orders.view.currency')
                            </p>

                            @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($order->payment->method); @endphp

                            <!-- Additional details -->
                            @if (! empty($additionalDetails))
                                <p 
                                    class="pt-4 font-semibold text-gray-800 dark:text-white"
                                    v-pre
                                >
                                    {{ $additionalDetails['title'] }}
                                </p>

                                <p 
                                    class="text-gray-600 dark:text-gray-300"
                                    v-pre
                                >
                                    {{ $additionalDetails['value'] }}
                                </p>
                            @endif

                            {!! view_render_event('bagisto.admin.sales.order.payment-method.after', ['order' => $order]) !!}
                        </div>

                        <!-- Shipping Method and Price Details -->
                        @if ($order->shipping_address)
                            <span class="mt-4 block w-full border-b dark:border-gray-800"></span>

                            <div class="pt-4">
                                <p 
                                    class="font-semibold text-gray-800 dark:text-white"
                                    v-pre
                                >
                                    {{ $order->shipping_title }}
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.shipping-method')
                                </p>

                                <p class="pt-4 font-semibold text-gray-800 dark:text-white">
                                    {{ core()->formatBasePrice($order->base_shipping_amount) }}
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.shipping-price')
                                </p>
                            </div>

                            {!! view_render_event('bagisto.admin.sales.order.shipping-method.after', ['order' => $order]) !!}
                        @endif
                    </x-slot>
                </x-admin::accordion>

                <!-- Shipment Information-->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.orders.view.shipments') ({{ count($order->shipments) }})
                        </p>
                    </x-slot>

                    <x-slot:content>
                        @forelse ($order->shipments as $shipment)
                            <div class="grid gap-y-2.5">
                                <div>
                                    <!-- Shipment Id -->
                                    <p class="font-semibold text-gray-800 dark:text-white">
                                        @lang('admin::app.sales.orders.view.shipment', ['shipment' => $shipment->id])
                                    </p>

                                    <!-- Shipment Created -->
                                    <p class="text-gray-600 dark:text-gray-300">
                                        {{ core()->formatDate($shipment->created_at, 'd M, Y H:i:s a') }}
                                    </p>
                                </div>

                                <div class="flex gap-2.5">
                                    <a
                                        href="{{ route('admin.sales.shipments.view', $shipment->id) }}"
                                        class="text-sm text-blue-600 transition-all hover:underline"
                                    >
                                        @lang('admin::app.sales.orders.view.view')
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.orders.view.no-shipment-found')
                            </p>
                        @endforelse
                    </x-slot>
                </x-admin::accordion>

                <!-- Invoice Information-->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.orders.view.invoices') ({{ count($order->invoices) }})
                        </p>
                    </x-slot>

                    <x-slot:content>
                        @forelse ($order->invoices as $index => $invoice)
                            <div class="grid gap-y-2.5">
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-white">
                                        @lang('admin::app.sales.orders.view.invoice-id', ['invoice' => $invoice->increment_id ?? $invoice->id])
                                    </p>

                                    <p class="text-gray-600 dark:text-gray-300">
                                        {{ core()->formatDate($invoice->created_at, 'd M, Y H:i:s a') }}
                                    </p>
                                </div>

                                <div class="flex gap-2.5">
                                    <a
                                        href="{{ route('admin.sales.invoices.view', $invoice->id) }}"
                                        class="text-sm text-blue-600 transition-all hover:underline"
                                    >
                                        @lang('admin::app.sales.orders.view.view')
                                    </a>

                                    <a
                                        href="{{ route('admin.sales.invoices.print', $invoice->id) }}"
                                        class="text-sm text-blue-600 transition-all hover:underline"
                                    >
                                        @lang('admin::app.sales.orders.view.download-pdf')
                                    </a>
                                </div>
                            </div>

                            @if ($index < count($order->invoices) - 1)
                                <span class="mb-4 mt-4 block w-full border-b dark:border-gray-800"></span>
                            @endif
                        @empty
                            <p class="text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.orders.view.no-invoice-found')
                            </p>
                        @endforelse
                    </x-slot>
                </x-admin::accordion>

                <!-- Refund Information -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.orders.view.refund')
                        </p>
                    </x-slot>

                    <x-slot:content>
                        @forelse ($order->refunds as $refund)
                            <div class="grid gap-y-2.5">
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-white">
                                        @lang('admin::app.sales.orders.view.refund-id', ['refund' => $refund->id])
                                    </p>

                                    <p class="text-gray-600 dark:text-gray-300">
                                        {{ core()->formatDate($refund->created_at, 'd M, Y H:i:s a') }}
                                    </p>

                                    <p class="mt-4 font-semibold text-gray-800 dark:text-white">
                                        @lang('admin::app.sales.orders.view.name')
                                    </p>

                                    <p 
                                        class="text-gray-600 dark:text-gray-300"
                                        v-pre
                                    >
                                        {{ $refund->order->customer_full_name }}
                                    </p>

                                    <p class="mt-4 font-semibold text-gray-800 dark:text-white">
                                        @lang('admin::app.sales.orders.view.status')
                                    </p>

                                    <p class="text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.refunded')

                                        <span class="font-semibold text-gray-800 dark:text-white">
                                            {{ core()->formatBasePrice($refund->base_grand_total) }}
                                        </span>
                                    </p>
                                </div>

                                <div class="flex gap-2.5">
                                    <a
                                        href="{{ route('admin.sales.refunds.view', $refund->id) }}"
                                        class="text-sm text-blue-600 transition-all hover:underline"
                                    >
                                        @lang('admin::app.sales.orders.view.view')
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.orders.view.no-refund-found')
                            </p>
                        @endforelse
                    </x-slot>
                </x-admin::accordion>

                {!! view_render_event('bagisto.admin.sales.order.right_component.after', ['order' => $order]) !!}
            </div>
        </div>
    </div>

    <!-- Slip Lightbox Modal -->
    <div id="slipModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/85 backdrop-blur-sm" onclick="this.classList.add('hidden'); this.classList.remove('flex')">
        <div class="relative max-h-[90vh] max-w-[90vw] overflow-hidden rounded-lg bg-white p-2 dark:bg-gray-900 shadow-2xl" onclick="event.stopPropagation()">
            <button class="absolute right-4 top-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-black/50 text-white hover:bg-black/70 text-xl font-bold transition-all" onclick="const m = document.getElementById('slipModal'); m.classList.add('hidden'); m.classList.remove('flex')">
                &times;
            </button>
            <img id="slipModalImage" class="max-h-[85vh] max-w-full object-contain rounded" src="" alt="Slip Detail">
        </div>
    </div>

    <!-- Upload Slip Modal -->
    <div id="uploadSlipModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 px-4" onclick="this.classList.add('hidden'); this.classList.remove('flex')">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-2xl dark:bg-gray-900" onclick="event.stopPropagation()">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">อัปโหลดสลิปโอนเงิน</h2>
                <button
                    type="button"
                    onclick="const m = document.getElementById('uploadSlipModal'); m.classList.add('hidden'); m.classList.remove('flex')"
                    class="text-2xl leading-none text-gray-400 hover:text-gray-600"
                >
                    &times;
                </button>
            </div>

            <form
                method="POST"
                enctype="multipart/form-data"
                action="{{ route('admin.sales.orders.slip.upload', $order->id) }}"
            >
                @csrf

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        เลือกไฟล์สลิป (JPG, PNG, PDF)
                    </label>
                    <input
                        type="file"
                        name="slip"
                        accept="image/*,.pdf"
                        required
                        class="block w-full rounded-md border border-gray-300 p-2.5 text-sm text-gray-700 focus:border-gray-400 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                    >
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="const m = document.getElementById('uploadSlipModal'); m.classList.add('hidden'); m.classList.remove('flex')"
                        class="rounded-md border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300"
                    >
                        ยกเลิก
                    </button>

                    <button type="submit" class="primary-button">
                        อัปโหลด
                    </button>
                </div>
            </form>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            window.showSlipModal = function(url) {
                const modal = document.getElementById('slipModal');
                const img = document.getElementById('slipModalImage');
                if (modal && img) {
                    img.src = url;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };

            window.showUploadSlipModal = function() {
                const modal = document.getElementById('uploadSlipModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };
        </script>
    @endPushOnce
</x-admin::layouts>
