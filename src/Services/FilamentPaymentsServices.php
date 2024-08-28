<?php

namespace TomatoPHP\FilamentPayments\Services;

use App\Models\Team;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Models\PaymentGateway;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentRequest;

class FilamentPaymentsServices
{
    public function pay(PaymentRequest $data, bool $json=false)
    {
        // Define the validation rules
        $rules = [
            'currency' => 'required|string|size:3|uppercase|exists:currencies,iso',
            'model' => 'required|string',
            'model_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'details' => 'required|string|max:100',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',

            'customer' => 'required|array',
            'customer.name' => 'required|string|max:255',
            'customer.email' => 'required|email',
            'customer.mobile' => 'required|string|max:20',

            'shipping_info' => 'nullable|array',
            'shipping_info.address_one' => 'nullable|string|max:255',
            'shipping_info.address_two' => 'nullable|string|max:255',
            'shipping_info.area' => 'nullable|string|max:100',
            'shipping_info.city' => 'nullable|string|max:100',
            'shipping_info.sub_city' => 'nullable|string|max:100',
            'shipping_info.state' => 'nullable|string|max:100',
            'shipping_info.postcode' => 'nullable|string|max:20',
            'shipping_info.country' => 'nullable|string|max:100',
            'shipping_info.others' => 'nullable|string|max:255',

            'billing_info' => 'nullable|array',
            'billing_info.address_one' => 'nullable|string|max:255',
            'billing_info.address_two' => 'nullable|string|max:255',
            'billing_info.area' => 'nullable|string|max:100',
            'billing_info.city' => 'nullable|string|max:100',
            'billing_info.sub_city' => 'nullable|string|max:100',
            'billing_info.state' => 'nullable|string|max:100',
            'billing_info.postcode' => 'nullable|string|max:20',
            'billing_info.country' => 'nullable|string|max:100',
            'billing_info.others' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($data->toArray(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            if($json){
                return response()->json([
                    'error' => $validator->errors()
                ], 400);
            }
            else {
                return [
                    'error' => $validator->errors()
                ];
            }
        }

        $validated = $validator->validated();


        // Create the Payment
        $payment = Payment::create([
            'model_id' => $validated['model_id'],
            'model_type' => $validated['model'],
            'method_currency' => $validated['currency'],
            'amount' => $validated['amount'],
            'detail' => $validated['details'],
            'trx' => Str::random(22),
            'status' => 0,
            'from_api' => true,
            'success_url' => $validated['success_url'],
            'failed_url' => $validated['cancel_url'],
            'customer' => $validated['customer'],
            'shipping_info' => $validated['shipping_info'] ?? [],
            'billing_info' => $validated['billing_info'] ?? [],
        ]);

        if($json){
            return response()->json([
                'status' => 'success',
                'message' => 'Payment created successfully',
                'data' => [
                    'id' => $payment->trx,
                    'url' => route('payment.index', $payment->trx),
                ]
            ], 201);
        }
        else {
            return route('payment.index', $payment->trx);
        }
    }

    public function loadDrivers(): void
    {
        $drivers = config('filament-payments.drivers');
        foreach ($drivers as $driver){
            $driver = app($driver);
            $paymentGate = $driver->integration();
            if(isset($paymentGate['alias'])){
                $payment = PaymentGateway::query()
                    ->where('alias', $paymentGate['alias'])
                    ->first();

                if(!$payment){
                    PaymentGateway::query()->create($paymentGate);
                }
            }
        }
    }
}
