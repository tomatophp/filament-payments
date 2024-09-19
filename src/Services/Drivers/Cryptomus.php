<?php

namespace TomatoPHP\FilamentPayments\Services\Drivers;

use Illuminate\Http\Request;
use Cryptomus\Api\Client;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentCurrency;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentGateway;


class Cryptomus extends Driver
{
    public static function process(Payment $payment): false|string
    {
        $gatewayData = $payment->gateway->gateway_parameters;

        $cryptomusGateway = Client::payment($gatewayData['payment_key'], $gatewayData['merchant_uuid']);

        try {
            $param = [
                'amount' => strval($payment->amount + $payment->charge),
                'currency' => $payment->method_currency,
                'order_id' => $payment->trx,
                'url_return' => route('payment.cancel', $payment->trx),
                'url_success' => route('payments.callback', 'Cryptomus') .  "?session=$payment->trx",
                'url_callback' => route('payments.callback', 'Cryptomus') .  "?session=$payment->trx",
                'is_payment_multiple' => true,
                'lifetime' => '3600',
                'is_refresh' => true,
                'course_source' => 'Binance'
            ];

            $response = $cryptomusGateway->create($param);
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }
        
        if ($response && $response['status'] == 'check' ) {
            $send['redirect'] = $response['url'];
            $send['session'] = $response['uuid'];
            return json_encode($send);
        } else {
            $send['error'] = true;
            $send['message'] = ['message'];
            return json_encode($send);
        }
    }

    public static function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $gatewayData = \TomatoPHP\FilamentPayments\Models\PaymentGateway::where('alias', 'Cryptomus')->orderBy('id', 'desc')->firstOrFail();
        $gatewayParameter = $gatewayData->gateway_parameters;

        $sessionId = $request->get('session');

        $payment = Payment::where('trx',  $sessionId)->where('status', 0)->firstOrFail();

        $cryptomusGateway = Client::payment($gatewayParameter['payment_key'], $gatewayParameter['merchant_uuid']);

        $data = ["order_id" => $sessionId];

        $result = $cryptomusGateway->info($data);

        if ($result['is_final'] && $result['order_id'] && in_array($result['payment_status'], ['paid', 'paid_over'])) {
            self::paymentDataUpdate($payment);
            return redirect($payment->success_url);
        }

        self::paymentDataUpdate($payment, true);
        return redirect($payment->failed_url);
    }

    public function integration(): array
    {
        return PaymentGateway::make('Cryptomus')
            ->alias('Cryptomus')
            ->status(true)
            ->crypto(true)
            ->gateway_parameters([
                "payment_key" => "",
                "merchant_uuid" => ""
            ])
            ->supported_currencies([
                PaymentCurrency::make('USD')
                    ->symbol('$')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray()
            ])
            ->toArray();
    }
}
