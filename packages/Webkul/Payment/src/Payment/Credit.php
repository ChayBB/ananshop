<?php

namespace Webkul\Payment\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Customer\Repositories\CustomerRepository;

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

        return $this->getConfigData('active')
            && (bool) app(CustomerRepository::class)->getCurrentGroup()->credit;
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
