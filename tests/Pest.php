<?php

use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Models\PaymentGateway;
use TomatoPHP\FilamentPayments\Tests\Fixtures\FakeGateway;
use TomatoPHP\FilamentPayments\Tests\Models\User;
use TomatoPHP\FilamentPayments\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function createFakeGateway(array $attributes = []): PaymentGateway
{
    return PaymentGateway::query()->create([
        ...(new FakeGateway)->integration(),
        'sort_order' => 1,
        ...$attributes,
    ]);
}

function createPayment(User $user, array $attributes = []): Payment
{
    return Payment::query()->create([
        'model_id' => $user->id,
        'model_type' => User::class,
        'method_currency' => 'USD',
        'amount' => 100,
        'charge' => 0,
        'rate' => 1,
        'final_amount' => 100,
        'detail' => 'Order #1001',
        'status' => 0,
        'from_api' => false,
        'success_url' => 'https://shop.example.test/success',
        'failed_url' => 'https://shop.example.test/cancel',
        'customer' => ['name' => 'John Doe', 'email' => 'john@example.test', 'mobile' => '+201000000000'],
        'shipping_info' => ['address_one' => '123 Main St', 'city' => 'Cairo', 'country' => 'EG'],
        'billing_info' => ['address_one' => '123 Main St', 'city' => 'Cairo', 'country' => 'EG'],
        ...$attributes,
    ]);
}
