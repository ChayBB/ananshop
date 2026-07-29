<!-- Stock Threshold Products Vue Component -->
<v-reporting-stock-threshold-products>
    <!-- Shimmer -->
    <x-admin::shimmer.reporting.stock.threshold-products />
</v-reporting-stock-threshold-products>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-reporting-stock-threshold-products-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.reporting.stock.threshold-products />
        </template>

        <!-- Stock Threshold Products Section -->
        <template v-else>
            <div class="box-shadow relative flex-1 rounded bg-white p-4 dark:bg-gray-900">
                <!-- Header -->
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-base font-semibold text-gray-600 dark:text-white">
                        @lang('admin::app.reporting.stock.index.stock-threshold-products')
                    </p>

                    <a
                        href="{{ route('admin.reporting.stock.view', ['type' => 'stock-threshold-products']) }}"
                        class="cursor-pointer text-sm text-blue-600 transition-all hover:underline"
                    >
                        @lang('admin::app.reporting.products.index.view-details')
                    </a>
                </div>

                <!-- Content -->
                <div
                    class="box-shadow rounded"
                    v-if="report.statistics.length"
                >
                    <!-- Single Product -->
                    <div
                        class="relative"
                        v-for="product in report.statistics"
                    >
                        <div class="row grid grid-cols-2 gap-y-6 border-b bg-white p-4 transition-all hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-950 max-sm:grid-cols-[1fr_auto]">
                            <div class="flex gap-2.5">
                                <template v-if="product.image">
                                    <div class="">
                                        <img
                                            class="max-h-[65px] min-h-[65px] min-w-[65px] max-w-[65px] rounded"
                                            :src="product.image"
                                        >
                                    </div>
                                </template>

                                <template v-else>
                                    <div class="relative h-[65px] max-h-[65px] w-full max-w-[65px] overflow-hidden rounded border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert">
                                        <img src="{{ bagisto_asset('images/product-placeholders/front.svg')}}">

                                        <p class="absolute bottom-1.5 w-full text-center text-[6px] font-semibold text-gray-400">
                                            @lang('admin::app.dashboard.index.product-image')
                                        </p>
                                    </div>
                                </template>

                                <div class="flex flex-col gap-1.5">
                                    <!-- Product Name -->
                                    <p class="text-base font-semibold text-gray-800 dark:text-white">
                                        @{{ product.name }}
                                    </p>

                                    <!-- Product SKU -->
                                    <p class="text-gray-600 dark:text-gray-300">
                                        @{{ "@lang('admin::app.dashboard.index.sku', ['sku' => ':replace'])".replace(':replace', product.sku) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-1.5">
                                <div class="flex flex-col gap-1.5">
                                    <!-- Product Price -->
                                    <p class="text-base font-semibold text-gray-800 dark:text-white">
                                        @{{ product.formatted_price }}
                                    </p>

                                    <!-- Total Product Stock -->
                                    <p :class="[product.total_qty > {{ core()->getConfigData('catalog.inventory.stock_options.out_of_stock_threshold') }} ? 'text-emerald-500' : 'text-red-500']">
                                        @{{ "@lang('admin::app.dashboard.index.total-stock', ['total_stock' => ':replace'])".replace(':replace', product.total_qty) }}
                                    </p>
                                </div>

                                <!-- View More Icon -->
                                <a :href="'{{ route('admin.catalog.products.edit', ':replace') }}'.replace(':replace', product.id)">
                                    <span class="icon-sort-right rtl:icon-sort-left cursor-pointer p-1.5 text-2xl hover:rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 ltr:ml-1 rtl:mr-1"></span>
                                </a>
                            </div>

                            @php
                                $lowStockThreshold = (float) (core()->getConfigData('catalog.inventory.stock_options.low_stock_threshold') ?: 20);
                                $mediumStockThreshold = (float) (core()->getConfigData('catalog.inventory.stock_options.medium_stock_threshold') ?: 30);
                                $gaugeMax = max($mediumStockThreshold * 2, $lowStockThreshold + 1);
                                $redWidth = min($lowStockThreshold / $gaugeMax * 100, 100);
                                $yellowWidth = max(min(($mediumStockThreshold - $lowStockThreshold) / $gaugeMax * 100, 100), 0);
                                $greenWidth = max(100 - $redWidth - $yellowWidth, 0);
                            @endphp

                            <!-- Stock Level Gauge: red 0-{{ $lowStockThreshold }}, yellow {{ $lowStockThreshold }}-{{ $mediumStockThreshold }}, green {{ $mediumStockThreshold }}+ (thresholds configurable in Catalog > Inventory) -->
                            <div class="col-span-2 max-sm:col-span-1">
                                <div class="relative h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div class="absolute inset-y-0 left-0 bg-red-400" style="width: {{ $redWidth }}%"></div>

                                    <div class="absolute inset-y-0 bg-amber-400" style="left: {{ $redWidth }}%; width: {{ $yellowWidth }}%"></div>

                                    <div class="absolute inset-y-0 bg-emerald-400" style="left: {{ $redWidth + $yellowWidth }}%; width: {{ $greenWidth }}%"></div>

                                    <div
                                        class="absolute top-1/2 h-3 w-3 -translate-y-1/2 rounded-full border-2 border-white bg-gray-800 shadow dark:border-gray-900"
                                        :style="{ left: 'calc(' + Math.min(product.total_qty, {{ $gaugeMax }}) / {{ $gaugeMax }} * 100 + '% - 6px)' }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <template v-else>
                    @include('admin::reporting.empty')
                </template>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-reporting-stock-threshold-products', {
            template: '#v-reporting-stock-threshold-products-template',

            data() {
                return {
                    report: [],

                    isLoading: true,
                }
            },

            mounted() {
                this.getStats({});
            },

            methods: {
                getStats(filters) {
                    this.isLoading = true;

                    var filters = Object.assign({}, filters);

                    filters.type = 'stock-threshold-products';

                    this.$axios.get("{{ route('admin.reporting.stock.stats') }}", {
                            params: filters
                        })
                        .then(response => {
                            this.report = response.data;

                            this.isLoading = false;
                        })
                        .catch(error => {});
                }
            }
        });
    </script>
@endPushOnce
