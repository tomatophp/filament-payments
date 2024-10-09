<?php

use TomatoPHP\FilamentPayments\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use TomatoPHP\FilamentPayments\Livewire\PaymentProcess;

Route::domain(config('filament-tenancy.central_domain'))->middleware(['web'])->group(function () {
    Route::name('payment.')->prefix('pay')->middleware(['auth:accounts'])->withoutMiddleware([\TomatoPHP\FilamentSubscriptions\Http\Middleware\VerifyBillableIsSubscribed::class])->group(function () {
        Route::get('{trx}', PaymentProcess::class)->name('index');
        Route::get('{trx}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
        Route::post('initiate', [PaymentController::class, 'initiate'])->name('initiate');
        Route::get('info', [PaymentController::class, 'info'])->name('info');
    });

    Route::any('pay/callback/{gateway}', [PaymentController::class, 'verify'])->name('payments.callback');
});
