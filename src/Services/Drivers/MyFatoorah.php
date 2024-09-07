<?php

namespace TomatoPHP\FilamentPayments\Services\Drivers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentCurrency;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentGateway;

class MyFatoorah extends Driver
{
    public static function process(Payment $payment): false|string
    {
        $gatewayParameters = $payment->gateway->gateway_parameters;

        $config = [
            'apiKey' => $gatewayParameters['api_key'],
            'countryCode' => $payment->method_currency,
            'isTest' => (bool)$gatewayParameters['test_mode'],
        ];

        try {
            $mfObj = new MyFatoorahPayment($config);
            $postFields = [
                'NotificationOption' => 'Lnk',
                'InvoiceValue'       => $payment->amount + $payment->charge,
                'CustomerName'       => $payment->customer['name'],
                'CallBackUrl'        => route('payments.callback', 'MyFatoorah') . "?session=$payment->trx",
                'ErrorUrl'           => route('payment.cancel', $payment->trx),
            ];

            $data = $mfObj->getInvoiceURL($postFields);

            $send['session'] = $data['invoiceId'];
            $send['redirect'] = $data['invoiceURL'];
            return json_encode($send);
        } catch (Exception $e) {
            $send['error'] = true;
            $send['message'] = $e;
            return json_encode($send);
        }
    }

    public static function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $gatewayData = \TomatoPHP\FilamentPayments\Models\PaymentGateway::where('alias', 'MyFatoorah')->orderBy('id', 'desc')->firstOrFail();
        $gatewayParameters = $gatewayData->gateway_parameters;

        $sessionId = $request->get('session');

        $payment = Payment::where('trx',  $sessionId)->where('status', 0)->firstOrFail();

        $config = [
            'apiKey' => $gatewayParameters['api_key'],
            'countryCode' => $payment->method_currency,
            'isTest' => (bool)$gatewayParameters['test_mode'],
        ];

        $mfObj = new MyFatoorahPaymentStatus($config);

        $data = $mfObj->getPaymentStatus($payment->method_code, 'InvoiceId');

        if ($data->InvoiceStatus === "Paid") {
            self::paymentDataUpdate($payment);
            return redirect($payment->success_url);
        } else {
            self::paymentDataUpdate($payment, true);
            return redirect($payment->failed_url);
        }
    }

    public function integration(): array
    {
        return PaymentGateway::make('MyFatoorah')
            ->alias('MyFatoorah')
            ->status(true)
            ->crypto(false)
            ->gateway_parameters([
                "api_key" => "",
                "test_mode" => "",
            ])
            ->supported_currencies([
                PaymentCurrency::make('KWT')
                    ->symbol('ك.د')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('SAU')
                    ->symbol('ر.س')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('AED')
                    ->symbol('د.إ')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('QAT')
                    ->symbol('ر.ق')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('BHR')
                    ->symbol('ب.د')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('OMN')
                    ->symbol('ر.ع.')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('JOD')
                    ->symbol('د.أ')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),

                PaymentCurrency::make('EGY')
                    ->symbol('ج.م')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray(),
            ])
            ->toArray();
    }
}
