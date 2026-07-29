<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.catalog.batches.create.receive')
    </x-slot>

    <x-admin::form :action="route('admin.catalog.batches.store')">
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="text-xl font-bold leading-6 text-gray-800 dark:text-white">
                @lang('admin::app.catalog.batches.create.receive')
            </p>

            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ route('admin.catalog.batches.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('admin::app.catalog.batches.create.back')
                </a>

                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('admin::app.catalog.batches.create.save')
                </button>
            </div>
        </div>

        <div class="mt-3.5 box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <div class="grid grid-cols-2 gap-4 max-md:grid-cols-1">
                <!-- Product -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">
                        @lang('admin::app.catalog.batches.create.product')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="select"
                        name="product_id"
                        rules="required"
                        :value="old('product_id')"
                        :label="trans('admin::app.catalog.batches.create.product')"
                    >
                        <option value=""></option>

                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->sku }} — {{ $product->name }}
                            </option>
                        @endforeach
                    </x-admin::form.control-group.control>

                    <x-admin::form.control-group.error control-name="product_id" />
                </x-admin::form.control-group>

                <!-- Inventory Source -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">
                        @lang('admin::app.catalog.batches.create.source')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="select"
                        name="inventory_source_id"
                        rules="required"
                        :value="old('inventory_source_id')"
                        :label="trans('admin::app.catalog.batches.create.source')"
                    >
                        @foreach ($inventorySources as $source)
                            <option value="{{ $source->id }}">
                                {{ $source->name }}
                            </option>
                        @endforeach
                    </x-admin::form.control-group.control>

                    <x-admin::form.control-group.error control-name="inventory_source_id" />
                </x-admin::form.control-group>

                <!-- Quantity -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">
                        @lang('admin::app.catalog.batches.create.qty')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="text"
                        name="qty"
                        rules="required|numeric|min:1"
                        :value="old('qty')"
                        :label="trans('admin::app.catalog.batches.create.qty')"
                    />

                    <x-admin::form.control-group.error control-name="qty" />
                </x-admin::form.control-group>

                <!-- Unit Cost -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        @lang('admin::app.catalog.batches.create.unit-cost')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="text"
                        name="unit_cost"
                        rules="numeric|min:0"
                        :value="old('unit_cost')"
                        :label="trans('admin::app.catalog.batches.create.unit-cost')"
                    />

                    <x-admin::form.control-group.error control-name="unit_cost" />
                </x-admin::form.control-group>

                <!-- Received At -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">
                        @lang('admin::app.catalog.batches.create.received-at')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="date"
                        name="received_at"
                        rules="required"
                        :value="old('received_at', now()->format('Y-m-d'))"
                        :label="trans('admin::app.catalog.batches.create.received-at')"
                    />

                    <x-admin::form.control-group.error control-name="received_at" />
                </x-admin::form.control-group>

                <!-- Expiry -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        @lang('admin::app.catalog.batches.create.expired-at')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="date"
                        name="expired_at"
                        :value="old('expired_at')"
                        :label="trans('admin::app.catalog.batches.create.expired-at')"
                    />

                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">
                        @lang('admin::app.catalog.batches.create.expiry-hint')
                    </p>

                    <x-admin::form.control-group.error control-name="expired_at" />
                </x-admin::form.control-group>

                <!-- Batch Number -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        @lang('admin::app.catalog.batches.create.batch-number')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="text"
                        name="batch_number"
                        :value="old('batch_number')"
                        :label="trans('admin::app.catalog.batches.create.batch-number')"
                    />

                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">
                        @lang('admin::app.catalog.batches.create.batch-hint')
                    </p>

                    <x-admin::form.control-group.error control-name="batch_number" />
                </x-admin::form.control-group>

                <!-- Notes -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        @lang('admin::app.catalog.batches.create.notes')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="textarea"
                        name="notes"
                        :value="old('notes')"
                        :label="trans('admin::app.catalog.batches.create.notes')"
                    />

                    <x-admin::form.control-group.error control-name="notes" />
                </x-admin::form.control-group>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>
