<?php

namespace TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource\Pages;

use TomatoPHP\FilamentPayments\Filament\Pages\PaymentGateway;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('payment')
                ->url(PaymentGateway::getUrl())
                ->label('Payment Gateway')
                ->tooltip('Payment Gateway')
                ->icon('heroicon-o-cog')
                ->hiddenLabel(),
        ];
    }
}
