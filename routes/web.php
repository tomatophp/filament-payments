<?php

use TomatoPHP\FilamentPayments\Http\Controllers\Gateway\Plisio\ProcessController as PlisioProcessController;
use TomatoPHP\FilamentPayments\Http\Controllers\Gateway\Stripe\ProcessController as StripeProcessController;
use TomatoPHP\FilamentPayments\Http\Controllers\Gateway\StripeV3\ProcessController;
use TomatoPHP\FilamentPayments\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use TomatoPHP\FilamentPayments\Livewire\PaymentProcess;

Route::middleware(['web'])->name('payment.')->group(function () {
    Route::get('pay/{trx}', PaymentProcess::class)->name('index');
    Route::get('pay/{trx}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::post('payment/initiate', [PaymentController::class, 'initiate'])->name('initiate');
    Route::get('payment/info', [PaymentController::class, 'info'])->name('info');
});

Route::any('stripe-v3', [ProcessController::class, 'verify'])->name('stripe-v3');
Route::any('stripe-embedded', [StripeProcessController::class, 'verify'])->name('stripe-embedded');
Route::any('plisio', [PlisioProcessController::class, 'verify'])->name('plisio');