<?php

use TomatoPHP\FilamentPayments\Http\Controllers\Gateway\Plisio\ProcessController as PlisioProcessController;
use TomatoPHP\FilamentPayments\Http\Controllers\Gateway\Stripe\ProcessController as StripeProcessController;
use TomatoPHP\FilamentPayments\Http\Controllers\Gateway\StripeV3\ProcessController as StripeV3ProcessController;
use TomatoPHP\FilamentPayments\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use TomatoPHP\FilamentPayments\Livewire\PaymentProcess;

Route::middleware(['web'])->name('payment.')->prefix('pay')->group(function () {
    Route::get('{trx}', PaymentProcess::class)->name('index');
    Route::get('{trx}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::post('initiate', [PaymentController::class, 'initiate'])->name('initiate');
    Route::get('info', [PaymentController::class, 'info'])->name('info');
});

Route::any('pay/callback/{gateway}', [PaymentController::class, 'verify'])->name('payments.callback');
