@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-import-customers-template"
    >
        <div>
            <!-- Import Button -->
            <button
                type="button"
                class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                @click="$refs.customerImportModal.open()"
            >
                <span class="icon-arrow-down text-xl text-gray-600 dark:text-white"></span>

                @lang('admin::app.customers.customers.index.import.import-btn')
            </button>

            <!-- Import Modal -->
            <x-admin::modal ref="customerImportModal">
                <x-slot:header>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('admin::app.customers.customers.index.import.title')
                    </p>
                </x-slot>

                <x-slot:content>
                    <p class="mb-4 text-xs text-gray-600 dark:text-gray-300">
                        @lang('admin::app.customers.customers.index.import.info')
                    </p>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.customers.customers.index.import.file')
                        </x-admin::form.control-group.label>

                        <input
                            type="file"
                            ref="file"
                            accept=".xlsx,.xls,.csv"
                            class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            @change="fileError = ''"
                        >

                        <p
                            class="mt-1 text-xs text-red-600"
                            v-if="fileError"
                            v-text="fileError"
                        ></p>
                    </x-admin::form.control-group>

                    <!-- Outcome of the last run -->
                    <div
                        class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800"
                        v-if="result"
                    >
                        <p class="font-semibold text-gray-800 dark:text-white">
                            @{{ summary }}
                        </p>

                        <div
                            class="mt-2.5 max-h-60 overflow-auto"
                            v-if="Object.keys(result.errors).length"
                        >
                            <p class="mb-1.5 text-xs font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.customers.customers.index.import.errors-title')
                            </p>

                            <p
                                class="text-xs text-red-600"
                                v-for="(messages, row) in result.errors"
                            >
                                @{{ rowLabel(row) }} @{{ messages.join(' / ') }}
                            </p>
                        </div>
                    </div>
                </x-slot>

                <x-slot:footer>
                    <x-admin::button
                        button-type="button"
                        class="primary-button justify-center"
                        :title="trans('admin::app.customers.customers.index.import.submit-btn')"
                        ::loading="isLoading"
                        ::disabled="isLoading"
                        @click="submit"
                    />
                </x-slot>
            </x-admin::modal>
        </div>
    </script>

    <script type="module">
        app.component('v-import-customers', {
            template: '#v-import-customers-template',

            emits: ['customers-imported'],

            data() {
                return {
                    isLoading: false,

                    fileError: '',

                    result: null,
                };
            },

            computed: {
                summary() {
                    if (! this.result) {
                        return '';
                    }

                    return "@lang('admin::app.customers.customers.index.import.import-success')"
                        .replace(':created', this.result.created)
                        .replace(':updated', this.result.updated)
                        .replace(':skipped', Object.keys(this.result.errors).length);
                },
            },

            methods: {
                rowLabel(row) {
                    /**
                     * A heading problem is reported against row zero, since it
                     * belongs to the file rather than to one of its rows.
                     */
                    if (Number(row) === 0) {
                        return '';
                    }

                    return "@lang('admin::app.customers.customers.index.import.row')".replace(':row', row);
                },

                submit() {
                    const file = this.$refs.file.files[0];

                    if (! file) {
                        this.fileError = "@lang('admin::app.customers.customers.index.import.file-required')";

                        return;
                    }

                    const formData = new FormData();

                    formData.append('file', file);

                    this.isLoading = true;

                    this.result = null;

                    this.$axios.post("{{ route('admin.customers.customers.import') }}", formData, {
                            headers: { 'Content-Type': 'multipart/form-data' },
                        })
                        .then((response) => {
                            this.result = response.data.data;

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$emit('customers-imported');

                            this.$refs.file.value = '';

                            this.isLoading = false;
                        })
                        .catch(error => {
                            this.isLoading = false;

                            if (error.response?.status == 422) {
                                this.fileError = Object.values(error.response.data.errors).flat().join(' ');
                            }
                        });
                },
            },
        });
    </script>
@endPushOnce
