<?php

namespace TomatoPHP\FilamentPayments\Services\Drivers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentCurrency;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentGateway;

class Tap extends Driver
{
    public static function process(Payment $payment): false|string
    {
        $gatewayParameters = $payment->gateway->gateway_parameters;

        $name = $payment->customer['name'];
        $names = explode(' ', $name, 2);

        $firstName = $names[0] ?? '';
        $lastName = $names[1] ?? '';

        $unique_id = uniqid();
        $response = Http::withHeaders([
            "authorization" => "Bearer " . $gatewayParameters['secret_key'],
            "Content-Type" => "application/json",
            'lang_code' => $gatewayParameters['lang_code']
        ])->post('https://api.tap.company/v2/charges', [
            "amount" => $payment->amount + $payment->charge,
            "currency" => $payment->method_currency,
            "threeDSecure" => true,
            "save_card" => false,
            "description" => "Cerdit",
            "statement_descriptor" => "Cerdit",
            "reference" => [
                "transaction" => $unique_id,
                "order" => $unique_id
            ],
            "receipt" => [
                "email" => true,
                "sms" => true
            ],
            "customer" => [
                "first_name" => $firstName,
                "middle_name" => "",
                "last_name" => $lastName,
                "email" => $payment->customer['email'],
                "phone" => [
                    "country_code" => "20",
                    "number" => $payment->customer['mobile']
                ]
            ],
            "source" => ["id" => "src_all"],
            "post" => ["url" => route('payments.callback', 'Tap') . "?session=$payment->trx"],
            "redirect" => ["url" => route('payments.callback', 'Tap') . "?session=$payment->trx"]
        ])->json();

        try {
            $send['session'] = $response['id'];
            $send['redirect'] = $response['transaction']['url'];
            return json_encode($send);
        } catch (\Throwable $th) {
            return $response;
        }
    }

    public static function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $gatewayData = \TomatoPHP\FilamentPayments\Models\PaymentGateway::where('alias', 'Tap')->orderBy('id', 'desc')->firstOrFail();
        $gatewayParameter = $gatewayData->gateway_parameters;

        $sessionId = $request->get('session');

        $payment = Payment::where('trx',  $sessionId)->where('status', 0)->firstOrFail();

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $gatewayParameter['secret_key'],
            "Content-Type" => "application/json",
        ])->get('https://api.tap.company/v2/charges/' . $request->tap_id)->json();

        if (isset($response['status']) && $response['status'] == "CAPTURED") {
            self::paymentDataUpdate($payment);
            return redirect($payment->success_url);
        } else {
            self::paymentDataUpdate($payment, true);
            return redirect($payment->failed_url);
        }
    }

    public function integration(): array
    {
        return PaymentGateway::make('Tap')
            ->alias('Tap')
            ->status(true)
            ->crypto(false)
            ->gateway_parameters([
                "secret_key" => "",
                "public_key" => "",
                "lang_code" => ""
            ])
            ->supported_currencies([
                PaymentCurrency::make('USD')
                    ->symbol('$')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(10000)
                    ->fixed_charge(0.20)
                    ->percent_charge(2.9)
                    ->toArray(),

                PaymentCurrency::make('EGP')
                    ->symbol('ج.م')
                    ->rate(0.021)
                    ->minimum_amount(10)
                    ->maximum_amount(15000)
                    ->fixed_charge(0.50)
                    ->percent_charge(3.0)
                    ->toArray(),

                PaymentCurrency::make('SAR')
                    ->symbol('ر.س')
                    ->rate(0.27)
                    ->minimum_amount(1)
                    ->maximum_amount(5000)
                    ->fixed_charge(0.20)
                    ->percent_charge(2.5)
                    ->toArray(),

                PaymentCurrency::make('AED')
                    ->symbol('د.إ')
                    ->rate(0.27)
                    ->minimum_amount(1)
                    ->maximum_amount(5000)
                    ->fixed_charge(0.25)
                    ->percent_charge(2.5)
                    ->toArray(),

                PaymentCurrency::make('BHD')
                    ->symbol('.د.ب')
                    ->rate(2.65)
                    ->minimum_amount(1)
                    ->maximum_amount(2000)
                    ->fixed_charge(0.10)
                    ->percent_charge(2.2)
                    ->toArray(),

                PaymentCurrency::make('EUR')
                    ->symbol('€')
                    ->rate(1.11)
                    ->minimum_amount(1)
                    ->maximum_amount(850)
                    ->fixed_charge(0.18)
                    ->percent_charge(2.7)
                    ->toArray(),

                PaymentCurrency::make('GBP')
                    ->symbol('£')
                    ->rate(1.32)
                    ->minimum_amount(1)
                    ->maximum_amount(700)
                    ->fixed_charge(0.15)
                    ->percent_charge(2.5)
                    ->toArray(),

                PaymentCurrency::make('KWD')
                    ->symbol('د.ك')
                    ->rate(3.27)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.20)
                    ->percent_charge(2.3)
                    ->toArray(),

                PaymentCurrency::make('OMR')
                    ->symbol('ر.ع.')
                    ->rate(2.60)
                    ->minimum_amount(1)
                    ->maximum_amount(2000)
                    ->fixed_charge(0.15)
                    ->percent_charge(2.4)
                    ->toArray(),

                PaymentCurrency::make('QAR')
                    ->symbol('ر.ق')
                    ->rate(0.27)
                    ->minimum_amount(1)
                    ->maximum_amount(5000)
                    ->fixed_charge(0.25)
                    ->percent_charge(2.6)
                    ->toArray(),
            ])
            ->toArray();
    }
}
