<?php

namespace TomatoPHP\FilamentPayments\Tests\Fixtures;

use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentCurrency;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentGateway;
use TomatoPHP\FilamentPayments\Services\Drivers\Driver;

/**
 * An offline driver used by the test suite; it never talks to a real payment provider.
 */
class FakeGateway extends Driver
{
    public static function process(Payment $payment): false|string
    {
        return json_encode([
            'session' => 'fake-session-'.$payment->trx,
            'redirect' => 'https://checkout.example.test/'.$payment->trx,
        ]);
    }

    public static function verify(Request $request): Application|RedirectResponse|Redirector
    {
        $payment = Payment::where('trx', $request->input('session'))->where('status', 0)->firstOrFail();

        self::paymentDataUpdate($payment);

        return redirect($payment->success_url);
    }

    public function integration(): array
    {
        return PaymentGateway::make('Fake Gateway')
            ->alias('FakeGateway')
            ->status(true)
            ->crypto(false)
            ->gateway_parameters([
                'secret_key' => '',
            ])
            ->supported_currencies([
                PaymentCurrency::make('USD')
                    ->symbol('$')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2.9)
                    ->toArray(),
            ])
            ->toArray();
    }
}
