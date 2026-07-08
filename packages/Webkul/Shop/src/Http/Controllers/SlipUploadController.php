<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Sales\Contracts\Order;
use Webkul\Sales\Repositories\OrderRepository;

class SlipUploadController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected OrderRepository $orderRepository) {}

    /**
     * Handle transfer slip upload.
     */
    public function upload(Request $request, $order)
    {
        $request->validate([
            'slip' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:10240'],
        ]);

        if (! $order instanceof Order) {
            $order = $this->orderRepository->findOrFail($order);
        }

        $path = $request->file('slip')->store("order_slips/{$order->id}", 'public');

        $order->update(['slip_path' => $path]);

        session()->flash('success', 'อัปโหลดสลิปโอนเงินเรียบร้อยแล้ว');

        return redirect()->route('shop.customers.account.orders.index');
    }
}
