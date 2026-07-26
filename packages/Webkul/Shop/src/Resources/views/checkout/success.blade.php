<x-shop::layouts
	:has-header="true"
	:has-feature="false"
	:has-footer="true"
>
    <!-- Page Title -->
    <x-slot:title>
		@lang('shop::app.checkout.success.thanks')
    </x-slot>

	<!-- Page content -->
	<div class="container mt-8 px-[60px] max-lg:px-8">
		<div class="grid place-items-center gap-y-5 max-md:gap-y-2.5">
			{{ view_render_event('bagisto.shop.checkout.success.image.before', ['order' => $order]) }}

			<img
				class="max-md:h-[100px] max-md:w-[100px]"
				src="{{ bagisto_asset('images/thank-you.png') }}"
				alt="@lang('shop::app.checkout.success.thanks')"
				title="@lang('shop::app.checkout.success.thanks')"
                loading="lazy"
                decoding="async"
			>

			{{ view_render_event('bagisto.shop.checkout.success.image.after', ['order' => $order]) }}

			<p class="text-xl max-md:text-sm">
				@if (auth()->guard('customer')->user())
					@lang('shop::app.checkout.success.order-id-info', [
						'order_id' => '<a class="text-blue-700" href="'.route('shop.customers.account.orders.view', $order->id).'">'.$order->increment_id.'</a>'
					])
				@else
					@lang('shop::app.checkout.success.order-id-info', ['order_id' => $order->increment_id])
				@endif
			</p>

			@if (
				$order->payment->method === 'moneytransfer'
				&& ($qrCodePath = core()->getConfigData('sales.payment_methods.moneytransfer.qr_code'))
			)
				<div class="flex flex-col items-center gap-2">
					<p class="text-base font-medium">
						คิวอาร์โค๊ดสำหรับโอนเงิน
					</p>

					<img
						src="{{ \Illuminate\Support\Facades\Storage::url($qrCodePath) }}"
						alt="คิวอาร์โค๊ดสำหรับโอนเงิน"
						class="h-48 w-48 rounded-lg border border-zinc-200 object-contain"
					>

					<a
						href="{{ \Illuminate\Support\Facades\Storage::url($qrCodePath) }}"
						download
						class="text-sm font-medium text-navyBlue underline"
					>
						ดาวน์โหลด QR Code
					</a>
				</div>
			@endif

			<p class="text-xl max-md:text-sm">
				<span class="font-bold">
					@lang('shop::app.checkout.onepage.summary.grand-total')
				</span>

				{{ core()->formatPrice($order->grand_total, $order->order_currency_code) }}
			</p>

			<p class="font-medium md:text-2xl">
				@lang('shop::app.checkout.success.thanks')
			</p>

			<p class="text-xl text-zinc-500 max-md:text-center max-md:text-xs">
				@if (! empty($order->checkout_message))
					{!! nl2br($order->checkout_message) !!}
				@else
					@lang('shop::app.checkout.success.info')
				@endif
			</p>

			{{ view_render_event('bagisto.shop.checkout.success.continue-shopping.before', ['order' => $order]) }}

			<div class="flex flex-wrap items-center justify-center gap-3">
				@if ($order->payment->method === 'cashondelivery')
					@if (auth()->guard('customer')->user())
						<a href="{{ route('shop.customers.account.orders.view', $order->id) }}">
							<div class="w-max cursor-pointer rounded-2xl border-2 border-navyBlue bg-white px-11 py-3 text-center text-base font-medium text-navyBlue max-md:rounded-lg max-md:px-6 max-md:py-1.5">
								ดูคำสั่งซื้อของคุณ
							</div>
						</a>
					@endif
				@elseif ($order->payment->method === 'credit')
					<div class="w-max rounded-2xl border-2 border-navyBlue bg-white px-11 py-3 text-center max-md:rounded-lg max-md:px-6 max-md:py-1.5">
						<p class="text-base font-medium text-navyBlue">
							ขอบคุณที่ใช้บริการเครดิตของเรา
						</p>

						@if (! is_null($creditRoundsRemaining))
							<p class="text-sm text-zinc-500">
								จำนวนเครดิตที่เหลือ: {{ $creditRoundsRemaining }} รอบ
							</p>
						@endif
					</div>
				@else
					<!-- Upload Slip Button -->
					<button
						type="button"
						onclick="document.getElementById('slip-upload-modal').classList.remove('hidden')"
						class="w-max cursor-pointer rounded-2xl border-2 border-navyBlue bg-white px-11 py-3 text-center text-base font-medium text-navyBlue max-md:rounded-lg max-md:px-6 max-md:py-1.5"
					>
						อัปโหลดสลิปโอนเงิน
					</button>
				@endif

				<a href="{{ route('shop.home.index') }}">
					<div class="w-max cursor-pointer rounded-2xl bg-navyBlue px-11 py-3 text-center text-base font-medium text-white max-md:rounded-lg max-md:px-6 max-md:py-1.5">
             		@lang('shop::app.checkout.cart.index.continue-shopping')
					</div>
				</a>
			</div>

			{{ view_render_event('bagisto.shop.checkout.success.continue-shopping.after', ['order' => $order]) }}
		</div>
	</div>

	<!-- Upload Slip Modal -->
	<div id="slip-upload-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 px-4">
		<div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
			<div class="mb-4 flex items-center justify-between">
				<h2 class="text-lg font-bold text-gray-800">อัปโหลดสลิปโอนเงิน</h2>
				<button
					type="button"
					onclick="document.getElementById('slip-upload-modal').classList.add('hidden')"
					class="text-gray-400 hover:text-gray-600 text-2xl leading-none"
				>
					&times;
				</button>
			</div>

			<p class="mb-4 text-sm text-gray-500">
				หมายเลขคำสั่งซื้อ: <strong>#{{ $order->increment_id }}</strong>
			</p>

			<form
				action="{{ route('shop.checkout.slip.upload', $order->id) }}"
				method="POST"
				enctype="multipart/form-data"
			>
				@csrf
				<div class="mb-4">
					<label class="mb-2 block text-sm font-medium text-gray-700">
						เลือกไฟล์สลิป (JPG, PNG, PDF)
					</label>
					<input
						type="file"
						name="slip"
						accept="image/*,.pdf"
						required
						class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-700 focus:border-navyBlue focus:outline-none"
					>
				</div>

				<div class="flex justify-end gap-3">
					<button
						type="button"
						onclick="document.getElementById('slip-upload-modal').classList.add('hidden')"
						class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
					>
						ยกเลิก
					</button>
					<button
						type="submit"
						class="rounded-lg bg-navyBlue px-5 py-2.5 text-sm font-medium text-white hover:opacity-90"
					>
						อัปโหลด
					</button>
				</div>
			</form>
		</div>
	</div>
</x-shop::layouts>
