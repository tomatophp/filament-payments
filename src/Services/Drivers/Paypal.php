<?php

namespace TomatoPHP\FilamentPayments\Services\Drivers;

use Exception;
use Illuminate\Http\Request;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentCurrency;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentGateway;

class Paypal extends Driver
{
    public static function process(Payment $payment): false|string
    {
        $gatewayParameters = $payment->gateway->gateway_parameters;

        if ($gatewayParameters['mode'] == "live")
            $environment = new ProductionEnvironment($gatewayParameters['client_id'], $gatewayParameters['secret']);
        else
            $environment = new SandboxEnvironment($gatewayParameters['client_id'], $gatewayParameters['secret']);

        $client = new PayPalHttpClient($environment);

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => uniqid(),
                "amount" => [
                    "value" => $payment->amount + $payment->charge,
                    "currency_code" => $payment->method_currency
                ]
            ]],
            "application_context" => [
                "cancel_url" => route('payment.cancel', $payment->trx) . "?session=$payment->trx",
                "return_url" => route('payments.callback', 'Paypal') . "?session=$payment->trx"
            ]
        ];

        try {
            $response = json_decode(json_encode($client->execute($request)), true);

            $send['session'] = $response['result']['id'];
            $send['redirect'] = collect($response['result']['links'])->where('rel', 'approve')->firstOrFail()['href'];
            return json_encode($send);
        } catch (Exception $e) {
            $send['error'] = true;
            $send['message'] = $e;
            return json_encode($send);
        }
    }

    public static function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $gatewayData = \TomatoPHP\FilamentPayments\Models\PaymentGateway::where('alias', 'Paypal')->orderBy('id', 'desc')->firstOrFail();
        $gatewayParameter = $gatewayData->gateway_parameters;

        $sessionId = $request->get('session');

        $payment = Payment::where('trx',  $sessionId)->where('status', 0)->firstOrFail();

        if ($gatewayParameter['mode'] == "live")
            $environment = new ProductionEnvironment($gatewayParameter['client_id'], $gatewayParameter['secret']);
        else
            $environment = new SandboxEnvironment($gatewayParameter['client_id'], $gatewayParameter['secret']);

        $client = new PayPalHttpClient($environment);

        try {
            $response = $client->execute(new OrdersCaptureRequest($request['token']));
            $result = json_decode(json_encode($response), true);
            if ($result['result']['status'] == "COMPLETED" && $result['statusCode'] == 201) {
                self::paymentDataUpdate($payment);
                return redirect($payment->success_url);
            } else {
                self::paymentDataUpdate($payment, true);
                return redirect($payment->failed_url);
            }
        } catch (Exception $e) {
            self::paymentDataUpdate($payment, true);
            return redirect($payment->failed_url);
        }
    }

    public function integration(): array
    {
        return PaymentGateway::make('Paypal')
            ->alias('Paypal')
            ->status(true)
            ->crypto(true)
            ->gateway_parameters([
                "client_id" => "",
                "secret" => "",
                "mode" => ""
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

                PaymentCurrency::make('EUR')
                    ->symbol('€')
                    ->rate(0.93)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.35)
                    ->percent_charge(2.9)
                    ->toArray(),

                PaymentCurrency::make('GBP')
                    ->symbol('£')
                    ->rate(0.75)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.35)
                    ->percent_charge(2.9)
                    ->toArray(),

                PaymentCurrency::make('AUD')
                    ->symbol('A$')
                    ->rate(1.50)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.30)
                    ->percent_charge(2.6)
                    ->toArray(),

                PaymentCurrency::make('CAD')
                    ->symbol('C$')
                    ->rate(1.35)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.30)
                    ->percent_charge(2.9)
                    ->toArray(),

                PaymentCurrency::make('JPY')
                    ->symbol('¥')
                    ->rate(140)
                    ->minimum_amount(100)
                    ->maximum_amount(100000)
                    ->fixed_charge(40)
                    ->percent_charge(3.6)
                    ->toArray(),

                PaymentCurrency::make('CNY')
                    ->symbol('¥')
                    ->rate(7.00)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(2.50)
                    ->percent_charge(2.9)
                    ->toArray(),

                PaymentCurrency::make('INR')
                    ->symbol('₹')
                    ->rate(83)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(3.00)
                    ->percent_charge(3.0)
                    ->toArray(),

                PaymentCurrency::make('BRL')
                    ->symbol('R$')
                    ->rate(5.20)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.60)
                    ->percent_charge(3.4)
                    ->toArray(),

                PaymentCurrency::make('MXN')
                    ->symbol('$')
                    ->rate(18.00)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(4.00)
                    ->percent_charge(3.0)
                    ->toArray(),
            ])
            ->toArray();
    }
}
