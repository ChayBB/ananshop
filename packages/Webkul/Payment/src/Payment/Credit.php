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
     * Whether the given customer (defaults to the logged-in one) has used
     * up all of their group's approved credit rounds. Every credit order
     * still counts against the approved rounds until it's cancelled or
     * refunded, not just once it's confirmed/paid off - otherwise a
     * customer could stack unlimited unpaid credit orders.
     *
     * @param  \Webkul\Customer\Contracts\Customer|null  $customer
     * @return bool
     */
    public function isCreditLimitExceeded($customer = null)
    {
        $customer = $customer ?: auth()->guard()->user();

        if (
            ! $customer
            || ! $customer->group
            || ! $customer->group->credit
        ) {
            return false;
        }

        $creditRounds = $customer->group->credit_rounds ?? 0;

        return $this->getUsedCreditRounds($customer) >= $creditRounds;
    }

    /**
     * How many of the given customer's approved credit rounds are still
     * unused. Uses the same "not cancelled/refunded" counting rule as
     * isCreditLimitExceeded() so the two never disagree.
     *
     * @param  \Webkul\Customer\Contracts\Customer|null  $customer
     * @return int
     */
    public function getRemainingCreditRounds($customer)
    {
        if (! $customer || ! $customer->group) {
            return 0;
        }

        $creditRounds = $customer->group->credit_rounds ?? 0;

        return max($creditRounds - $this->getUsedCreditRounds($customer), 0);
    }

    /**
     * @param  \Webkul\Customer\Contracts\Customer  $customer
     * @return int
     */
    protected function getUsedCreditRounds($customer)
    {
        return OrderProxy::modelClass()::where('customer_id', $customer->id)
            ->whereNotIn('custom_status', ['ยกเลิกออร์เดอร์', 'คืนเงิน'])
            ->whereHas('payment', fn ($query) => $query->where('method', 'credit'))
            ->count();
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
