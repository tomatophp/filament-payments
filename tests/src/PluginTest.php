<?php

use Filament\Facades\Filament;
use TomatoPHP\FilamentPayments\Filament\Pages\PaymentGateway;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource;
use TomatoPHP\FilamentPayments\FilamentPaymentsPlugin;

it('registers plugin', function () {
    $panel = Filament::getCurrentOrDefaultPanel();

    $panel->plugins([
        FilamentPaymentsPlugin::make(),
    ]);

    expect($panel->getPlugin('filament-payments'))
        ->toBeInstanceOf(FilamentPaymentsPlugin::class);
});

it('registers the payment resource and the gateways page', function () {
    $panel = Filament::getCurrentOrDefaultPanel();

    expect($panel->getResources())->toContain(PaymentResource::class)
        ->and($panel->getPages())->toContain(PaymentGateway::class);
});
