<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.sales.invoices.view.title', ['invoice_id' => $invoice->increment_id ?? $invoice->id])
    </x-slot>

    @php
        $order = $invoice->order;
    @endphp

    <!-- Main Body -->
    <div class="grid">
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            {!! view_render_event('bagisto.admin.sales.invoice.title.before', ['order' => $order]) !!}

            <p class="text-xl font-bold leading-6 text-gray-800 dark:text-white">
                @lang('admin::app.sales.invoices.view.title', ['invoice_id' => $invoice->increment_id ?? $invoice->id])

                <span class="{{ $invoice->status_label_class }} mx-1.5 text-sm">
                    {{ $invoice->status_label }}
                </span>
            </p>

            {!! view_render_event('bagisto.admin.sales.invoice.title.after', ['order' => $order]) !!}

            <div class="flex items-center gap-x-2.5">
                <!-- Back Button -->
                <a
                    href="{{ route('admin.sales.invoices.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('admin::app.account.edit.back-btn')
                </a>
            </div>
        </div>
    </div>

    <!-- Filter row -->
    <div class="flex items-center justify-between gap-4 mt-7 max-md:flex-wrap">
        <div class="flex flex-wrap items-center gap-x-1 gap-y-2">
            {!! view_render_event('bagisto.admin.sales.invoice.page_action.before', ['order' => $order]) !!}

            <a
                href="{{ route('admin.sales.invoices.print', $invoice->id) }}"
                class="inline-flex w-full max-w-max cursor-pointer items-center justify-between gap-x-2 px-1 py-1.5 text-center font-semibold text-gray-600 transition-all hover:rounded-md hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-800"
            >
                <span class="text-2xl icon-printer"></span>

                @lang('admin::app.sales.invoices.view.print')
            </a>

            <!-- Send Duplicate Invoice Modal -->
            <div>
                <button
                    type="button"
                    class="inline-flex w-full max-w-max cursor-pointer items-center justify-between gap-x-2 px-1 py-1.5 text-center font-semibold text-gray-600 transition-all hover:rounded-md hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-800"
                    @click="$refs.groupCreateModal.open()"
                >
                    <span class="text-2xl icon-mail"></span>

                    @lang('admin::app.sales.invoices.view.send-duplicate-invoice')
                </button>

                <x-admin::form :action="route('admin.sales.invoices.send_duplicate_email', $invoice->id)">
                    <!-- Create Group Modal -->
                    <x-admin::modal ref="groupCreateModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @lang('admin::app.sales.invoices.view.send-duplicate-invoice')
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.sales.invoices.view.email')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="email"
                                    id="email"
                                    name="email"
                                    rules="required|email"
                                    :value="$invoice->order->customer_email"
                                    :label="trans('admin::app.sales.invoices.view.email')"
                                />

                                <x-admin::form.control-group.error control-name="email" />
                            </x-admin::form.control-group>
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <!-- Save Button -->
                            <x-admin::button
                                button-type="button"
                                class="primary-button"
                                :title="trans('admin::app.sales.invoices.view.send')"
                            />
                        </x-slot>
                    </x-admin::modal>
                </x-admin::form>
            </div>

            {!! view_render_event('bagisto.admin.sales.invoice.page_action.after', ['order' => $order]) !!}

        </div>
    </div>

    <!-- body content -->
    <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
        <!-- Left sub-component -->
        <div class="flex flex-col flex-1 gap-2 max-xl:flex-auto">
            <!-- Invoice Item Section -->
            <div class="bg-white rounded box-shadow dark:bg-gray-900">
                <p class="p-4 mb-4 text-base font-semibold text-gray-800 dark:text-white">
                    @lang('admin::app.sales.invoices.view.invoice-items') ({{ count($invoice->items) }})
                </p>

                <div class="grid">
                    <!-- Invoice Item Details-->
                    @foreach($invoice->items as $item)
                        <div class="flex justify-between gap-2.5 border-b border-slate-300 px-4 py-6 dark:border-gray-800">
                            <div class="flex gap-2.5">
                                <!-- Product Image -->
                                @if ($item->product?->base_image_url)
                                    <img
                                        class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded"
                                        src="{{ $item->product->base_image_url }}"
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
                                    <!-- Item Name -->
                                    <p 
                                        class="text-base font-semibold text-gray-800 break-all dark:text-white"
                                        v-pre
                                    >
                                        {{ $item->name }}
                                    </p>

                                    <p class="text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.invoices.view.amount-per-unit', [
                                            'amount' => core()->formatBasePrice($item->base_price),
                                            'qty'    => $item->qty,
                                            ])
                                    </p>

                                    <div class="flex flex-col place-items-start gap-1.5">
                                        @if (isset($item->additional['attributes']))
                                            <!-- Item Additional Details -->
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

                                        <!--SKU -->
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.sku', ['sku' => $item->getTypeInstance()->getOrderedItem($item)->sku])
                                        </p>

                                        <!-- Quantity -->
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.qty', ['qty' => $item->qty])
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-1 place-content-start">
                                <!-- Item Grand Total -->
                                <p class="flex items-center justify-end text-base font-semibold text-gray-800 gap-x-1 dark:text-white">
                                    {{ core()->formatBasePrice($item->base_total + $item->base_tax_amount - $item->base_discount_amount) }}
                                </p>

                                <!-- Item Base Price -->
                                <div class="flex flex-col place-items-start items-end gap-1.5">
                                    @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.price', ['price' => core()->formatBasePrice($item->base_price_incl_tax)])
                                        </p>
                                    @elseif (core()->getConfigData('sales.taxes.sales.display_prices') == 'both')
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.price-excl-tax', ['price' => core()->formatBasePrice($item->base_price)])
                                        </p>

                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.price-incl-tax', ['price' => core()->formatBasePrice($item->base_price_incl_tax)])
                                        </p>
                                    @else
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.price', ['price' => core()->formatBasePrice($item->base_price)])
                                        </p>
                                    @endif

                                    <!-- Item Tax Amount -->
                                    <p class="text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.invoices.view.tax', ['tax' => core()->formatBasePrice($item->base_tax_amount)])
                                    </p>

                                    <!-- Item Discount -->
                                    @if ($invoice->base_discount_amount > 0)
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.discount', ['discount' => core()->formatBasePrice($item->base_discount_amount)])
                                        </p>
                                    @endif

                                    <!-- Item Sub-Total -->
                                    @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.sub-total', ['sub_total' => core()->formatBasePrice($item->base_total_incl_tax)])
                                        </p>
                                    @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.sub-total-excl-tax', ['sub_total' => core()->formatBasePrice($item->base_total)])
                                        </p>

                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.sub-total-incl-tax', ['sub_total' => core()->formatBasePrice($item->base_total_incl_tax)])
                                        </p>
                                    @else
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.invoices.view.sub-total', ['sub_total' => core()->formatBasePrice($item->base_total)])
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!--Sale Summary -->
                <div class="mt-4 flex w-full justify-between items-start gap-4 p-4">
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
                    @else
                        <div class="flex flex-col gap-2">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">
                                หลักฐานการชำระเงิน
                            </p>
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
                        </div>
                    @endif

                    <div class="flex gap-2.5">
                        <div class="flex flex-col gap-y-1.5">
                        @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                            <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.invoices.view.sub-total-summary-excl-tax')
                            </p>

                            <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.invoices.view.sub-total-summary-incl-tax')
                            </p>
                        @else
                            <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.invoices.view.sub-total-summary')
                            </p>
                        @endif

                        @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                            <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.invoices.view.shipping-and-handling-excl-tax')
                            </p>

                            <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.invoices.view.shipping-and-handling-incl-tax')
                            </p>
                        @else
                            <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.invoices.view.shipping-and-handling')
                            </p>
                        @endif

                        <p class="!leading-5 text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.invoices.view.summary-tax')
                        </p>

                        @if ($invoice->base_discount_amount > 0)
                            <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.invoices.view.summary-discount')
                            </p>
                        @endif

                        <p class="text-base font-semibold !leading-5 text-gray-800 dark:text-white">
                            @lang('admin::app.sales.invoices.view.grand-total')
                        </p>
                    </div>

                    <div class="flex flex-col gap-y-1.5">
                        <!-- Subtotal -->
                        @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                            <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                {{ core()->formatBasePrice($invoice->base_sub_total_incl_tax) }}
                            </p>
                        @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                            <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                {{ core()->formatBasePrice($invoice->base_sub_total) }}
                            </p>

                            <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                {{ core()->formatBasePrice($invoice->base_sub_total_incl_tax) }}
                            </p>
                        @else
                            <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                {{ core()->formatBasePrice($invoice->base_sub_total) }}
                            </p>
                        @endif

                        <!-- Shipping and Handling -->
                        @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'including_tax')
                            <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                {{ core()->formatBasePrice($invoice->base_shipping_amount_incl_tax) }}
                            </p>
                        @elseif (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                            <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                {{ core()->formatBasePrice($invoice->base_shipping_amount) }}
                            </p>

                            <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                {{ core()->formatBasePrice($invoice->base_shipping_amount_incl_tax) }}
                            </p>
                        @else
                            <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                {{ core()->formatBasePrice($invoice->base_shipping_amount) }}
                            </p>
                        @endif

                        <!-- Tax -->
                        <p class="!leading-5 text-gray-600 dark:text-gray-300">
                            {{ core()->formatBasePrice($invoice->base_tax_amount) }}
                        </p>

                        <!-- Discount -->
                        @if ($invoice->base_discount_amount > 0)
                            <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                {{ core()->formatBasePrice($invoice->base_discount_amount) }}
                            </p>
                        @endif

                        <!-- Grand Total -->
                        <p class="text-base font-semibold !leading-5 text-gray-800 dark:text-white">
                            {{ core()->formatBasePrice($invoice->base_grand_total) }}
                        </p>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Right sub-component -->
        <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
            <!-- component 1 -->
            <x-admin::accordion>
                <x-slot:header>
                    <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                        @lang('admin::app.sales.invoices.view.customer')
                    </p>
                </x-slot>

                <x-slot:content v-pre>
                    <div class="flex flex-col {{ $order->billing_address ? 'pb-4' : ''}}">
                        <p 
                            class="font-semibold text-gray-800 dark:text-white"
                            v-text="'{{ $invoice->order->customer_full_name }}'"
                        >
                        </p>

                        {!! view_render_event('bagisto.admin.sales.invoice.customer_name.after', ['order' => $order]) !!}

                        <p class="text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.invoices.view.customer-email', ['email' => $invoice->order->customer_email])
                        </p>

                        {!! view_render_event('bagisto.admin.sales.invoice.customer_email.after', ['order' => $order]) !!}
                    </div>

                    @if ($order->billing_address || $order->shipping_address)
                        <!-- Billing Address -->
                        @if ($order->billing_address)
                            <div class="{{ $order->shipping_address ? 'pb-4' : '' }}">
                                <span class="block w-full border-b dark:border-gray-800"></span>

                                <div class="flex items-center justify-between">
                                    <p class="py-4 text-base font-semibold text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.invoices.view.billing-address')
                                    </p>
                                </div>

                                @include ('admin::sales.address', ['address' => $order->billing_address])

                                {!! view_render_event('bagisto.admin.sales.invoice.billing_address.after', ['order' => $order]) !!}
                            </div>
                        @endif

                        <!-- Shipping Address -->
                        @if ($order->shipping_address)
                            <span class="block w-full border-b dark:border-gray-800"></span>

                            <div class="flex items-center justify-between">
                                <p class="py-4 text-base font-semibold text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.invoices.view.shipping-address')
                                </p>
                            </div>

                            @include ('admin::sales.address', ['address' => $order->shipping_address])

                            {!! view_render_event('bagisto.admin.sales.invoice.shipping_address.after', ['order' => $order]) !!}
                        @endif
                    @endif
                </x-slot>
            </x-admin::accordion>

            <!-- component 2 -->
            <x-admin::accordion>
                <x-slot:header>
                    <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                        @lang('admin::app.sales.invoices.view.order-information')
                    </p>
                </x-slot>

                <x-slot:content>
                    <div class="flex justify-start w-full gap-5">
                        <div class="flex flex-col gap-y-1.5">
                            @foreach (['order-id', 'order-date', 'order-status', 'invoice-status', 'channel'] as $item)
                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.invoices.view.' . $item)
                                </p>
                            @endforeach
                        </div>

                        <div class="flex flex-col gap-y-1.5">
                            <!-- Order Id -->
                            <p class="font-semibold text-blue-600 transition-all hover:underline">
                                <a href="{{ route('admin.sales.orders.view', $order->id) }}">#{{ $order->increment_id }}</a>
                            </p>

                            {!! view_render_event('bagisto.admin.sales.invoice.increment_id.after', ['order' => $order]) !!}

                            <!-- Order Date -->
                            <p class="text-gray-600 dark:text-gray-300">
                                {{ core()->formatDate($order->created_at) }}
                            </p>

                            {!! view_render_event('bagisto.admin.sales.invoice.created_at.after', ['order' => $order]) !!}

                            <!-- Order Status -->
                            <p class="text-gray-600 dark:text-gray-300">
                                {{ $order->status_label }}
                            </p>

                            {!! view_render_event('bagisto.admin.sales.invoice.status_label.after', ['order' => $order]) !!}

                            <!-- Invoice Status -->
                            <p class="text-gray-600 dark:text-gray-300">
                                {{ $invoice->status_label }}
                            </p>

                            <!-- Order Channel -->
                            <p 
                                class="text-gray-600 dark:text-gray-300"
                                v-pre
                            >
                                {{ $order->channel_name }}
                            </p>

                            {!! view_render_event('bagisto.admin.sales.invoice.channel_name.after', ['order' => $order]) !!}
                        </div>
                    </div>
                </x-slot>
            </x-admin::accordion>
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
