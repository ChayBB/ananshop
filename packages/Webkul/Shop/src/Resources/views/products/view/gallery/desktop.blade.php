<!-- For large screens greater than 1180px. -->
<div class="sticky top-20 flex flex-col h-max gap-4 max-sm:hidden max-w-[400px]">
    <!-- Product Base Image and Video with Shimmer-->
    <div class="relative max-h-[440px] max-w-[400px]">
        <div
            v-show="isMediaLoading"
        >
            <div class="shimmer min-h-[440px] min-w-[400px] rounded-xl bg-zinc-200"></div>
        </div>

        <div
            v-show="! isMediaLoading"
        >
            <img
                class="w-full cursor-pointer rounded-xl"
                :src="baseFile.path"
                v-if="baseFile.type == 'image'"
                alt="{{ $product->name }}"
                width="400"
                height="440"
                tabindex="0"
                @click="isImageZooming = !isImageZooming"
                @load="onMediaLoad()"
                fetchpriority="high"
            />

            <div
                class="w-full cursor-pointer rounded-xl"
                tabindex="0"
                v-if="baseFile.type == 'video'"
            >
                <video
                    controls
                    width="475"
                    alt="{{ $product->name }}"
                    @click="isImageZooming = !isImageZooming"
                    @loadeddata="onMediaLoad()"
                    :key="baseFile.path"
                >
                    <source
                        :src="baseFile.path"
                        type="video/mp4"
                    />
                </video>
            </div>
        </div>

        @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
            <div
                class="absolute top-3 flex h-11 w-11 cursor-pointer items-center justify-center rounded-full border bg-white text-2xl transition-all hover:opacity-[0.8] ltr:right-3 rtl:left-3"
                role="button"
                aria-label="@lang('shop::app.products.view.add-to-wishlist')"
                tabindex="0"
                :class="isWishlist ? 'icon-heart-fill text-red-600' : 'icon-heart'"
                @click="$emit('toggle-wishlist')"
            >
            </div>
        @endif
    </div>

    <!-- Product Image and Videos Slider (thumbnails at the bottom) -->
    <div class="flex h-auto w-full min-h-[100px] max-h-[100px] flex-nowrap items-center justify-center gap-2.5 overflow-x-auto overflow-y-hidden">
        <!-- Arrow Left -->
        <span
            class="icon-arrow-left cursor-pointer text-2xl"
            role="button"
            aria-label="@lang('shop::app.components.products.carousel.previous')"
            tabindex="0"
            @click="swipeLeft"
            v-if="lengthOfMedia"
        >
        </span>

        <!-- Swiper Container -->
        <div
            ref="swiperContainer"
            class="flex flex-row w-full gap-2.5 justify-center overflow-auto scroll-smooth scrollbar-hide"
        >
            <template v-for="(media, index) in [...media.images, ...media.videos]">
                <video
                    v-if="media.type == 'videos'"
                    :class="`transparent max-h-[100px] min-w-[100px] cursor-pointer rounded-xl border-2 ${isActiveMedia(index) ? 'pointer-events-none border-emerald-500' : 'border-zinc-200'}`"
                    @click="change(media, index)"
                    alt="{{ $product->name }}"
                    tabindex="0"
                >
                    <source
                        :src="media.video_url"
                        type="video/mp4"
                    />
                </video>

                <img
                    v-else
                    :class="`transparent max-h-[100px] min-w-[100px] cursor-pointer rounded-xl border-2 ${isActiveMedia(index) ? 'pointer-events-none border-emerald-500' : 'border-zinc-200'}`"
                    :src="media.small_image_url"
                    alt="{{ $product->name }}"
                    width="100"
                    height="100"
                    tabindex="0"
                    @click="change(media, index)"
                />
            </template>
        </div>

        <!-- Arrow Right -->
        <span
            class="icon-arrow-right cursor-pointer text-2xl"
            v-if= "lengthOfMedia"
            role="button"
            aria-label="@lang('shop::app.components.products.carousel.next')"
            tabindex="0"
            @click="swipeRight"
        >
        </span>
    </div>
</div>
