<?php

use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use TomatoPHP\FilamentPayments\Filament\Pages\PaymentGateway;
use TomatoPHP\FilamentPayments\Models\PaymentGateway as PaymentGatewayModel;
use TomatoPHP\FilamentPayments\Services\Drivers\Paypal;
use TomatoPHP\FilamentPayments\Services\Drivers\StripeV3;
use TomatoPHP\FilamentPayments\Tests\Fixtures\FakeGateway;
use TomatoPHP\FilamentPayments\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    config()->set('filament-payments.drivers', [FakeGateway::class]);

    actingAs(User::factory()->create());

    $this->gateway = createFakeGateway([
        'gateway_parameters' => ['secret_key' => 'sk_live_SUPERSECRET', 'public_key' => 'pk_visible'],
    ]);
});

function rawGatewayParameters(PaymentGatewayModel $gateway): string
{
    return (string) DB::table('payment_gateways')->where('id', $gateway->id)->value('gateway_parameters');
}

it('never sends a stored secret to the browser', function () {
    $component = livewire(PaymentGateway::class)
        ->mountAction(TestAction::make('edit')->table($this->gateway))
        ->assertSuccessful();

    expect(json_encode($component->snapshot))->toContain('pk_visible')->not->toContain('sk_live_SUPERSECRET')
        ->and($component->html())->not->toContain('sk_live_SUPERSECRET')
        ->and(json_encode($component->get('mountedActions')))->not->toContain('sk_live_SUPERSECRET');
});

it('shows non-secret values and renders secrets as password inputs', function () {
    $component = livewire(PaymentGateway::class)
        ->mountAction(TestAction::make('edit')->table($this->gateway))
        ->assertFormFieldExists(
            'gateway_parameters.secret_key',
            'mountedActionSchema0',
            fn (TextInput $field): bool => $field->isPassword()
                && $field->getHint() === 'Leave blank to keep the current value',
        )
        ->assertFormFieldExists(
            'gateway_parameters.public_key',
            'mountedActionSchema0',
            fn (TextInput $field): bool => ! $field->isPassword(),
        );

    expect(json_encode($component->get('mountedActions')))->toContain('pk_visible');
});

it('keeps the stored secret when the secret field is left blank', function () {
    livewire(PaymentGateway::class)
        ->mountAction(TestAction::make('edit')->table($this->gateway))
        ->setActionData(['gateway_parameters' => ['secret_key' => '', 'public_key' => 'pk_changed']])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect($this->gateway->refresh()->gateway_parameters)
        ->toBe(['secret_key' => 'sk_live_SUPERSECRET', 'public_key' => 'pk_changed']);
});

it('replaces the stored secret when a new one is entered and stores it encrypted', function () {
    livewire(PaymentGateway::class)
        ->mountAction(TestAction::make('edit')->table($this->gateway))
        ->setActionData(['gateway_parameters' => ['secret_key' => 'sk_live_ROTATED', 'public_key' => 'pk_visible']])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect($this->gateway->refresh()->gateway_parameters['secret_key'])->toBe('sk_live_ROTATED')
        ->and(rawGatewayParameters($this->gateway))->not->toContain('sk_live_ROTATED')->not->toContain('pk_visible');
});

it('still reads gateway parameters saved as plain json before encryption', function () {
    DB::table('payment_gateways')->where('id', $this->gateway->id)
        ->update(['gateway_parameters' => json_encode(['secret_key' => 'sk_legacy', 'public_key' => 'pk_legacy'])]);

    expect($this->gateway->refresh()->gateway_parameters)->toBe(['secret_key' => 'sk_legacy', 'public_key' => 'pk_legacy']);
});

it('declares the secret keys of the built-in drivers', function () {
    expect(StripeV3::secretKeys())->toBe(['secret_key'])
        ->and(Paypal::secretKeys())->toBe(['secret']);
});
