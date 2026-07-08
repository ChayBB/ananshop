<!-- COD Payment Vue Component -->
<v-cod-payment>
    <div class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800">
        <span class="icon-sales text-2xl"></span>
        เก็บเงินปลายทาง
    </div>
</v-cod-payment>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-cod-payment-template"
    >
        <div>
            <div
                class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                @click="$refs.codDrawer.open()"
            >
                <span
                    class="icon-sales text-2xl"
                    role="presentation"
                    tabindex="0"
                ></span>
                เก็บเงินปลายทาง
            </div>

            <!-- COD Drawer -->
            <x-admin::form
                method="POST"
                :action="route('admin.sales.invoices.store', $order->id)"
            >
                <x-admin::drawer ref="codDrawer">
                    <!-- Drawer Header -->
                    <x-slot:header>
                        <div class="grid h-8 gap-3">
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-medium dark:text-white">
                                    เก็บเงินปลายทาง (Cash on Delivery)
                                </p>
                            </div>
                        </div>
                    </x-slot>

                    <!-- Drawer Content -->
                    <x-slot:content class="!p-6">
                        <div class="grid gap-4">
                            <!-- Information -->
                            <div class="flex flex-col gap-2 rounded-md border border-slate-200 p-4 dark:border-gray-800 bg-gray-50 dark:bg-gray-950">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">เลขที่คำสั่งซื้อ:</span>
                                    <span class="font-semibold text-gray-800 dark:text-white font-medium">#{{ $order->increment_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">ชื่อลูกค้า:</span>
                                    <span class="font-semibold text-gray-800 dark:text-white font-medium">{{ $order->customer_full_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">ยอดรวมทั้งหมดที่ต้องชำระ:</span>
                                    <span class="font-bold text-blue-600 dark:text-blue-400 text-lg">{{ core()->formatBasePrice($order->base_grand_total) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">สถานะชำระเงิน:</span>
                                    @if ($order->canInvoice())
                                        <span class="label-pending text-sm px-2 py-0.5 rounded">รอการชำระเงินปลายทาง</span>
                                    @else
                                        <span class="label-completed text-sm px-2 py-0.5 rounded">เก็บเงินปลายทางเรียบร้อยแล้ว</span>
                                    @endif
                                </div>
                            </div>

                            @if ($order->canInvoice())
                                <!-- Items Summary to confirm -->
                                <p class="font-semibold text-gray-800 dark:text-white mt-2">รายการสินค้าที่ต้องชำระเงินปลายทาง</p>
                                <div class="grid gap-3">
                                    @foreach ($order->items as $item)
                                        @if ($item->qty_to_invoice)
                                            <!-- Hidden inputs for invoice quantities -->
                                            <input 
                                                type="hidden" 
                                                name="invoice[items][{{ $item->id }}]" 
                                                value="{{ $item->qty_to_invoice }}"
                                            />
                                            <div class="flex justify-between items-center border-b pb-2 dark:border-gray-800">
                                                <div class="grid gap-1">
                                                    <span class="text-gray-800 dark:text-white font-medium">{{ $item->name }}</span>
                                                    <span class="text-gray-500 text-xs">SKU: {{ $item->sku }}</span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-gray-800 dark:text-white font-semibold">x {{ $item->qty_to_invoice }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <!-- Action button -->
                                <div class="mt-4 flex justify-end">
                                    <button
                                        type="submit"
                                        class="primary-button"
                                    >
                                        ยืนยันการรับชำระเงินปลายทาง
                                    </button>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center p-8 gap-3 text-center">
                                    <span class="icon-checked text-5xl text-green-600"></span>
                                    <p class="text-gray-600 dark:text-gray-400 font-semibold">
                                        คำสั่งซื้อนี้ได้รับการยืนยันการชำระเงินปลายทางและออกใบกำกับภาษีเรียบร้อยแล้ว
                                    </p>
                                </div>
                            @endif
                        </div>
                    </x-slot>
                </x-admin::drawer>
            </x-admin::form>
        </div>
    </script>

    <script type="module">
        app.component('v-cod-payment', {
            template: '#v-cod-payment-template',
        });
    </script>
@endPushOnce
