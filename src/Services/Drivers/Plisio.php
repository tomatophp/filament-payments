<?php

namespace TomatoPHP\FilamentPayments\Services\Drivers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentCurrency;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentGateway;

class Plisio extends Driver
{
    public static function process(Payment $payment): false|string
    {
        $gatewayData = $payment->gateway->gateway_parameters;

        if(!$gatewayData['secret_key']){
            $send['error'] = true;
            $send['message'] = 'Plisio secret key is not set';
            return json_encode($send);
        }
        $plisioGateway = new \Plisio\PlisioSdkLaravel\Payment($gatewayData['secret_key']);

        try {
            $data = array(
                'order_name' => 'Order #' . $payment->trx,
                'order_number' => $payment->trx,
                'source_amount' => number_format($payment->amount + $payment->charge, 8, '.', ''),
                'source_currency' => $payment->method_currency,
                'cancel_url' => route('payment.cancel', $payment->trx),
                'callback_url' => route('payments.callback', 'Plisio') .  "?session=$payment->trx",
                'success_url' => route('payments.callback', 'Plisio') .  "?session=$payment->trx",
                'email' => $payment->customer['email'],
                'plugin' => 'laravelSdk',
                'version' => '1.0.0'
            );

            //Create invoice and put response to the $response variable.
            $response = $plisioGateway->createTransaction($data);
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        if ($response && $response['status'] !== 'error' && !empty($response['data'])) {
            $send['redirect'] = $response['data']['invoice_url'];
            $send['session'] = $response['data']['txn_id'];
            return json_encode($send);
        } elseif ($response && $response['status'] == 'error' && $response['data']['code'] === 205) {
            $send['redirect'] = 'https://plisio.net/invoice/' . $payment->method_code;
            return json_encode($send);
        } else {
            $send['error'] = true;
            $send['message'] = $response['data']['message'];
            return json_encode($send);
        }
    }

    public static function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $gatewayData = \TomatoPHP\FilamentPayments\Models\PaymentGateway::where('alias', 'Plisio')->orderBy('id', 'desc')->firstOrFail();
        $gatewayParameter = $gatewayData->gateway_parameters;

        $sessionId = $request->get('session');

        $payment = Payment::where('trx',  $sessionId)->where('status', 0)->firstOrFail();

        $response = Http::get("https://api.plisio.net/api/v1/operations/{$payment->method_code}", [
            'api_key' => $gatewayParameter['secret_key'],
        ]);

        $response = $response->json();

        if ($response['status'] === 'success' && ($response['data']['status'] === 'completed' || $response['data']['status'] === 'mismatch')) {
            self::paymentDataUpdate($payment);

            return redirect($payment->success_url);
        }

        self::paymentDataUpdate($payment, true);
        return redirect($payment->failed_url);
    }

    public function integration(): array
    {
        return PaymentGateway::make('Plisio')
            ->alias('Plisio')
            ->status(true)
            ->crypto(true)
            ->gateway_parameters([
                "secret_key" => ""
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
