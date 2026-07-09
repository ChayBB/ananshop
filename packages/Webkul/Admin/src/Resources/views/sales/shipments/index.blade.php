<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.sales.shipments.index.title')
    </x-slot>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="py-3 text-xl font-bold text-gray-800 dark:text-white">
            @lang('admin::app.sales.shipments.index.title')
        </p>

        <div class="flex items-center gap-x-2.5">
            <!-- Export Modal -->
            <x-admin::datagrid.export :src="route('admin.sales.shipments.index')" />
        </div>
    </div>

    <x-admin::datagrid :src="route('admin.sales.shipments.index')">
        <template #header="{
            isLoading,
            available,
            applied,
            selectAll,
            sort,
            performAction
        }">
            <template v-if="isLoading">
                <x-admin::shimmer.datagrid.table.head />
            </template>

            <template v-else>
                <div
                    class="row grid min-h-[47px] items-center gap-2 border-b bg-gray-50 px-4 py-2 font-semibold text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                    style="grid-template-columns: 20px 88px 138px 68px 109px 132px 132px 105px 85px;"
                >
                    <template v-for="column in available.columns">
                        <p
                            class="flex items-center gap-1.5 overflow-hidden text-ellipsis whitespace-nowrap"
                            :class="{'cursor-pointer select-none hover:text-gray-800 dark:hover:text-white': column.sortable}"
                            @click="sort(column)"
                            v-if="column.visibility"
                        >
                            @{{ column.label }}

                            <i
                                class="align-text-bottom text-base text-gray-800 dark:text-white"
                                :class="[applied.sort.order === 'asc' ? 'icon-down-stat': 'icon-up-stat']"
                                v-if="column.index == applied.sort.column"
                            ></i>
                        </p>
                    </template>

                    <p
                        class="place-self-end whitespace-nowrap"
                        v-if="available.actions.length"
                    >
                        @lang('admin::app.components.datagrid.table.actions')
                    </p>
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
                <x-admin::shimmer.datagrid.table.body />
            </template>

            <template v-else>
                <div
                    v-for="record in available.records"
                    class="row grid items-center gap-2 border-b px-4 py-2.5 text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                    :class="{ 'cursor-pointer': record.actions.length }"
                    style="grid-template-columns: 20px 88px 138px 68px 109px 132px 132px 105px 85px;"
                    @click="record.actions.length ? performAction(record.actions[0]) : null"
                >
                    <template v-for="column in available.columns">
                        <p
                            class="overflow-hidden text-ellipsis whitespace-nowrap"
                            v-html="record[column.index]"
                            v-if="column.visibility"
                        >
                        </p>
                    </template>

                    <p
                        class="place-self-end whitespace-nowrap"
                        v-if="available.actions.length"
                    >
                        <span
                            class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800"
                            :class="action.icon"
                            v-text="! action.icon ? action.title : ''"
                            v-for="action in record.actions"
                        >
                        </span>
                    </p>
                </div>
            </template>
        </template>
    </x-admin::datagrid>

</x-admin::layouts>
