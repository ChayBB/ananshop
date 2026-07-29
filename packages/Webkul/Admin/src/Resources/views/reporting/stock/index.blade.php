<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.reporting.stock.index.control-center')
    </x-slot>

    <!-- Page Header -->
    <div class="mb-5 flex items-center justify-between gap-4 max-sm:flex-wrap">
        <div class="grid gap-1.5">
            <p class="pt-1.5 text-xl font-bold leading-6 text-gray-800 dark:text-white">
                @lang('admin::app.reporting.stock.index.control-center')
            </p>
        </div>

        <a
            href="{{ route('admin.reporting.stock.view', ['type' => 'stock-threshold-products']) }}"
            class="primary-button"
        >
            @lang('admin::app.reporting.stock.index.view-full-report')
        </a>
    </div>

    <v-stock-control-center>
        <div class="flex flex-col gap-4">
            <div class="shimmer h-[92px] rounded"></div>

            <div class="shimmer h-[280px] rounded"></div>
        </div>
    </v-stock-control-center>

    @pushOnce('scripts')
        <script
            type="module"
            src="{{ bagisto_asset('js/chart.js') }}"
        >
        </script>

        <script
            type="text/x-template"
            id="v-stock-control-center-template"
        >
            <div>
                <!-- Shimmer -->
                <template v-if="isLoading">
                    <div class="flex flex-col gap-4">
                        <div class="shimmer h-[92px] rounded"></div>

                        <div class="shimmer h-[280px] rounded"></div>
                    </div>
                </template>

                <template v-else>
                    <!-- Summary Counters -->
                    <div class="mb-4 grid grid-cols-4 gap-4 max-md:grid-cols-2">
                        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                            <p class="text-2xl font-bold text-red-500">
                                @{{ report.summary.critical }}
                            </p>

                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @lang('admin::app.reporting.stock.index.critical')
                            </p>
                        </div>

                        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                            <p class="text-2xl font-bold text-amber-500">
                                @{{ report.summary.low }}
                            </p>

                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @lang('admin::app.reporting.stock.index.low')
                            </p>
                        </div>

                        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                            <p class="text-2xl font-bold text-emerald-500">
                                @{{ report.summary.healthy }}
                            </p>

                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @lang('admin::app.reporting.stock.index.healthy')
                            </p>
                        </div>

                        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                @{{ report.total }}
                            </p>

                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @lang('admin::app.reporting.stock.index.total-tracked')
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4 max-lg:flex-wrap [&>*]:flex-1">
                        <!-- Stock Health Chart -->
                        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                            <p class="mb-4 text-base font-semibold text-gray-600 dark:text-white">
                                @lang('admin::app.reporting.stock.index.stock-health')
                            </p>

                            <x-admin::charts.bar
                                ::labels="report.chart.labels"
                                ::datasets="report.chart.datasets"
                                :aspect-ratio="1.9"
                            />
                        </div>

                        <!-- Critical Alerts -->
                        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                            <p class="mb-4 text-base font-semibold text-gray-600 dark:text-white">
                                @lang('admin::app.reporting.stock.index.critical-alerts')
                            </p>

                            <div
                                class="grid gap-3"
                                v-if="report.critical_products.length"
                            >
                                <a
                                    class="flex items-center justify-between gap-4 rounded border border-gray-100 p-2.5 transition-all hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950"
                                    v-for="product in report.critical_products"
                                    :href="'{{ route('admin.catalog.products.edit', ':replace') }}'.replace(':replace', product.id)"
                                >
                                    <div class="grid gap-0.5">
                                        <p class="font-semibold text-gray-800 dark:text-white">
                                            @{{ product.name }}
                                        </p>

                                        <p class="text-xs text-gray-500 dark:text-gray-300">
                                            @{{ product.sku }}
                                        </p>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-2.5">
                                        <span class="relative block h-1.5 w-[40px] overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                            <span
                                                class="absolute inset-y-0 left-0 rounded-full bg-red-400"
                                                :style="{ width: Math.max(product.percentage, 3) + '%' }"
                                            ></span>
                                        </span>

                                        <p class="text-sm font-semibold text-red-500">
                                            @{{ product.quantity }} / @{{ product.max_stock_level }}
                                        </p>
                                    </div>
                                </a>
                            </div>

                            <p
                                class="py-6 text-center text-sm text-gray-400"
                                v-else
                            >
                                @lang('admin::app.reporting.stock.index.no-critical')
                            </p>
                        </div>
                    </div>
                </template>
            </div>
        </script>

        <script type="module">
            app.component('v-stock-control-center', {
                template: '#v-stock-control-center-template',

                data() {
                    return {
                        report: [],

                        isLoading: true,
                    }
                },

                mounted() {
                    this.getStats();
                },

                methods: {
                    getStats() {
                        this.isLoading = true;

                        this.$axios.get("{{ route('admin.reporting.stock.stats') }}", {
                                params: { type: 'stock-control-center' }
                            })
                            .then(response => {
                                this.report = response.data.statistics;

                                this.isLoading = false;
                            })
                            .catch(error => {});
                    }
                }
            });
        </script>
    @endPushOnce
</x-admin::layouts>
