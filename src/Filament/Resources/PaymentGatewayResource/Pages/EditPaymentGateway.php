<?php

namespace TomatoPHP\FilamentPayments\Filament\Resources\PaymentGatewayResource\Pages;

use TomatoPHP\FilamentPayments\Filament\Resources\PaymentGatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentGateway extends EditRecord
{
    protected static string $resource = PaymentGatewayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
