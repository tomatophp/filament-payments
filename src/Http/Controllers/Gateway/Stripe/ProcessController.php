<?php

namespace TomatoPHP\FilamentPayments\Controllers\Gateway\Stripe;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use TomatoPHP\FilamentPayments\Controllers\PaymentController;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Models\PaymentGateway;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class ProcessController extends Controller
{

    public static function process($payment)
    {
        $stripeData = $payment->gateway->gateway_parameters;
        \Stripe\Stripe::setApiKey($stripeData['secret_key']);

        try {
            $session = \Stripe\PaymentIntent::create([
                'amount' => round($payment->amount + $payment->charge, 2) * 100,
                'currency' => "$payment->method_currency",
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $send['view'] = 'filament-payments::payment.StripeEmbedded';
        $send['session'] = $session->id;
        $send['client_secret'] = $session->client_secret;
        $send['publishable_key'] = $stripeData['publishable_key'];
        $send['success_url'] = route('stripe-embedded');

        $payment->method_code = json_decode(json_encode($session))->id;
        $payment->save();
        return json_encode($send);
    }


    public function verify(Request $request)
    {
        $StripeAcc = PaymentGateway::where('alias', 'Stripe')->orderBy('id', 'desc')->firstOrFail();
        $gateway_parameter = $StripeAcc->gateway_parameters;

        Stripe::setApiKey($gateway_parameter['secret_key']);
        $stripeSession = $request->get('payment_intent');
        $session = PaymentIntent::retrieve($stripeSession);

        $payment = Payment::where('method_code',  $session->id)->where('status', 0)->firstOrFail();

        if ($session->status === 'succeeded') {
            PaymentController::paymentDataUpdate($payment);

            return redirect($payment->success_url);
        }

        PaymentController::paymentDataUpdate($payment, true);
        return redirect($payment->failed_url);
    }
}
