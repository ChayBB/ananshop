<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Webkul\Customer\Models\Customer;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Shop\Http\Requests\Customer\LoginRequest;

class SessionController extends Controller
{
    /**
     * Display the resource.
     *
     * @return RedirectResponse|View
     */
    public function index()
    {
        if (auth()->guard('customer')->check()) {
            return redirect()->route('shop.home.index');
        }

        return view('shop::customers.sign-in');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(LoginRequest $loginRequest)
    {
        $login = trim($loginRequest->input('email'));
        $password = $loginRequest->input('password');

        $channelId = core()->getCurrentChannel()->id;

        $customer = Customer::where(function ($query) use ($login) {
            $query->where('email', $login)
                ->orWhere('username', $login)
                ->orWhere('phone', $login)
                ->orWhere('first_name', $login);
        })->where('channel_id', $channelId)->first();

        if (! $customer) {
            $customer = Customer::where(function ($query) use ($login) {
                $query->where('email', $login)
                    ->orWhere('username', $login)
                    ->orWhere('phone', $login)
                    ->orWhere('first_name', $login);
            })->first();
        }

        if (! $customer || ! Hash::check($password, $customer->password)) {
            session()->flash('error', trans('shop::app.customers.login-form.invalid-credentials'));

            return redirect()->back();
        }

        if (! $customer->status) {
            session()->flash('warning', trans('shop::app.customers.login-form.not-activated'));

            return redirect()->back();
        }

        if (! $customer->is_verified) {
            session()->flash('info', trans('shop::app.customers.login-form.verify-first'));

            Cookie::queue(Cookie::make('enable-resend', 'true', 1));

            Cookie::queue(Cookie::make('email-for-resend', $customer->email, 1));

            return redirect()->back();
        }

        auth()->guard('customer')->login($customer);

        /**
         * Event passed to prepare cart after login.
         */
        Event::dispatch('customer.after.login', auth()->guard('customer')->user());

        if (core()->getConfigData('customer.settings.login_options.redirected_to_page') == 'account') {
            return redirect()->route('shop.customers.account.profile.index');
        }

        return redirect()->route('shop.home.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy()
    {
        $id = auth()->guard('customer')->user()->id;

        auth()->guard('customer')->logout();

        Event::dispatch('customer.after.logout', $id);

        return redirect()->route('shop.home.index');
    }
}
