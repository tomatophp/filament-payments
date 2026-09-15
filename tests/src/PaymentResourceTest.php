<?php

use Filament\Actions\Testing\TestAction;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource\Pages\ListPayments;
use TomatoPHP\FilamentPayments\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->create();

    actingAs($this->user);
});

it('can render the payments page', function () {
    createPayment($this->user);

    $this->get(PaymentResource::getUrl('index'))->assertSuccessful();
});

it('can list payments', function () {
    $completed = createPayment($this->user, ['status' => 1, 'method_name' => 'Fake Gateway']);
    $processing = createPayment($this->user, ['status' => 0]);

    livewire(ListPayments::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$completed, $processing])
        ->assertCanRenderTableColumn('trx')
        ->assertCanRenderTableColumn('amount')
        ->assertCanRenderTableColumn('status');
});

it('can filter payments by status', function () {
    $completed = createPayment($this->user, ['status' => 1]);
    $cancelled = createPayment($this->user, ['status' => 2]);

    livewire(ListPayments::class)
        ->filterTable('status', 1)
        ->assertCanSeeTableRecords([$completed])
        ->assertCanNotSeeTableRecords([$cancelled]);
});

it('can view a payment', function () {
    $payment = createPayment($this->user, ['status' => 1, 'method_name' => 'Fake Gateway']);

    livewire(ListPayments::class)
        ->mountAction(TestAction::make('view')->table($payment))
        ->assertSuccessful()
        ->assertSee($payment->trx)
        ->assertSee('Fake Gateway');
});
