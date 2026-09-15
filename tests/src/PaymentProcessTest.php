<?php

use TomatoPHP\FilamentPayments\Livewire\PaymentProcess;
use TomatoPHP\FilamentPayments\Tests\Fixtures\FakeGateway;
use TomatoPHP\FilamentPayments\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    config()->set('filament-payments.drivers', [FakeGateway::class]);

    $this->user = User::factory()->create(['name' => 'Acme Store']);

    actingAs($this->user, 'testing');
});

it('renders the payment page for a pending payment', function () {
    createFakeGateway();
    $payment = createPayment($this->user);

    livewire(PaymentProcess::class, ['trx' => $payment->trx])
        ->assertSuccessful()
        ->assertSee('Fake Gateway')
        ->assertSee('Order #1001');
});

it('serves the payment page over http', function () {
    createFakeGateway();
    $payment = createPayment($this->user);

    $this->get(route('payment.index', $payment->trx))
        ->assertSuccessful()
        ->assertSee('Order #1001');
});

it('does not open the payment page for a completed payment', function () {
    createFakeGateway();
    $payment = createPayment($this->user, ['status' => 1]);

    $this->get(route('payment.index', $payment->trx))->assertNotFound();
});

it('rounds the gateway fee to two decimals', function () {
    createFakeGateway();
    // 0.2 fixed + 2.9% of 10.33 = 0.49957
    $payment = createPayment($this->user, ['amount' => 10.33]);

    $component = livewire(PaymentProcess::class, ['trx' => $payment->trx]);

    expect($component->get('payment')->charge)->toBe(0.5);
});
