<?php

use Illuminate\Support\Facades\URL;
use TomatoPHP\FilamentPayments\Tests\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create(['name' => 'Acme Store', 'email' => 'owner@example.test']);
    $this->other = User::factory()->create(['name' => 'Someone Else', 'email' => 'other@example.test']);
    createFakeGateway();
});

it('opens the payment page for the owner through a signed link', function () {
    actingAs($this->user, 'testing');
    $payment = createPayment($this->user);

    $this->get($payment->payUrl())->assertSuccessful()->assertSee('Order #1001');
});

it('rejects the payment page without a signature', function () {
    actingAs($this->user, 'testing');
    $payment = createPayment($this->user);

    $this->get('/pay/'.$payment->trx)->assertForbidden();
});

it('rejects a tampered signed payment link', function () {
    actingAs($this->user, 'testing');
    $payment = createPayment($this->user);
    $other = createPayment($this->user, ['detail' => 'Order #2002']);

    $this->get(str_replace($payment->trx, $other->trx, $payment->payUrl()))->assertForbidden();
});

it('rejects an expired payment link', function () {
    actingAs($this->user, 'testing');
    $payment = createPayment($this->user);
    $url = $payment->payUrl();

    $this->travel(3)->days();

    $this->get($url)->assertForbidden();
});

it('denies the payment page to a user who does not own the payment', function () {
    $payment = createPayment($this->user);

    actingAs($this->other, 'testing');

    $this->get($payment->payUrl())->assertForbidden();
});

it('lets the payment customer open the page even when the model is not the user', function () {
    $payment = createPayment($this->user, [
        'customer' => ['name' => 'Other', 'email' => 'OTHER@example.test', 'mobile' => '+201000000000'],
    ]);

    actingAs($this->other, 'testing');

    $this->get($payment->payUrl())->assertSuccessful();
});

it('can turn the owner check off', function () {
    config()->set('filament-payments.enforce_owner', false);
    $payment = createPayment($this->user);

    actingAs($this->other, 'testing');

    $this->get($payment->payUrl())->assertSuccessful();
});


it('does not cancel a payment through an unsigned GET', function () {
    actingAs($this->user, 'testing');
    $payment = createPayment($this->user);

    $this->get('/pay/'.$payment->trx.'/cancel')->assertForbidden();

    expect($payment->fresh()->status)->toBe(0);
});

it('does not cancel a payment through a tampered signed GET', function () {
    actingAs($this->user, 'testing');
    $payment = createPayment($this->user);

    $this->get($payment->cancelUrl().'&x=1')->assertForbidden();

    expect($payment->fresh()->status)->toBe(0);
});

it('cancels through the signed gateway return link for the owner', function () {
    actingAs($this->user, 'testing');
    $payment = createPayment($this->user);

    $this->get($payment->cancelUrl(['session' => $payment->trx]))
        ->assertRedirect('https://shop.example.test/cancel');

    expect($payment->fresh()->status)->toBe(2);
});

it('does not cancel through the signed link for another user', function () {
    $payment = createPayment($this->user);

    actingAs($this->other, 'testing');

    $this->get($payment->cancelUrl())->assertForbidden();

    expect($payment->fresh()->status)->toBe(0);
});

it('cancels with a POST from the owner', function () {
    actingAs($this->user, 'testing');
    $payment = createPayment($this->user);

    $this->post($payment->cancelSubmitUrl())->assertRedirect('https://shop.example.test/cancel');

    expect($payment->fresh()->status)->toBe(2);
});

it('does not cancel with a POST from another user', function () {
    $payment = createPayment($this->user);

    actingAs($this->other, 'testing');

    $this->post($payment->cancelSubmitUrl())->assertForbidden();

    expect($payment->fresh()->status)->toBe(0);
});

it('renders a CSRF protected cancel form on the payment page', function () {
    actingAs($this->user, 'testing');
    $payment = createPayment($this->user);

    $this->get($payment->payUrl())
        ->assertSee('method="POST"', false)
        ->assertSee($payment->cancelSubmitUrl(), false)
        ->assertSee('name="_token"', false);
});

it('hands signed links to the callers that create payments', function () {
    $payment = createPayment($this->user);

    expect(URL::hasValidSignature(request()->create($payment->payUrl())))->toBeTrue()
        ->and(URL::hasValidSignature(request()->create($payment->cancelUrl(['session' => $payment->trx]))))->toBeTrue()
        ->and($payment->payUrl())->toContain('signature=')
        ->and($payment->cancelUrl())->toContain('signature=');
});
