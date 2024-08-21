<?php

use TomatoPHP\FilamentPayments\Http\Controllers\Gateway\Plisio\ProcessController as PlisioProcessController;
use TomatoPHP\FilamentPayments\Http\Controllers\Gateway\Stripe\ProcessController as StripeProcessController;
use TomatoPHP\FilamentPayments\Http\Controllers\Gateway\StripeV3\ProcessController as StripeV3ProcessController;
use TomatoPHP\FilamentPayments\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->name('payment.')->group(function () {
    Route::get('pay/{trx}', [PaymentController::class, 'pay'])->name('index');
    Route::post('pay/{trx}/process', [PaymentController::class, 'process'])->name('pay');
    Route::post('/payment/calculate-fee', [PaymentController::class, 'calculateFee'])->name('calculateFee');

    Route::get('pay/{trx}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    // Route::post('payment/initiate', [PaymentController::class, 'depositInsert'])->name('insert');
    // Route::get('payment/info', [PaymentController::class, 'info'])->name('info');
});


Route::any('stripe-v3', [StripeV3ProcessController::class, 'verify'])->name('stripe-v3');
Route::any('stripe-embedded', [StripeProcessController::class, 'verify'])->name('stripe-embedded');
Route::any('plisio', [PlisioProcessController::class, 'verify'])->name('plisio');
