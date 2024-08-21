<?php

namespace TomatoPHP\FilamentPayments\Services;

use TomatoPHP\FilamentPayments\Models\PaymentGateway;

class FilamentPaymentsServices
{
    public function loadDrivers(): void
    {
        $drivers = config('filament-payments.drivers');
        foreach ($drivers as $driver){
            $driver = app($driver);
            $paymentGate = $driver->integration();
            if(isset($paymentGate['alias'])){
                $payment = PaymentGateway::query()
                    ->where('alias', $paymentGate['alias'])
                    ->first();

                if(!$payment){
                    PaymentGateway::query()->create($paymentGate);
                }
            }
        }
    }
}
