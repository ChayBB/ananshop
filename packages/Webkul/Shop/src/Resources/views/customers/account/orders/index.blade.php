<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.orders.title')
    </x-slot>

    <!-- Breadcrumbs -->
    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
            <x-shop::breadcrumbs name="orders" />
        @endSection
    @endif

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <div class="mb-8 flex items-center max-sm:mb-5">
            <!-- Back Button -->
            <a
                class="grid md:hidden"
                href="{{ route('shop.customers.account.index') }}"
            >
                <span class="icon-arrow-left rtl:icon-arrow-right text-2xl"></span>
            </a>

            <h2 class="text-2xl font-medium ltr:ml-2.5 rtl:mr-2.5 max-sm:text-base md:ltr:ml-0 md:rtl:mr-0">
                @lang('shop::app.customers.account.orders.title')
            </h2>
        </div>

        {!! view_render_event('bagisto.shop.customers.account.orders.list.before') !!}

        <!-- For Desktop View -->
        <div class="max-md:hidden">
            <x-shop::datagrid :src="route('shop.customers.account.orders.index')">
                <!-- Custom body slot to add upload slip button -->
                <template #body="{
                    isLoading,
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">
                    <template v-if="isLoading">
                        <x-shop::shimmer.datagrid.table.body />
                    </template>

                    <template v-else>
                        <template v-for="record in available.records">
                            <div class="row grid items-center gap-2.5 border-b px-4 py-6 text-sm text-gray-600 transition-all hover:bg-gray-50 last:border-none"
                                style="grid-template-columns: 1fr 1.5fr 1fr 1fr auto;">

                                <!-- Order ID -->
                                <p class="font-medium text-gray-800">
                                    #@{{ record.increment_id }}
                                </p>

                                <!-- Date -->
                                <p>@{{ record.created_at }}</p>

                                <!-- Total -->
                                <p class="font-semibold text-gray-800">@{{ record.grand_total }}</p>

                                <!-- Status -->
                                <p v-html="record.status"></p>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <!-- View button -->
                                    <a
                                        :href="record.actions[0].url"
                                        class="icon-eye cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100"
                                    >
                                    </a>

                                    <!-- Upload/View Slip action (rendered server-side) -->
                                    <span v-html="record.slip_actions"></span>
                                </div>
                            </div>
                        </template>
                    </template>
                </template>
            </x-shop::datagrid>
        </div>

        <!-- For Mobile View -->
        <div class="md:hidden">
            <x-shop::datagrid :src="route('shop.customers.account.orders.index')">
                <!-- Datagrid Header -->
                <template #header="{
                    isLoading,
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">
                    <div class="hidden"></div>
                </template>

                <template #body="{
                    isLoading,
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">
                    <template v-if="isLoading">
                        <x-shop::shimmer.datagrid.table.body />
                    </template>
    
                    <template v-else>
                        <template v-for="record in available.records">
                            <div class="w-full p-4 border rounded-md transition-all hover:bg-gray-50 [&>*]:border-0 mb-4 last:mb-0">
                                <a :href="record.actions[0].url">
                                    <div class="flex justify-between">
                                        <div class="text-sm font-semibold">
                                            @lang('shop::app.customers.account.orders.order-id'): #@{{ record.id }}
    
                                            <p class="text-xs font-normal text-neutral-500">
                                                @{{ record.created_at }}
                                            </p>
                                        </div>
    
                                        <p v-html="record.status"></p>
                                    </div>
        
                                    <div class="mt-2.5 text-xs font-normal text-neutral-500">
                                        @lang('shop::app.customers.account.orders.subtotal')
    
                                        <p class="text-xl font-semibold text-black">
                                            @{{ record.grand_total }}
                                        </p>
                                    </div>
                                </a>

                                <!-- Upload/View Slip action (rendered server-side) -->
                                <template v-if="record.slip_actions">
                                    <div class="mt-3 border-t pt-3" v-html="record.slip_actions"></div>
                                </template>
                            </div>
                        </template>
                    </template>
                </template>
            </x-shop::datagrid>
        </div>
    
        {!! view_render_event('bagisto.shop.customers.account.orders.list.after') !!}

    </div>

    <!-- View Slip Modal (shared) -->
    <div id="viewSlipModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 px-4" onclick="this.classList.add('hidden'); this.classList.remove('flex')">
        <div class="w-full max-w-lg overflow-hidden rounded-lg bg-white shadow-xl" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between gap-5 border-b border-zinc-200 p-6">
                <p id="viewSlipModalTitle" class="text-base font-semibold text-gray-800"></p>

                <span
                    class="icon-cancel cursor-pointer text-3xl"
                    onclick="const m = document.getElementById('viewSlipModal'); m.classList.add('hidden'); m.classList.remove('flex')"
                ></span>
            </div>

            <div class="flex items-center justify-center bg-white p-6">
                <img id="viewSlipModalImage" src="" class="max-h-[70vh] max-w-full rounded border object-contain" alt="Slip">
            </div>
        </div>
    </div>

    <!-- Upload Slip Modal (shared) -->
    <div id="uploadSlipModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 px-4" onclick="this.classList.add('hidden'); this.classList.remove('flex')">
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between gap-5 border-b border-zinc-200 p-6">
                <div>
                    <p class="text-base font-semibold text-gray-800">
                        อัปโหลดหลักฐานการชำระเงิน
                    </p>

                    <p id="uploadSlipModalOrderId" class="mt-1 text-sm text-gray-500"></p>
                </div>

                <span
                    class="icon-cancel cursor-pointer text-3xl"
                    onclick="const m = document.getElementById('uploadSlipModal'); m.classList.add('hidden'); m.classList.remove('flex')"
                ></span>
            </div>

            <div class="bg-white p-6">
                <form id="uploadSlipForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            เลือกไฟล์สลิป (JPG, PNG, PDF)
                        </label>
                        <input
                            type="file"
                            name="slip"
                            accept=".jpg,.jpeg,.png,.gif,.webp,.pdf"
                            required
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            รองรับไฟล์ JPG, PNG, GIF, WEBP, PDF ขนาดไม่เกิน 10MB
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="primary-button">
                            อัปโหลด
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            window.showViewSlipModal = function(url, incrementId) {
                const modal = document.getElementById('viewSlipModal');
                const img = document.getElementById('viewSlipModalImage');
                const title = document.getElementById('viewSlipModalTitle');

                if (modal && img) {
                    img.src = url;

                    if (title) {
                        title.textContent = 'สลิปโอนเงิน ของคำสั่งซื้อ #' + incrementId;
                    }

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };

            window.showUploadSlipModal = function(orderId, incrementId) {
                const modal = document.getElementById('uploadSlipModal');
                const form = document.getElementById('uploadSlipForm');
                const orderText = document.getElementById('uploadSlipModalOrderId');

                if (modal && form) {
                    form.action = '{{ url('checkout/slip') }}/' + orderId;

                    if (orderText) {
                        orderText.textContent = 'คำสั่งซื้อ #' + incrementId;
                    }

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };
        </script>
    @endPushOnce
</x-shop::layouts.account>
