<?php

namespace TomatoPHP\FilamentPayments\Http\Controllers\Gateway\StripeV3;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use TomatoPHP\FilamentPayments\Controllers\PaymentController;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Models\PaymentGateway;
use Stripe\Stripe;
use TomatoPHP\FilamentPayments\Services\Drivers\StripeV3;

class ProcessController extends Controller
{

    /**
     * @param $payment
     * @return false|string
     */
    public static function process($payment): false|string
    {
        $stripeData = $payment->gateway->gateway_parameters;
        $alias = $payment->gateway->alias;
        \Stripe\Stripe::setApiKey($stripeData['secret_key']);

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'unit_amount' => round($payment->amount + $payment->charge, 2) * 100,
                        'currency' => "$payment->method_currency",
                        'product_data' => [
                            'name' => setting('site_name'),
                            'description' => 'Payment with Stripe',
                        ]
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'cancel_url' => route('payment.cancel', $payment->trx),
                'success_url' => route('stripe-v3') . "?session={CHECKOUT_SESSION_ID}",
            ]);
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $send['redirect'] = $session->url;
        $send['session'] = $session->id;
        $send['publishable_key'] = $stripeData['publishable_key'];
        $payment->method_code = json_decode(json_encode($session))->id;
        $payment->save();
        return json_encode($send);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        return StripeV3::verify($request);
    }
}
