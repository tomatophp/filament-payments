<?php

namespace TomatoPHP\FilamentPayments\Services\Drivers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentCurrency;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentGateway;

class Paymob extends Driver
{
    public static function process(Payment $payment): false|string
    {
        $gatewayData = $payment->gateway->gateway_parameters;

        $name = $payment->customer['name'];
        $names = explode(' ', $name, 2);

        $firstName = $names[0] ?? '';
        $lastName = $names[1] ?? '';

        $request_new_token = Http::withHeaders(['content-type' => 'application/json'])
            ->post('https://accept.paymobsolutions.com/api/auth/tokens', [
                "api_key" => $gatewayData['api_key']
            ])->json();

        $get_order = Http::withHeaders(['content-type' => 'application/json'])
            ->post('https://accept.paymobsolutions.com/api/ecommerce/orders', [
                "auth_token" => $request_new_token['token'],
                "delivery_needed" => "false",
                "amount_cents" => round($payment->amount + $payment->charge, 2) * 100,
                "items" => []
            ])->json();

        $get_url_token = Http::withHeaders(['content-type' => 'application/json'])
            ->post('https://accept.paymobsolutions.com/api/acceptance/payment_keys', [
                "auth_token" => $request_new_token['token'],
                "expiration" => 36000,
                "amount_cents" => $get_order['amount_cents'],
                "order_id" => $get_order['id'],
                "billing_data" => [
                    "apartment" => "NA",
                    "email" => $payment->customer['email'],
                    "floor" => "NA",
                    "first_name" => $firstName,
                    "street" => $payment->billing_info['address_one'],
                    "building" => "NA",
                    "phone_number" => $payment->customer['mobile'],
                    "shipping_method" => "NA",
                    "postal_code" => $payment->billing_info['postcode'],
                    "city" => $payment->billing_info['city'],
                    "country" => $payment->billing_info['country'],
                    "last_name" => $lastName,
                    "state" => $payment->billing_info['state']
                ],
                "currency" => $payment->method_currency,
                "integration_id" => $gatewayData['integration_id']
            ])->json();

        $send['session'] =  $get_order['id'];
        $send['redirect'] = "https://accept.paymobsolutions.com/api/acceptance/iframes/" . $gatewayData['iframe_id'] . "?payment_token=" . $get_url_token['token'];
        return json_encode($send);
    }

    public static function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $gatewayData = \TomatoPHP\FilamentPayments\Models\PaymentGateway::where('alias', 'Paymob')->orderBy('id', 'desc')->firstOrFail();
        $gatewayParameter = $gatewayData->gateway_parameters;

        $payment = Payment::where('method_code',  $request['order'])->where('status', 0)->firstOrFail();

        $string = $request['amount_cents'] . $request['created_at'] . $request['currency'] . $request['error_occured'] . $request['has_parent_transaction'] . $request['id'] . $request['integration_id'] . $request['is_3d_secure'] . $request['is_auth'] . $request['is_capture'] . $request['is_refunded'] . $request['is_standalone_payment'] . $request['is_voided'] . $request['order'] . $request['owner'] . $request['pending'] . $request['source_data_pan'] . $request['source_data_sub_type'] . $request['source_data_type'] . $request['success'];

        if (hash_equals(hash_hmac('sha512', $string, $gatewayParameter['hmac']), $request['hmac']) && $request['success'] == "true") {
            self::paymentDataUpdate($payment);
            return redirect($payment->success_url);
        } else {
            self::paymentDataUpdate($payment, true);
            return redirect($payment->failed_url);
        }
    }

    public function integration(): array
    {
        return PaymentGateway::make('Paymob')
            ->alias('Paymob')
            ->status(true)
            ->crypto(false)
            ->gateway_parameters([
                "api_key" => "",
                "integration_id" => "",
                "iframe_id" => "",
                "hmac" => "",
            ])
            ->supported_currencies([
                PaymentCurrency::make('USD')
                    ->symbol('$')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('EUR')
                    ->symbol('€')
                    ->rate(1.11)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('EGP')
                    ->symbol('£')
                    ->rate(0.021)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('SAR')
                    ->symbol('ر.س')
                    ->rate(0.27)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('AED')
                    ->symbol('د.إ')
                    ->rate(0.27)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('JOD')
                    ->symbol('د.أ')
                    ->rate(1.41)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('KWD')
                    ->symbol('د.ك')
                    ->rate(3.28)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('OMR')
                    ->symbol('ر.ع.')
                    ->rate(2.60)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),
            ])
            ->toArray();
    }
}
