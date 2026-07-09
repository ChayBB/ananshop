<?php

namespace Webkul\Admin\Http\Controllers\Sales;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Sales\CODOrderDataGrid;
use Webkul\Admin\DataGrids\Sales\CreditOrderDataGrid;
use Webkul\Admin\DataGrids\Sales\OrderDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Resources\AddressResource;
use Webkul\Admin\Http\Resources\CartResource;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Sales\Repositories\OrderCommentRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderCommentRepository $orderCommentRepository,
        protected CartRepository $cartRepository,
        protected CustomerGroupRepository $customerGroupRepository,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(OrderDataGrid::class)->process();
        }

        $channels = core()->getAllChannels();

        $groups = $this->customerGroupRepository->findWhere([['code', '<>', 'guest']]);

        return view('admin::sales.orders.index', compact('channels', 'groups'));
    }

    /**
     * Display a listing of the COD resource.
     *
     * @return View
     */
    public function codIndex()
    {
        if (request()->ajax()) {
            return datagrid(CODOrderDataGrid::class)->process();
        }

        return view('admin::sales.cod.index');
    }

    /**
     * Display a listing of the credit customers resource.
     *
     * @return View
     */
    public function creditIndex()
    {
        if (request()->ajax()) {
            return datagrid(CreditOrderDataGrid::class)->process();
        }

        return view('admin::sales.credit.index');
    }

    /**
     * Upload payment slip for a credit customer's order.
     *
     * @param  int  $orderId
     * @return RedirectResponse
     */
    public function uploadCreditSlip($orderId)
    {
        $this->storeUploadedSlip($orderId);

        return redirect()->route('admin.sales.credit.index');
    }

    /**
     * Upload payment slip for an order from its view/invoice page.
     *
     * @param  int  $orderId
     * @return RedirectResponse
     */
    public function uploadSlip($orderId)
    {
        $this->storeUploadedSlip($orderId);

        return redirect()->back();
    }

    /**
     * Validate and store the uploaded payment slip against the given order.
     *
     * @param  int  $orderId
     * @return void
     */
    protected function storeUploadedSlip($orderId)
    {
        request()->validate([
            'slip' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:10240'],
        ]);

        $order = $this->orderRepository->findOrFail($orderId);

        $path = request()->file('slip')->store("order_slips/{$order->id}", 'public');

        $order->update(['slip_path' => $path]);

        session()->flash('success', 'อัปโหลดสลิปโอนเงินเรียบร้อยแล้ว');
    }

    /**
     * Update order custom status and/or payment method.
     *
     * @return RedirectResponse
     */
    public function updateCustomStatus(int $id)
    {
        $action = request()->input('action');
        $order = $this->orderRepository->findOrFail($id);

        if ($action === 'cod') {
            if (! empty($order->slip_path)) {
                session()->flash('error', 'ไม่สามารถเปลี่ยนเป็นเก็บเงินปลายทางได้ เนื่องจากลูกค้าอัปโหลดสลิปเรียบร้อยแล้ว');

                return redirect()->back();
            }

            if (in_array($order->custom_status, ['ยืนยันการชำระเงินแล้ว', 'เสร็จสมบูรณ์', 'ยกเลิกออร์เดอร์', 'คืนเงิน'])) {
                session()->flash('error', 'ไม่สามารถเปลี่ยนเป็นเก็บเงินปลายทางได้สำหรับออร์เดอร์นี้');

                return redirect()->back();
            }

            // Update payment method to cashondelivery in order_payment table
            DB::table('order_payment')
                ->where('order_id', $order->id)
                ->update(['method' => 'cashondelivery']);

            // If already shipped, set to 'เรียกเก็บเงินจากพนักงานขนส่ง', otherwise set to 'เก็บเงินปลายทาง'
            $newStatus = in_array($order->custom_status, ['จัดส่ง', 'เรียกเก็บเงินจากพนักงานขนส่ง']) ? 'เรียกเก็บเงินจากพนักงานขนส่ง' : 'เก็บเงินปลายทาง';
            $order->update(['custom_status' => $newStatus]);

            session()->flash('success', 'ย้ายรายชื่อออร์เดอร์นี้ไปแสดงในเมนู เก็บเงินปลายทาง เรียบร้อยแล้ว');
        } elseif ($action === 'confirm_payment') {
            if (empty($order->slip_path)) {
                session()->flash('error', 'ไม่สามารถยืนยันการชำระเงินได้ เนื่องจากลูกค้ายังไม่ได้อัปโหลดสลิปหลักฐานการชำระเงิน');

                return redirect()->back();
            }

            if (in_array($order->custom_status, ['ยืนยันการชำระเงินแล้ว', 'เสร็จสมบูรณ์', 'ยกเลิกออร์เดอร์', 'คืนเงิน'])) {
                session()->flash('error', 'ไม่สามารถยืนยันการชำระเงินซ้ำ หรือ ดำเนินการต่อได้');

                return redirect()->back();
            }

            $newStatus = in_array($order->custom_status, ['จัดส่ง', 'จัดส่งโดยเครดิต', 'เรียกเก็บเงินจากพนักงานขนส่ง']) ? 'เสร็จสมบูรณ์' : 'ยืนยันการชำระเงินแล้ว';
            $order->update(['custom_status' => $newStatus]);
            session()->flash('success', "อัปเดตสถานะเป็น: {$newStatus} เรียบร้อยแล้ว");
        } elseif ($action === 'ship') {
            if (in_array($order->custom_status, ['จัดส่ง', 'จัดส่งโดยเครดิต', 'เรียกเก็บเงินจากพนักงานขนส่ง', 'เสร็จสมบูรณ์', 'ยกเลิกออร์เดอร์', 'คืนเงิน'])) {
                session()->flash('error', 'ไม่สามารถจัดส่งออร์เดอร์นี้ได้เนื่องจากอยู่ในสถานะที่ไม่ถูกต้อง');

                return redirect()->back();
            }

            $isCOD = ($order->payment->method === 'cashondelivery' || $order->custom_status === 'เก็บเงินปลายทาง');
            $isCredit = $order->payment->method === 'credit';

            if ($order->custom_status === 'ยืนยันการชำระเงินแล้ว') {
                $newStatus = 'เสร็จสมบูรณ์';
            } elseif ($isCOD) {
                $newStatus = 'เรียกเก็บเงินจากพนักงานขนส่ง';
            } elseif ($isCredit) {
                $newStatus = 'จัดส่งโดยเครดิต';
            } else {
                $newStatus = 'จัดส่ง';
            }
            $order->update(['custom_status' => $newStatus]);
            session()->flash('success', "อัปเดตสถานะเป็น: {$newStatus} เรียบร้อยแล้ว");
        } elseif ($action === 'cancel') {
            $order->update(['custom_status' => 'ยกเลิกออร์เดอร์']);
            session()->flash('success', 'อัปเดตสถานะเป็น: ยกเลิกออร์เดอร์ เรียบร้อยแล้ว');
        } elseif ($action === 'refund') {
            $order->update(['custom_status' => 'คืนเงิน']);
            session()->flash('success', 'อัปเดตสถานะเป็น: คืนเงิน เรียบร้อยแล้ว');
        }

        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(int $cartId)
    {
        $cart = $this->cartRepository->find($cartId);

        if (! $cart) {
            return redirect()->route('admin.sales.orders.index');
        }

        $addresses = AddressResource::collection($cart->customer->addresses);

        $cart = new CartResource($cart);

        return view('admin::sales.orders.create', compact('cart', 'addresses'));
    }

    /**
     * Store order
     */
    public function store(int $cartId)
    {
        $cart = $this->cartRepository->findOrFail($cartId);

        Cart::setCart($cart);

        if (Cart::hasError()) {
            return response()->json([
                'message' => trans('admin::app.sales.orders.create.error'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        Cart::collectTotals();

        try {
            $this->validateOrder();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $cart = Cart::getCart();

        if (! in_array($cart->payment->method, ['cashondelivery', 'moneytransfer'])) {
            return response()->json([
                'message' => trans('admin::app.sales.orders.create.payment-not-supported'),
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        Cart::removeCart($cart);

        session()->flash('order', trans('admin::app.sales.orders.create.order-placed-success'));

        return new JsonResource([
            'redirect' => true,
            'redirect_url' => route('admin.sales.orders.view', $order->id),
        ]);
    }

    /**
     * Show the view for the specified resource.
     *
     * @return View
     */
    public function view($id)
    {
        $order = $this->orderRepository->find($id);

        if (! $order) {
            $order = $this->orderRepository->findOneByField('increment_id', $id);

            if ($order) {
                return redirect()->route('admin.sales.orders.view', $order->id);
            }

            abort(404);
        }

        return view('admin::sales.orders.view', compact('order'));
    }

    /**
     * Reorder action for the specified resource.
     *
     * @return Response
     */
    public function reorder(int $id)
    {
        $order = $this->orderRepository->findOrFail($id);

        if (! $order->customer) {
            session()->flash('error', trans('admin::app.sales.orders.view.reorder-customer-missing'));

            return redirect()->route('admin.sales.orders.view', $id);
        }

        $cart = Cart::createCart([
            'customer' => $order->customer,
            'is_active' => false,
        ]);

        Cart::setCart($cart);

        $skippedBooking = false;

        foreach ($order->items as $item) {
            if ($item->type === 'booking') {
                $skippedBooking = true;

                continue;
            }

            try {
                Cart::addProduct($item->product, $item->additional);
            } catch (\Exception $e) {
                // do nothing
            }
        }

        if ($skippedBooking) {
            session()->flash('info', trans('admin::app.sales.orders.view.reorder-booking-skipped'));
        }

        return redirect()->route('admin.sales.orders.create', $cart->id);
    }

    /**
     * Cancel action for the specified resource.
     *
     * @return Response
     */
    public function cancel(int $id)
    {
        $result = $this->orderRepository->cancel($id, force: true);

        if ($result) {
            session()->flash('success', trans('admin::app.sales.orders.view.cancel-success'));
        } else {
            session()->flash('error', trans('admin::app.sales.orders.view.create-error'));
        }

        return redirect()->route('admin.sales.orders.view', $id);
    }

    /**
     * Add comment to the order
     *
     * @return Response
     */
    public function comment(int $id)
    {
        $validatedData = $this->validate(request(), [
            'comment' => 'required',
            'customer_notified' => 'sometimes|sometimes',
        ]);

        $validatedData['order_id'] = $id;

        Event::dispatch('sales.order.comment.create.before');

        $comment = $this->orderCommentRepository->create($validatedData);

        Event::dispatch('sales.order.comment.create.after', $comment);

        session()->flash('success', trans('admin::app.sales.orders.view.comment-success'));

        return redirect()->route('admin.sales.orders.view', $id);
    }

    /**
     * Result of search product.
     *
     * @return JsonResponse
     */
    public function search()
    {
        $orders = $this->orderRepository->scopeQuery(function ($query) {
            return $query->where('customer_email', 'like', '%'.urldecode(request()->input('query')).'%')
                ->orWhere('status', 'like', '%'.urldecode(request()->input('query')).'%')
                ->orWhere(DB::raw('CONCAT(customer_first_name, " ", customer_last_name)'), 'like', '%'.urldecode(request()->input('query')).'%')
                ->orWhere('increment_id', request()->input('query'))
                ->orderBy('created_at', 'desc');
        })->paginate(10);

        foreach ($orders as $key => $order) {
            $orders[$key]['formatted_created_at'] = core()->formatDate($order->created_at, 'd M Y');

            $orders[$key]['status_label'] = $order->status_label;

            $orders[$key]['customer_full_name'] = $order->customer_full_name;
        }

        return response()->json($orders);
    }

    /**
     * Validate order before creation.
     *
     * @return void|\Exception
     */
    public function validateOrder()
    {
        $cart = Cart::getCart();

        if (! Cart::haveMinimumOrderAmount()) {
            throw new \Exception(trans('admin::app.sales.orders.create.minimum-order-error', [
                'amount' => core()->formatPrice(core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0),
            ]));
        }

        if (
            $cart->haveStockableItems()
            && ! $cart->shipping_address
        ) {
            throw new \Exception(trans('admin::app.sales.orders.create.check-shipping-address'));
        }

        if (! $cart->billing_address) {
            throw new \Exception(trans('admin::app.sales.orders.create.check-billing-address'));
        }

        if (
            $cart->haveStockableItems()
            && ! $cart->selected_shipping_rate
        ) {
            throw new \Exception(trans('admin::app.sales.orders.create.specify-shipping-method'));
        }

        if (! $cart->payment) {
            throw new \Exception(trans('admin::app.sales.orders.create.specify-payment-method'));
        }
    }
}
