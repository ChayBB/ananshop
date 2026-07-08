<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.components.layouts.sidebar.credit')
    </x-slot>

    @push('styles')
        <style>
            @media (min-width: 768px) {
                .admin-orders-grid, .admin-orders-grid-header {
                    display: grid !important;
                    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
                }
            }
            @media (min-width: 525px) and (max-width: 767px) {
                .admin-orders-grid, .admin-orders-grid-header {
                    display: grid !important;
                    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                }
            }
            @media (max-width: 524px) {
                .admin-orders-grid, .admin-orders-grid-header {
                    display: grid !important;
                    grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
                }
            }
        </style>
    @endpush

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="py-3 text-xl font-bold text-gray-800 dark:text-white">
            @lang('admin::app.components.layouts.sidebar.credit')
        </p>

        <div class="flex items-center gap-x-2.5">
            <x-admin::datagrid.export src="{{ route('admin.sales.credit.index') }}" />
        </div>
    </div>

    <x-admin::datagrid :src="route('admin.sales.credit.index')" :isMultiRow="true">
        <template #header="{
            isLoading,
            available,
            applied,
            selectAll,
            sort,
            performAction
        }">
            <template v-if="isLoading">
                <x-admin::shimmer.datagrid.table.head :isMultiRow="true" />
            </template>

            <template v-else>
                <!-- Grid Header Columns -->
                <div class="row admin-orders-grid-header items-center border-b px-2 sm:px-4 py-2.5 dark:border-gray-800">
                    <div
                        class="flex select-none items-center gap-2.5"
                        v-for="columnGroup in [['increment_id', 'created_at', 'status'], ['base_grand_total', 'method', 'channel_id'], ['full_name', 'customer_email', 'location'], ['items'], ['credit_status'], ['slip_path']]"
                    >
                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base">
                            <span class="[&>*]:after:content-['_/_']">
                                <template v-for="column in columnGroup">
                                    <span
                                        class="after:content-['/'] last:after:content-['']"
                                        :class="{
                                            'font-medium text-gray-800 dark:text-white': applied.sort.column == column,
                                            'cursor-pointer hover:text-gray-800 dark:hover:text-white': available.columns.find(columnTemp => columnTemp.index === column)?.sortable,
                                        }"
                                        @click="
                                            available.columns.find(columnTemp => columnTemp.index === column)?.sortable ? sort(available.columns.find(columnTemp => columnTemp.index === column)) : {}
                                        "
                                    >
                                        @{{ available.columns.find(columnTemp => columnTemp.index === column)?.label }}
                                    </span>
                                </template>
                            </span>

                            <i
                                class="align-text-bottom text-base text-gray-800 dark:text-white ltr:ml-1.5 rtl:mr-1.5"
                                :class="[applied.sort.order === 'asc' ? 'icon-down-stat': 'icon-up-stat']"
                                v-if="columnGroup.includes(applied.sort.column)"
                            >
                            </i>
                        </p>
                    </div>
                </div>
            </template>
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
                <x-admin::shimmer.datagrid.table.body :isMultiRow="true" />
            </template>

            <template v-else>
                <!-- Order Rows -->
                <div
                    class="row admin-orders-grid gap-y-4 border-b px-2 sm:px-4 py-2.5 transition-all hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950"
                    v-for="record in available.records"
                >
                    <!-- Order Id, Created, Status Section -->
                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm sm:text-base font-semibold text-gray-800 dark:text-white">
                            @{{ "@lang('admin::app.sales.orders.index.datagrid.id')".replace(':id', record.increment_id) }}
                        </p>

                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                            @{{ record.created_at }}
                        </p>

                        <p v-html="record.status"></p>
                    </div>

                    <!-- Total Amount, Pay Via, Channel -->
                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm sm:text-base font-semibold text-gray-800 dark:text-white">
                            @{{ $admin.formatPrice(record.base_grand_total) }}
                        </p>

                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.orders.index.datagrid.pay-by', ['method' => ''])@{{ record.method }}
                        </p>

                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                            @{{ record.channel_name }}
                        </p>
                    </div>

                    <!-- Customer, Email, Location Section -->
                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm sm:text-base text-gray-800 dark:text-white">
                            @{{ record.full_name }}
                        </p>

                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                            @{{ record.customer_email }}
                        </p>

                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                            @{{ record.location }}
                        </p>
                    </div>

                    <!-- Products Section -->
                    <div
                        class="flex flex-col gap-1.5 text-xs sm:text-sm"
                        v-html="record.items"
                    >
                    </div>

                    <!-- Credit Used / Remaining Section -->
                    <div
                        class="flex flex-col gap-1.5 text-xs sm:text-sm"
                        v-html="record.credit_status"
                    >
                    </div>

                    <!-- Slip Section -->
                    <div class="flex items-center justify-between gap-x-2">
                        <div
                            class="flex flex-col gap-1.5 text-xs sm:text-sm"
                            v-html="record.slip_path"
                        >
                        </div>

                        <a :href="'{{ route('admin.sales.orders.view', ':id') }}'.replace(':id', record.id)">
                            <span class="icon-sort-right rtl:icon-sort-left cursor-pointer p-1.5 text-xl sm:text-2xl hover:rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 ltr:ml-1 rtl:mr-1"></span>
                        </a>
                    </div>
                </div>
            </template>
        </template>
    </x-admin::datagrid>

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
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900" onclick="event.stopPropagation()">
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

            <form id="uploadSlipForm" method="POST" enctype="multipart/form-data">
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
                        class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-700 focus:border-gray-400 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                    >
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="const m = document.getElementById('uploadSlipModal'); m.classList.add('hidden'); m.classList.remove('flex')"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300"
                    >
                        ยกเลิก
                    </button>

                    <button
                        type="submit"
                        class="primary-button"
                    >
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

            window.showUploadSlipModal = function(orderId) {
                const modal = document.getElementById('uploadSlipModal');
                const form = document.getElementById('uploadSlipForm');
                if (modal && form) {
                    form.action = '{{ route('admin.sales.credit.slip.upload', ':id') }}'.replace(':id', orderId);
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };
        </script>
    @endPushOnce
</x-admin::layouts>
