<?php

use TomatoPHP\FilamentPayments\Livewire\PaymentProcess;
use TomatoPHP\FilamentPayments\Services\Drivers\Paypal;
use TomatoPHP\FilamentPayments\Tests\Fixtures\FakeGateway;
use TomatoPHP\FilamentPayments\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    config()->set('filament-payments.drivers', [FakeGateway::class]);

    $this->user = User::factory()->create();

    actingAs($this->user, 'testing');
});

it('returns a 404 when the callback gateway is unknown', function () {
    $this->get(route('payments.callback', 'NotAGateway'))->assertNotFound();
});

it('does not resolve the abstract base driver from a callback', function () {
    $this->get(route('payments.callback', 'Driver'))->assertNotFound();
});

it('completes a verified payment even when the paid model no longer exists', function () {
    $payment = createPayment($this->user, ['model_id' => 999999]);

    $this->get(route('payments.callback', 'FakeGateway').'?session='.$payment->trx)
        ->assertRedirect('https://shop.example.test/success');

    expect((int) $payment->refresh()->status)->toBe(1);
});

it('stores the gateway fee rounded to two decimals when the payment is processed', function () {
    createFakeGateway();
    // 0.2 fixed + 2.9% of 10.33 = 0.49957
    $payment = createPayment($this->user, ['amount' => 10.33]);

    livewire(PaymentProcess::class, ['trx' => $payment->trx])
        ->call('process')
        ->assertRedirect('https://checkout.example.test/'.$payment->trx);

    $payment->refresh();

    expect((float) $payment->charge)->toBe(0.5)
        ->and((float) $payment->final_amount)->toBe(10.83)
        ->and($payment->method_code)->toBe('fake-session-'.$payment->trx);
});

it('formats PayPal order amounts with exactly two decimals', function () {
    expect(Paypal::formatAmount(10.33 + 0.49957))->toBe('10.83')
        ->and(Paypal::formatAmount(10.5))->toBe('10.50')
        ->and(Paypal::formatAmount(100))->toBe('100.00');
});

it('serves the payment info endpoint instead of treating "info" as a transaction id', function () {
    $this->getJson(route('payment.info'))
        ->assertStatus(422)
        ->assertJsonStructure(['errors' => ['public_key', 'id']]);
});
