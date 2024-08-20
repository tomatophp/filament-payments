<?php

namespace TomatoPHP\FilamentPayments\Filament\Resources\PaymentGatewayResource\Pages;

use TomatoPHP\FilamentPayments\Filament\Resources\PaymentGatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentGateways extends ListRecords
{
    protected static string $resource = PaymentGatewayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
