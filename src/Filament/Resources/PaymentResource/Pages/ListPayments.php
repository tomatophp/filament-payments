<?php

namespace TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use TomatoPHP\FilamentPayments\Filament\Pages\PaymentGateway;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource;

class ListPayments extends ManageRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('payment')
                ->url(PaymentGateway::getUrl())
                ->label(trans('filament-payments::messages.payment_gateways.title'))
                ->tooltip(trans('filament-payments::messages.payment_gateways.title'))
                ->icon('heroicon-o-cog')
                ->hiddenLabel(),
        ];
    }
}
