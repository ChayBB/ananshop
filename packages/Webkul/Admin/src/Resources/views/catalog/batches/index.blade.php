<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.catalog.batches.index.title')
    </x-slot>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="text-xl font-bold leading-6 text-gray-800 dark:text-white">
            @lang('admin::app.catalog.batches.index.title')
        </p>

        @if (bouncer()->hasPermission('catalog.batches'))
            <a
                href="{{ route('admin.catalog.batches.create') }}"
                class="primary-button"
            >
                @lang('admin::app.catalog.batches.index.receive')
            </a>
        @endif
    </div>

    <x-admin::datagrid :src="route('admin.catalog.batches.index')" />
</x-admin::layouts>
