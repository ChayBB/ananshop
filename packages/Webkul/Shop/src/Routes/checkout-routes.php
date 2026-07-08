<?php

use Illuminate\Support\Facades\Route;
use Webkul\Shop\Http\Controllers\CartController;
use Webkul\Shop\Http\Controllers\OnepageController;
use Webkul\Shop\Http\Controllers\SlipUploadController;

/**
 * Cart routes.
 */
Route::controller(CartController::class)->prefix('checkout/cart')->group(function () {
    Route::get('', 'index')->name('shop.checkout.cart.index');
});

Route::controller(OnepageController::class)->prefix('checkout/onepage')->group(function () {
    Route::get('', 'index')->name('shop.checkout.onepage.index');

    Route::get('success', 'success')->name('shop.checkout.onepage.success');
});

/**
 * Slip upload route.
 */
Route::post('checkout/slip/{order}', [SlipUploadController::class, 'upload'])->name('shop.checkout.slip.upload');
