<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.sales-codes.index.title')
    </x-slot>

    <v-sales-codes>
        <div class="flex items-center justify-between">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('admin::app.settings.sales-codes.index.title')
            </p>
        </div>

        <x-admin::shimmer.datagrid />
    </v-sales-codes>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-sales-codes-template"
        >
            <div class="flex items-center justify-between">
                <p class="text-xl font-bold text-gray-800 dark:text-white">
                    @lang('admin::app.settings.sales-codes.index.title')
                </p>

                <div class="flex items-center gap-x-2.5">
                    @if (bouncer()->hasPermission('settings.sales_codes.create'))
                        <button
                            type="button"
                            class="primary-button"
                            @click="resetForm(); $refs.salesCodeUpdateOrCreateModal.open()"
                        >
                            @lang('admin::app.settings.sales-codes.index.create.title')
                        </button>
                    @endif
                </div>
            </div>

            <x-admin::datagrid
                :src="route('admin.settings.sales_codes.index')"
                ref="datagrid"
            >
                @php
                    $hasPermission = bouncer()->hasPermission('settings.sales_codes.edit') || bouncer()->hasPermission('settings.sales_codes.delete');
                @endphp

                <template #body="{ isLoading, available, performAction }">
                    <template v-if="isLoading">
                        <x-admin::shimmer.datagrid.table.body />
                    </template>

                    <div
                        v-else
                        v-for="record in available.records"
                        class="row grid items-center gap-2.5 border-b px-4 py-4 text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                        style="grid-template-columns: repeat({{ $hasPermission ? '5' : '4' }}, minmax(150px, 1fr));"
                    >
                        <p>@{{ record.id }}</p>

                        <p class="break-all">@{{ record.code }}</p>

                        <p class="break-words">@{{ record.name }}</p>

                        <p>@{{ record.status }}</p>

                        @if ($hasPermission)
                            <div class="flex justify-end">
                                <a @click="editModal(record.actions.find(action => action.icon === 'icon-edit')?.url)">
                                    <span
                                        :class="record.actions.find(action => action.icon === 'icon-edit')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                    >
                                    </span>
                                </a>

                                <a @click="performAction(record.actions.find(action => action.icon === 'icon-delete'))">
                                    <span
                                        :class="record.actions.find(action => action.icon === 'icon-delete')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                    >
                                    </span>
                                </a>
                            </div>
                        @endif
                    </div>
                </template>
            </x-admin::datagrid>

            <!-- Modal Form -->
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form
                    @submit="handleSubmit($event, updateOrCreate)"
                    ref="salesCodeCreateForm"
                >
                    <x-admin::modal ref="salesCodeUpdateOrCreateModal">
                        <x-slot:header>
                            <p
                                class="text-lg font-bold text-gray-800 dark:text-white"
                                v-if="isUpdating"
                            >
                                @lang('admin::app.settings.sales-codes.index.edit.title')
                            </p>

                            <p
                                class="text-lg font-bold text-gray-800 dark:text-white"
                                v-else
                            >
                                @lang('admin::app.settings.sales-codes.index.create.title')
                            </p>
                        </x-slot>

                        <x-slot:content>
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                                v-model="data.sales_code.id"
                            />

                            <!-- Code -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.sales-codes.index.create.code')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="code"
                                    rules="required"
                                    v-model="data.sales_code.code"
                                    :label="trans('admin::app.settings.sales-codes.index.create.code')"
                                    :placeholder="trans('admin::app.settings.sales-codes.index.create.code')"
                                />

                                <x-admin::form.control-group.error control-name="code" />
                            </x-admin::form.control-group>

                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.sales-codes.index.create.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                    v-model="data.sales_code.name"
                                    :label="trans('admin::app.settings.sales-codes.index.create.name')"
                                    :placeholder="trans('admin::app.settings.sales-codes.index.create.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            <!-- Status -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.sales-codes.index.create.status')
                                </x-admin::form.control-group.label>

                                <div class="mt-2.5 w-full gap-2.5">
                                    <x-admin::form.control-group.control
                                        type="switch"
                                        name="status"
                                        :value="1"
                                        v-model="data.sales_code.status"
                                        :label="trans('admin::app.settings.sales-codes.index.create.status')"
                                        ::checked="data.sales_code.status"
                                    />

                                    <x-admin::form.control-group.error control-name="status" />
                                </div>
                            </x-admin::form.control-group>
                        </x-slot>

                        <x-slot:footer>
                            <x-admin::button
                                button-type="submit"
                                class="primary-button justify-center"
                                :title="trans('admin::app.settings.sales-codes.index.create.save-btn')"
                                ::loading="isLoading"
                                ::disabled="isLoading"
                            />
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-sales-codes', {
                template: '#v-sales-codes-template',

                data() {
                    return {
                        isUpdating: false,

                        isLoading: false,

                        data: {
                            sales_code: {},
                        },
                    }
                },

                methods: {
                    updateOrCreate(params, { setErrors }) {
                        this.isLoading = true;

                        let formData = new FormData(this.$refs.salesCodeCreateForm);

                        if (params.id) {
                            formData.append('_method', 'put');
                        }

                        this.$axios.post(params.id ? "{{ route('admin.settings.sales_codes.update') }}" : "{{ route('admin.settings.sales_codes.store') }}", formData)
                            .then((response) => {
                                this.isLoading = false;

                                this.$refs.salesCodeUpdateOrCreateModal.close();

                                this.$refs.datagrid.get();

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                this.resetForm();
                            })
                            .catch(error => {
                                this.isLoading = false;

                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                }
                            });
                    },

                    editModal(url) {
                        this.isUpdating = true;

                        this.$axios.get(url)
                            .then((response) => {
                                this.data = response.data;

                                this.$refs.salesCodeUpdateOrCreateModal.open();
                            })
                            .catch(error => {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            });
                    },

                    resetForm() {
                        this.isUpdating = false;

                        this.data = {
                            sales_code: {
                                status: true,
                            },
                        };

                        this.$refs.modalForm.resetForm();
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
