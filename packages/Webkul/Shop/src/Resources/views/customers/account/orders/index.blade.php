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

                                    <!-- Upload Slip button (only for pending status) -->
                                    <template v-if="record.status && record.status.includes('label-pending') && !record.status.includes('pending-payment') && record.method !== 'cashondelivery' && !record.slip_path">
                                        <button
                                            type="button"
                                            class="flex items-center gap-1 rounded-md border border-orange-400 bg-orange-50 px-2 py-1 text-xs font-medium text-orange-600 transition-all hover:bg-orange-100"
                                            @click="$refs['slip-modal-' + record.id].toggle()"
                                        >
                                            <span class="icon-upload text-sm"></span>
                                            อัปโหลดสลิป
                                        </button>

                                        <!-- Upload Slip Modal -->
                                        <x-shop::modal ::ref="'slip-modal-' + record.id">
                                            <x-slot:toggle></x-slot>

                                            <x-slot:header>
                                                <p class="text-base font-semibold text-gray-800">
                                                    อัปโหลดหลักฐานการชำระเงิน
                                                </p>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    คำสั่งซื้อ #@{{ record.increment_id }}
                                                </p>
                                            </x-slot>

                                            <x-slot:content>
                                                <form
                                                    :action="`{{ url('checkout/slip') }}/` + record.id"
                                                    method="POST"
                                                    enctype="multipart/form-data"
                                                    class="space-y-4"
                                                    :id="'slip-form-' + record.id"
                                                >
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
                                                </form>
                                            </x-slot>

                                            <x-slot:footer>
                                                <button
                                                    type="submit"
                                                    :form="'slip-form-' + record.id"
                                                    class="primary-button"
                                                >
                                                    อัปโหลด
                                                </button>
                                            </x-slot>
                                        </x-shop::modal>
                                    </template>

                                    <!-- View Slip button (if uploaded) -->
                                    <template v-if="record.slip_path">
                                        <button
                                            type="button"
                                            class="flex items-center gap-1 rounded-md border border-zinc-200 bg-zinc-50 px-2 py-1 text-xs font-medium text-zinc-600 transition-all hover:bg-zinc-100"
                                            @click="$refs['view-slip-modal-' + record.id].toggle()"
                                        >
                                            <span class="icon-file text-sm"></span>
                                            ดูสลิป
                                        </button>

                                        <!-- View Slip Modal -->
                                        <x-shop::modal ::ref="'view-slip-modal-' + record.id">
                                            <x-slot:toggle></x-slot>

                                            <x-slot:header>
                                                <p class="text-base font-semibold text-gray-800">
                                                    สลิปโอนเงิน ของคำสั่งซื้อ #@{{ record.increment_id }}
                                                </p>
                                            </x-slot>

                                            <x-slot:content>
                                                <div class="flex justify-center items-center p-2 bg-white rounded-md">
                                                    <img :src="'/storage/' + record.slip_path" class="max-w-full max-h-[70vh] object-contain rounded border" />
                                                </div>
                                            </x-slot>
                                        </x-shop::modal>
                                    </template>
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

                                <!-- Upload Slip button for mobile (pending orders only) -->
                                <template v-if="record.status && record.status.includes('label-pending') && !record.status.includes('pending-payment') && record.method !== 'cashondelivery' && !record.slip_path">
                                    <div class="mt-3 border-t pt-3">
                                        <button
                                            type="button"
                                            class="flex w-full items-center justify-center gap-1.5 rounded-md border border-orange-400 bg-orange-50 px-3 py-2 text-sm font-medium text-orange-600 hover:bg-orange-100"
                                            @click="$refs['slip-modal-mob-' + record.id].toggle()"
                                        >
                                            <span class="icon-upload text-base"></span>
                                            อัปโหลดสลิปชำระเงิน
                                        </button>

                                        <x-shop::modal ::ref="'slip-modal-mob-' + record.id">
                                            <x-slot:toggle></x-slot>

                                            <x-slot:header>
                                                <p class="text-base font-semibold text-gray-800">
                                                    อัปโหลดหลักฐานการชำระเงิน
                                                </p>
                                            </x-slot>

                                            <x-slot:content>
                                                <form
                                                    :action="`{{ url('checkout/slip') }}/` + record.id"
                                                    method="POST"
                                                    enctype="multipart/form-data"
                                                    :id="'slip-form-mob-' + record.id"
                                                >
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
                                                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                                                        >
                                                    </div>
                                                </form>
                                            </x-slot>

                                            <x-slot:footer>
                                                <button
                                                    type="submit"
                                                    :form="'slip-form-mob-' + record.id"
                                                    class="primary-button"
                                                >
                                                    อัปโหลด
                                                </button>
                                            </x-slot>
                                        </x-shop::modal>
                                    </div>
                                </template>

                                <!-- View Slip button for mobile if uploaded -->
                                <template v-if="record.slip_path">
                                    <div class="mt-3 border-t pt-3">
                                        <button
                                            type="button"
                                            class="flex w-full items-center justify-center gap-1.5 rounded-md border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium text-zinc-600 hover:bg-zinc-100"
                                            @click="$refs['view-slip-modal-mob-' + record.id].toggle()"
                                        >
                                            <span class="icon-file text-base"></span>
                                            ดูสลิป
                                        </button>

                                        <!-- View Slip Modal for Mobile -->
                                        <x-shop::modal ::ref="'view-slip-modal-mob-' + record.id">
                                            <x-slot:toggle></x-slot>

                                            <x-slot:header>
                                                <p class="text-base font-semibold text-gray-800">
                                                    สลิปโอนเงิน ของคำสั่งซื้อ #@{{ record.increment_id }}
                                                </p>
                                            </x-slot>

                                            <x-slot:content>
                                                <div class="flex justify-center items-center p-2 bg-white rounded-md">
                                                    <img :src="'/storage/' + record.slip_path" class="max-w-full max-h-[70vh] object-contain rounded border" />
                                                </div>
                                            </x-slot>
                                        </x-shop::modal>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </template>
                </template>
            </x-shop::datagrid>
        </div>
    
        {!! view_render_event('bagisto.shop.customers.account.orders.list.after') !!}

    </div>
</x-shop::layouts.account>
