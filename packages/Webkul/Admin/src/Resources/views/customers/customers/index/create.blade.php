@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-create-customer-form-template"
    >
        <x-admin::form
            v-slot="{ meta, errors, handleSubmit }"
            as="div"
        >
            <form @submit="handleSubmit($event, create)">
                <!-- Customer Create Modal -->
                <x-admin::modal ref="customerCreateModal">
                    <!-- Modal Header -->
                    <x-slot:header>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">
                            @lang('admin::app.customers.customers.index.create.title')
                        </p>
                    </x-slot>

                    <!-- Modal Content -->
                    <x-slot:content>
                        {!! view_render_event('bagisto.admin.customers.create.before') !!}

                        <!-- Customer Code -->
                        <x-admin::form.control-group class="mb-2.5 w-full">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.customers.customers.index.create.customer-code')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="customer_code"
                                name="customer_code"
                                :label="trans('admin::app.customers.customers.index.create.customer-code')"
                                :placeholder="trans('admin::app.customers.customers.index.create.customer-code')"
                            />

                            <x-admin::form.control-group.error control-name="customer_code" />
                        </x-admin::form.control-group>

                        <!-- Sales Code -->
                        <x-admin::form.control-group class="mb-2.5 w-full">
                            <x-admin::form.control-group.label for="create_sales_code_id">
                                @lang('admin::app.settings.users.index.create.sales-code')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="select"
                                id="create_sales_code_id"
                                name="sales_code_id"
                                :label="trans('admin::app.settings.users.index.create.sales-code')"
                            >
                                <option value="">@lang('admin::app.settings.users.index.create.sales-code-none')</option>

                                @foreach ($salesCodes as $salesCode)
                                    @php($salesName = $salesCode->admins->pluck('name')->filter()->unique()->implode(', ') ?: $salesCode->name)

                                    <option value="{{ $salesCode->id }}">
                                        {{ $salesName ? $salesCode->code.' - '.$salesName : $salesCode->code }}
                                    </option>
                                @endforeach
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error control-name="sales_code_id" />
                        </x-admin::form.control-group>

                        <div class="flex gap-4 max-sm:flex-wrap">
                            <!-- First Name -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.customers.customers.index.create.first-name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    rules="required"
                                    :label="trans('admin::app.customers.customers.index.create.first-name')"
                                    :placeholder="trans('admin::app.customers.customers.index.create.first-name')"
                                />

                                <x-admin::form.control-group.error control-name="first_name" />
                            </x-admin::form.control-group>

                            <!-- Last Name -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.customers.customers.index.create.last-name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    rules="required"
                                    :label="trans('admin::app.customers.customers.index.create.last-name')"
                                    :placeholder="trans('admin::app.customers.customers.index.create.last-name')"
                                />

                                <x-admin::form.control-group.error control-name="last_name" />
                            </x-admin::form.control-group>
                        </div>

                        <!-- Email -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.customers.customers.index.create.email')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="email"
                                id="email"
                                name="email"
                                rules="email"
                                :label="trans('admin::app.customers.customers.index.create.email')"
                                placeholder="email@example.com"
                            />

                            <x-admin::form.control-group.error control-name="email" />
                        </x-admin::form.control-group>

                        <!-- LINE ID -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.customers.customers.index.create.line-id')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="line_id"
                                name="line_id"
                                :label="trans('admin::app.customers.customers.index.create.line-id')"
                                :placeholder="trans('admin::app.customers.customers.index.create.line-id')"
                            />

                            <x-admin::form.control-group.error control-name="line_id" />
                        </x-admin::form.control-group>

                        <div class="flex gap-4 max-sm:flex-wrap">
                            <!-- Password -->
                            <x-admin::form.control-group class="w-full">
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.customers.customers.index.create.password')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="password"
                                    id="password"
                                    name="password"
                                    rules="min:6"
                                    :label="trans('admin::app.customers.customers.index.create.password')"
                                    :placeholder="trans('admin::app.customers.customers.index.create.password')"
                                    ref="password"
                                />

                                <x-admin::form.control-group.error control-name="password" />
                            </x-admin::form.control-group>

                            <!-- Confirm Password -->
                            <x-admin::form.control-group class="w-full">
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.customers.customers.index.create.confirm-password')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    rules="confirmed:@password"
                                    :label="trans('admin::app.customers.customers.index.create.confirm-password')"
                                    :placeholder="trans('admin::app.customers.customers.index.create.confirm-password')"
                                />

                                <x-admin::form.control-group.error control-name="password_confirmation" />
                            </x-admin::form.control-group>
                        </div>

                        <!-- Contact Number -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.customers.customers.index.create.contact-number')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="phone"
                                name="phone"
                                rules="phone"
                                :label="trans('admin::app.customers.customers.index.create.contact-number')"
                                :placeholder="trans('admin::app.customers.customers.index.create.contact-number')"
                            />

                            <x-admin::form.control-group.error control-name="phone" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.customers.customers.index.create.date-of-birth')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="date"
                                id="dob"
                                name="date_of_birth"
                                :label="trans('admin::app.customers.customers.index.create.date-of-birth')"
                                :placeholder="trans('admin::app.customers.customers.index.create.date-of-birth')"
                            />

                            <x-admin::form.control-group.error control-name="date_of_birth" />
                        </x-admin::form.control-group>

                        <!-- Gender -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.customers.customers.index.create.gender')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="select"
                                id="gender"
                                name="gender"
                                :label="trans('admin::app.customers.customers.index.create.gender')"
                            >
                                <option value="">
                                    @lang('admin::app.customers.customers.index.create.select-gender')
                                </option>

                                <option value="Male">
                                    @lang('admin::app.customers.customers.index.create.male')
                                </option>

                                <option value="Female">
                                    @lang('admin::app.customers.customers.index.create.female')
                                </option>

                                <option value="Other">
                                    @lang('admin::app.customers.customers.index.create.other')
                                </option>
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error control-name="gender" />
                        </x-admin::form.control-group>

                        <div class="flex gap-4 max-sm:flex-wrap">
                            <!-- Channel -->
                            <x-admin::form.control-group class="w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.customers.customers.index.create.channel')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="channel"
                                    name="channel_id"
                                    rules="required"
                                    :label="trans('admin::app.customers.customers.index.create.channel')"
                                    ::value="channels[0]?.id"
                                >
                                    <option 
                                        v-for="channel in channels" 
                                        :value="channel.id"
                                        selected
                                    > 
                                        @{{ channel.name }} (@{{ channel.code }})
                                    </option>
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="channel_id" />
                            </x-admin::form.control-group>

                            <!-- Customer Group -->
                            <x-admin::form.control-group class="w-full">
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.customers.customers.index.create.customer-group')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="customerGroup"
                                    name="customer_group_id"
                                    :label="trans('admin::app.customers.customers.index.create.customer-group')"
                                    ::value="groups[0]?.id"
                                >
                                    <option 
                                        v-for="group in groups" 
                                        :value="group.id"
                                        selected
                                    > 
                                        @{{ group.name }} 
                                    </option>
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="customer_group_id" />
                            </x-admin::form.control-group>
                        </div>

                        <!-- Address, saved together with the customer -->
                        <div class="mb-2.5 mt-6 border-t border-gray-200 pt-4 dark:border-gray-800">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.customers.customers.index.create.address-title')
                            </p>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">
                                @lang('admin::app.customers.customers.index.create.address-info')
                            </p>
                        </div>

                        <div class="flex gap-4 max-sm:flex-wrap">
                            <!-- Company Name -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.customers.customers.view.address.create.company-name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="company_name"
                                    name="company_name"
                                    :label="trans('admin::app.customers.customers.view.address.create.company-name')"
                                    :placeholder="trans('admin::app.customers.customers.view.address.create.company-name')"
                                />

                                <x-admin::form.control-group.error control-name="company_name" />
                            </x-admin::form.control-group>

                            <!-- Vat Id -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.customers.customers.view.address.create.vat-id')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="vat_id"
                                    name="vat_id"
                                    :label="trans('admin::app.customers.customers.view.address.create.vat-id')"
                                    :placeholder="trans('admin::app.customers.customers.view.address.create.vat-id')"
                                />

                                <x-admin::form.control-group.error control-name="vat_id" />
                            </x-admin::form.control-group>
                        </div>

                        <!-- Street Address -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.customers.customers.view.address.create.street-address')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="address[0]"
                                name="address[0]"
                                class="mb-2"
                                v-model="street"
                                :label="trans('admin::app.customers.customers.view.address.create.street-address')"
                                :placeholder="trans('admin::app.customers.customers.view.address.create.street-address')"
                            />

                            <x-admin::form.control-group.error
                                class="mb-2"
                                control-name="address[0]"
                            />

                            @if (core()->getConfigData('customer.address.information.street_lines') > 1)
                                @for ($i = 1; $i < core()->getConfigData('customer.address.information.street_lines'); $i++)
                                    <x-admin::form.control-group.control
                                        type="text"
                                        id="address[{{ $i }}]"
                                        name="address[{{ $i }}]"
                                        class="mb-2"
                                        :label="trans('admin::app.customers.customers.view.address.create.street-address')"
                                        :placeholder="trans('admin::app.customers.customers.view.address.create.street-address')"
                                    />

                                    <x-admin::form.control-group.error control-name="address[{{ $i }}]" />
                                @endfor
                            @endif
                        </x-admin::form.control-group>

                        <div class="flex gap-4 max-sm:flex-wrap">
                            <!-- City -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label ::class="hasStreet ? 'required' : ''">
                                    @lang('admin::app.customers.customers.view.address.create.city')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="city"
                                    name="city"
                                    ::rules="hasStreet ? 'required' : ''"
                                    :label="trans('admin::app.customers.customers.view.address.create.city')"
                                    :placeholder="trans('admin::app.customers.customers.view.address.create.city')"
                                />

                                <x-admin::form.control-group.error control-name="city" />
                            </x-admin::form.control-group>

                            <!-- PostCode -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label ::class="hasStreet ? 'required' : ''">
                                    @lang('admin::app.customers.customers.view.address.create.post-code')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="postcode"
                                    name="postcode"
                                    ::rules="hasStreet ? 'required|postcode' : ''"
                                    :label="trans('admin::app.customers.customers.view.address.create.post-code')"
                                    :placeholder="trans('admin::app.customers.customers.view.address.create.post-code')"
                                />

                                <x-admin::form.control-group.error control-name="postcode" />
                            </x-admin::form.control-group>
                        </div>

                        <div class="flex gap-4 max-sm:flex-wrap">
                            <!-- Country -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label ::class="hasStreet ? 'required' : ''">
                                    @lang('admin::app.customers.customers.view.address.create.country')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="country"
                                    name="country"
                                    v-model="country"
                                    ::rules="hasStreet ? 'required' : ''"
                                    :label="trans('admin::app.customers.customers.view.address.create.country')"
                                >
                                    <option value="">@lang('admin::app.customers.customers.view.address.create.select-country')</option>

                                    @foreach (core()->countries() as $country)
                                        <option value="{{ $country->code }}">{{ $country->name }}</option>
                                    @endforeach
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="country" />
                            </x-admin::form.control-group>

                            <!-- State -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label ::class="hasStreet ? 'required' : ''">
                                    @lang('admin::app.customers.customers.view.address.create.state')
                                </x-admin::form.control-group.label>

                                <template v-if="haveStates()">
                                    <x-admin::form.control-group.control
                                        type="select"
                                        id="state"
                                        name="state"
                                        ::rules="hasStreet ? 'required' : ''"
                                        :label="trans('admin::app.customers.customers.view.address.create.state')"
                                    >
                                        <option value="">@lang('admin::app.customers.customers.view.address.create.state')</option>

                                        <option
                                            v-for="state in countryStates[country]"
                                            :value="state.code"
                                        >
                                            @{{ state.default_name }}
                                        </option>
                                    </x-admin::form.control-group.control>
                                </template>

                                <template v-else>
                                    <x-admin::form.control-group.control
                                        type="text"
                                        id="state"
                                        name="state"
                                        ::rules="hasStreet ? 'required' : ''"
                                        :label="trans('admin::app.customers.customers.view.address.create.state')"
                                        :placeholder="trans('admin::app.customers.customers.view.address.create.state')"
                                    />
                                </template>

                                <x-admin::form.control-group.error control-name="state" />
                            </x-admin::form.control-group>
                        </div>

                        {!! view_render_event('bagisto.admin.customers.create.after') !!}
                    </x-slot>

                    <!-- Modal Footer -->
                    <x-slot:footer>
                        <!-- Save Button -->
                        <x-admin::button
                            button-type="submit"
                            class="primary-button justify-center"
                            :title="trans('admin::app.customers.customers.index.create.save-btn')"
                            ::loading="isLoading"
                            ::disabled="isLoading"
                        />
                    </x-slot>
                </x-admin::modal>
            </form>
        </x-admin::form>
    </script>

    <script type="module">
        app.component('v-create-customer-form', {
            template: '#v-create-customer-form-template',

            data() {
                return {
                    groups: @json($groups),

                    channels: @json($channels),

                    countryStates: @json(core()->groupedStatesByCountries()),

                    street: '',

                    country: '',

                    isLoading: false,
                };
            },

            computed: {
                /**
                 * The address is optional, so the rest of its fields only
                 * become required once a street has been entered.
                 */
                hasStreet() {
                    return !! this.street?.trim();
                },
            },

            methods: {
                openModal() {
                    this.$refs.customerCreateModal.open();
                },

                haveStates() {
                    return !! this.countryStates[this.country]?.length;
                },

                create(params, { resetForm, setErrors }) {
                    this.isLoading = true;

                    this.$axios.post("{{ route('admin.customers.customers.store') }}", params)
                        .then((response) => {
                            this.$refs.customerCreateModal.close();

                            this.$emit('customer-created', response.data.data);

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            resetForm();

                            this.street = '';

                            this.country = '';

                            this.isLoading = false;
                        })
                        .catch(error => {                            
                            this.isLoading = false;

                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            }
                        });
                }
            }
        })
    </script>
@endPushOnce