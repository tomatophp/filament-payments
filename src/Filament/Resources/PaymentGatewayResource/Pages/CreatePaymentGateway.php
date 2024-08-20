<?php

namespace TomatoPHP\FilamentPayments\Filament\Resources\PaymentGatewayResource\Pages;

use TomatoPHP\FilamentPayments\Filament\Resources\PaymentGatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentGateway extends CreateRecord
{
    protected static string $resource = PaymentGatewayResource::class;
}
