{!! view_render_event('bagisto.shop.layout.header.before') !!}

@if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1 )
    <div class="max-lg:hidden">
        <x-shop::layouts.header.desktop.top />
    </div>
@endif

<header class="shadow-gray sticky top-0 z-10 bg-white shadow-sm max-lg:shadow-none">
    <v-header-switcher>
        <!-- Desktop Header Shimmer -->
        <div class="flex flex-wrap max-lg:hidden">
            <div class="flex min-h-[78px] w-full justify-between border border-b border-l-0 border-r-0 border-t-0 px-[60px] max-1180:px-8">
                <!-- Left Navigation Section -->
                <div class="flex items-center gap-x-10 max-[1180px]:gap-x-5">
                    <!-- Logo Shimmer -->
                    <span
                        class="shimmer block h-[29px] w-[131px] rounded"
                        role="presentation"
                    >
                    </span>

                    <!-- Categories Shimmer -->
                    <div class="flex items-center gap-5">
                        <span
                            class="shimmer h-6 w-20 rounded"
                            role="presentation"
                        >
                        </span>

                        <span
                            class="shimmer h-6 w-20 rounded"
                            role="presentation"
                        >
                        </span>

                        <span
                            class="shimmer h-6 w-20 rounded"
                            role="presentation"
                        >
                        </span>
                    </div>
                </div>

                <!-- Right Navigation Section -->
                <div class="flex items-center gap-x-9 max-[1100px]:gap-x-6 max-lg:gap-x-8">
                    <!-- Search Bar Shimmer -->
                    <div class="relative w-full max-w-[445px]">
                        <span
                            class="shimmer block h-[42px] w-[250px] rounded-lg px-11 py-3"
                            role="presentation"
                        >
                        </span>
                    </div>

                    <!-- Right Navigation Icons Shimmer -->
                    <div class="mt-1.5 flex gap-x-8 max-[1100px]:gap-x-6 max-lg:gap-x-8">
                        <!-- Compare Icon Shimmer -->
                        <span
                            class="shimmer h-6 w-6 rounded"
                            role="presentation"
                        >
                        </span>

                        <!-- Cart Icon Shimmer -->
                        <span
                            class="shimmer h-6 w-6 rounded"
                            role="presentation"
                        >
                        </span>

                        <!-- Profile Icon Shimmer -->
                        <span
                            class="shimmer h-6 w-6 rounded"
                            role="presentation"
                        >
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Header Shimmer -->
        <div class="flex flex-wrap gap-4 px-4 pb-4 pt-6 shadow-sm lg:hidden">
            <div class="flex w-full items-center justify-between">
                <!-- Left Navigation -->
                <div class="flex items-center gap-x-1.5">
                    <!-- Hamburger Menu Shimmer -->
                    <span 
                        class="shimmer block h-6 w-6 rounded" 
                        role="presentation"
                    >
                    </span>
                    
                    <!-- Logo Shimmer -->
                    <span 
                        class="shimmer block h-[29px] w-[131px] rounded" 
                        role="presentation"
                    >
                    </span>
                </div>

                <!-- Right Navigation Icons -->
                <div class="flex items-center gap-x-5 max-md:gap-x-4">
                    <!-- Compare Icon Shimmer -->
                    <span 
                        class="shimmer block h-6 w-6 rounded" 
                        role="presentation"
                    >
                    </span>
                    
                    <!-- Cart Icon Shimmer -->
                    <span 
                        class="shimmer block h-6 w-6 rounded" 
                        role="presentation"
                    >
                    </span>
                    
                    <!-- Profile Icon Shimmer -->
                    <span 
                        class="shimmer block h-6 w-6 rounded" 
                        role="presentation"
                    >
                    </span>
                </div>
            </div>

            <!-- Search Bar Shimmer -->
            <div class="-mx-4 flex w-[calc(100%+2rem)] items-center px-2">
                <div class="relative w-full">
                    <span
                        class="shimmer block h-[42px] w-full rounded-xl px-11 py-3.5 max-md:rounded-lg"
                        role="presentation"
                    >
                    </span>
                </div>
            </div>
        </div>
    </v-header-switcher>
</header>

{!! view_render_event('bagisto.shop.layout.header.after') !!}

@pushOnce('scripts')
    <script 
        type="text/x-template" 
        id="v-header-switcher-template"
    >
        <v-desktop-header v-if="isDesktop"></v-desktop-header>
        
        <v-mobile-header v-else></v-mobile-header>
    </script>

    <script type="module">
        app.component('v-header-switcher', {
            template: '#v-header-switcher-template',

            data() {
                return {
                    isDesktop: window.innerWidth >= 1024
                }
            },

            mounted() {
                this.media = window.matchMedia('(min-width: 1024px)');

                this.media.addEventListener('change', this.handleMedia);
            },

            beforeUnmount() {
                this.media.removeEventListener('change', this.handleMedia);
            },

            methods: {
                handleMedia(e) {
                    this.isDesktop = e.matches;
                }
            }
        });

        app.component('v-desktop-header', {
            template: '#v-desktop-header-template'
        });

        app.component('v-mobile-header', {
            template: '#v-mobile-header-template'
        });

        app.component('v-search-bar', {
            template: '#v-search-bar-template',

            data() {
                return {
                    searchTerm: '{{ request("query") }}' || '',
                    results: [],
                    isOpen: false,
                    isSearching: false,
                    debounceTimer: null,
                };
            },

            mounted() {
                this.$nextTick(() => {
                    const input = this.$el.querySelector('input[name="query"]');

                    if (input) {
                        input.value = this.searchTerm;

                        input.addEventListener('input', (e) => {
                            this.searchTerm = e.target.value;
                            this.onInput();
                        });

                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                this.isOpen = false;
                            }
                        });

                        input.removeAttribute('required');
                    }
                });

                document.addEventListener('click', this.handleClickOutside);
            },

            beforeUnmount() {
                document.removeEventListener('click', this.handleClickOutside);

                if (this.debounceTimer) {
                    clearTimeout(this.debounceTimer);
                }
            },

            methods: {
                onInput() {
                    if (this.debounceTimer) {
                        clearTimeout(this.debounceTimer);
                    }

                    if (this.searchTerm.length < 2) {
                        this.results = [];
                        this.isOpen = false;
                        return;
                    }

                    this.debounceTimer = setTimeout(() => {
                        this.search();
                    }, 400);
                },

                search() {
                    this.isSearching = true;

                    this.$axios.get("{{ route('shop.api.products.index') }}", {
                        params: {
                            query: this.searchTerm,
                            limit: 5,
                        }
                    })
                    .then(response => {
                        this.results = response.data.data;
                        this.isOpen = true;
                        this.isSearching = false;
                    })
                    .catch(error => {
                        console.log(error);
                        this.isSearching = false;
                    });
                },

                productUrl(product) {
                    return "{{ route('shop.product_or_category.index', ':slug') }}".replace(':slug', product.url_key);
                },

                visit(url) {
                    this.isOpen = false;
                    window.location.href = url;
                },

                viewAll() {
                    this.isOpen = false;
                    window.location.href = "{{ route('shop.search.index') }}" + '?query=' + encodeURIComponent(this.searchTerm);
                },

                handleClickOutside(e) {
                    if (! this.$el.contains(e.target)) {
                        this.isOpen = false;
                    }
                },
            },
        });
    </script>

    <script
        type="text/x-template"
        id="v-search-bar-template"
    >
        <div class="relative">
            <slot></slot>

            <!-- Auto Search Dropdown -->
            <div
                class="absolute top-full z-[100] mt-1 w-full overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-lg"
                v-if="isOpen && (results.length || isSearching)"
            >
                <!-- Loading -->
                <div
                    class="flex items-center justify-center py-4 text-sm text-gray-500"
                    v-if="isSearching"
                >
                    <span class="icon-spinner animate-spin text-xl ltr:mr-2 rtl:ml-2"></span>
                    @lang('shop::app.components.layouts.header.desktop.bottom.search') ...
                </div>

                <!-- Results List -->
                <template v-else>
                    <div
                        class="flex cursor-pointer items-center gap-3 border-b border-zinc-100 px-4 py-2.5 transition-colors hover:bg-zinc-50 last:border-b-0"
                        v-for="product in results"
                        :key="product.id"
                        @click="visit(productUrl(product))"
                    >
                        <img
                            class="h-12 w-12 rounded-lg object-cover"
                            :src="product.base_image?.small_image_url"
                            :alt="product.name"
                            loading="lazy"
                        />

                        <div class="min-w-0 flex-1">
                            <p
                                class="truncate text-sm font-medium text-gray-900"
                                v-text="product.name"
                            ></p>

                            <p
                                class="text-xs text-gray-500"
                                v-html="product.price_html?.final?.formatted_price ?? product.price_html?.regular?.formatted_price ?? ''"
                            ></p>
                        </div>
                    </div>

                    <!-- View All Results Link -->
                    <div
                        class="cursor-pointer border-t border-zinc-200 bg-zinc-50 py-2.5 text-center text-xs font-semibold text-navyBlue hover:bg-zinc-100"
                        @click="viewAll"
                        v-if="results.length"
                    >
                        @lang('shop::app.categories.view.load-more') →
                    </div>
                </template>
            </div>
        </div>
    </script>

    <script 
        type="text/x-template" 
        id="v-desktop-header-template"
    >
        <x-shop::layouts.header.desktop />
    </script>

    <script 
        type="text/x-template" 
        id="v-mobile-header-template"
    >
        <x-shop::layouts.header.mobile />
    </script>
@endPushOnce
