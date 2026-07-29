<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.reporting.stock.index.title')
    </x-slot>

    <!-- Page Header -->
    <div class="mb-5 flex items-center justify-between gap-4 max-sm:flex-wrap">
        <div class="grid gap-1.5">
            <p class="pt-1.5 text-xl font-bold leading-6 text-gray-800 dark:text-white">
                @lang('admin::app.reporting.stock.index.title')
            </p>
        </div>
    </div>

    <!-- Stock Stats Vue Component -->
    <div class="flex flex-1 flex-col gap-4 max-xl:flex-auto">
        <!-- Stock Threshold Products Section -->
        <div class="flex flex-col justify-between gap-4 flex-1 [&>*]:flex-1 md:flex-row">
            @include('admin::reporting.stock.threshold-products')
        </div>
    </div>
</x-admin::layouts>
