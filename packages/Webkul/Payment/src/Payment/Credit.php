<?php

namespace Webkul\Payment\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Sales\Models\OrderProxy;

class Credit extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'credit';

    /**
     * Get redirect url.
     *
     * @return string
     */
    public function getRedirectUrl() {}

    /**
     * Is available.
     *
     * @return bool
     */
    public function isAvailable()
    {
        if (! $this->cart) {
            $this->setCart();
        }

        if (
            ! $this->getConfigData('active')
            || ! (bool) app(CustomerRepository::class)->getCurrentGroup()->credit
            || ! auth()->guard()->user()
        ) {
            return false;
        }

        return ! $this->isCreditLimitExceeded();
    }

    /**
     * Whether the logged-in customer has used up all of their group's
     * approved credit rounds. Every credit order still counts against the
     * approved rounds until it's cancelled or refunded, not just once it's
     * confirmed/paid off - otherwise a customer could stack unlimited
     * unpaid credit orders.
     *
     * @return bool
     */
    public function isCreditLimitExceeded()
    {
        $customer = auth()->guard()->user();

        if (
            ! $customer
            || ! $customer->group
            || ! $customer->group->credit
        ) {
            return false;
        }

        $creditRounds = $customer->group->credit_rounds ?? 0;

        $usedRounds = OrderProxy::modelClass()::where('customer_id', $customer->id)
            ->whereNotIn('custom_status', ['ยกเลิกออร์เดอร์', 'คืนเงิน'])
            ->whereHas('payment', fn ($query) => $query->where('method', 'credit'))
            ->count();

        return $usedRounds >= $creditRounds;
    }

    /**
     * Get payment method image.
     *
     * @return array
     */
    public function getImage()
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/credit.svg', 'shop');
    }
}
