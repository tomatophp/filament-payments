<?php

use Filament\Actions\Testing\TestAction;
use TomatoPHP\FilamentPayments\Filament\Pages\PaymentGateway;
use TomatoPHP\FilamentPayments\Models\PaymentGateway as PaymentGatewayModel;
use TomatoPHP\FilamentPayments\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can render the payment gateways page', function () {
    $this->get(PaymentGateway::getUrl())->assertSuccessful();
});

it('seeds every configured driver as a gateway when the page opens', function () {
    livewire(PaymentGateway::class)->assertSuccessful();

    // Fawery, Moyaser, Payfort and Paytabs are placeholders without an integration yet.
    expect(PaymentGatewayModel::query()->pluck('alias')->sort()->values()->all())
        ->toBe(['Cryptomus', 'Paymob', 'Paypal', 'Plisio', 'StripeV3', 'Tap']);
});

it('lists payment gateways', function () {
    $gateway = createFakeGateway();

    livewire(PaymentGateway::class)
        ->assertCanSeeTableRecords([$gateway])
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('crypto');
});

it('can edit a payment gateway', function () {
    $gateway = createFakeGateway();

    livewire(PaymentGateway::class)
        ->mountAction(TestAction::make('edit')->table($gateway))
        ->assertSuccessful()
        ->setActionData([
            'gateway_parameters' => ['secret_key' => 'sk_test_updated'],
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect($gateway->refresh()->gateway_parameters)->toBe(['secret_key' => 'sk_test_updated', 'public_key' => '']);
});
