<?php

namespace TomatoPHP\FilamentPayments\Controllers\Gateway\Plisio;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use TomatoPHP\FilamentPayments\Controllers\PaymentController;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Plisio\PlisioSdkLaravel\Payment as PlisioPayment;

class ProcessController extends Controller
{

    public static function process($payment)
    {
        $gatewayData = $payment->gateway->gateway_parameters;

        $plisioGateway = new PlisioPayment($gatewayData['secret_key']);

        try {

            $data = array(
                'order_name' => 'Order #' . $payment->trx,
                'order_number' => $payment->trx,
                'source_amount' => number_format($payment->amount + $payment->charge, 8, '.', ''),
                'source_currency' => $payment->method_currency,
                'cancel_url' => route('payment.cancel', $payment->trx),
                'callback_url' => route('plisio') .  "?session=$payment->trx",
                'success_url' => route('plisio') .  "?session=$payment->trx",
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

    public function verify(Request $request)
    {
        $gatewayData = PaymentGateway::where('alias', 'Plisio')->orderBy('id', 'desc')->firstOrFail();
        $gatewayParameter = $gatewayData->gateway_parameters;
        
        $sessionId = $request->get('session');

        $payment = Payment::where('trx',  $sessionId)->where('status', 0)->firstOrFail();

        $response = Http::get("https://api.plisio.net/api/v1/operations/{$payment->method_code}", [
            'api_key' => $gatewayParameter['secret_key'],
        ]);

        $response = $response->json();

        if ($response['status'] === 'success' && ($response['data']['status'] === 'completed' || $response['data']['status'] === 'mismatch')) {
            PaymentController::paymentDataUpdate($payment);

            return redirect($payment->success_url);
        }

        PaymentController::paymentDataUpdate($payment, true);
        return redirect($payment->failed_url);
    }
}
