<?php

namespace TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource\Pages;

use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
