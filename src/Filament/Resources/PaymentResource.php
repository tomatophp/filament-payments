<?php

namespace TomatoPHP\FilamentPayments\Filament\Resources;

use Illuminate\Support\Carbon;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource\Pages;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource\RelationManagers;
use TomatoPHP\FilamentPayments\Models\Payment;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return trans('filament-payments::messages.title');
    }

    public static function getNavigationLabel(): string
    {
        return trans('filament-payments::messages.payments.title');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('filament-payments::messages.payments.title');
    }

    public static function getLabel(): ?string
    {
        return trans('filament-payments::messages.payments.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trx')
                    ->label(trans('filament-payments::messages.payments.columns.transaction_id'))
                    ->sortable(),
                TextColumn::make('method_name')
                    ->label(trans('filament-payments::messages.payments.columns.method_name'))
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(trans('filament-payments::messages.payments.columns.amount'))
                    ->formatStateUsing(function (Payment $record) {
                        return  Number::currency($record->amount, in: $record->method_currency) . " + " . Number::currency($record->charge, in: $record->method_currency) . '<br>' . Number::currency(($record->amount + $record->charge), in: $record->method_currency);
                    })->html(),

                TextColumn::make('rate')
                    ->label(trans('filament-payments::messages.payments.columns.conversion'))
                    ->formatStateUsing(function (Payment $record) {
                        return  Number::currency(1, in: 'USD') . " = " . Number::currency($record->rate, in: $record->method_currency) . '<br>' . Number::currency($record->final_amount, in: 'USD');
                    })->html(),

                TextColumn::make('status')
                    ->label(trans('filament-payments::messages.payments.columns.status'))
                    ->badge()
                    ->state(fn($record) => match ($record->status) {
                        0 => trans('filament-payments::messages.payments.columns.processing'),
                        1 => trans('filament-payments::messages.payments.columns.completed'),
                        2 => trans('filament-payments::messages.payments.columns.cancelled'),
                        default => trans('filament-payments::messages.payments.columns.initiated'),
                    })
                    ->icon(fn($record) => match ($record->status) {
                        0 => 'heroicon-o-clock',
                        1 => 'heroicon-s-check-circle',
                        2 => 'heroicon-s-x-circle',
                        default => 'heroicon-s-x-circle',
                    })
                    ->color(fn($record) => match ($record->status) {
                        0 => 'info',
                        1 => 'success',
                        2 => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(trans('filament-payments::messages.payments.columns.date'))
                    ->dateTime('d/m/Y h:iA')
                    ->description(fn ($record): string => Carbon::parse($record->created_at)->diffForHumans()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(trans('filament-payments::messages.payments.columns.status'))
                    ->options([
                        0 => trans('filament-payments::messages.payments.columns.processing'),
                        1 => trans('filament-payments::messages.payments.columns.completed'),
                        2 => trans('filament-payments::messages.payments.columns.cancelled'),
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('status')
                    ->label(trans('filament-payments::messages.payments.columns.status')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\ViewAction::make(),
            ])
            ->searchable();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make(trans('filament-payments::messages.payments.columns.details'))
                            ->schema([
                                TextEntry::make('status')
                                    ->label(trans('filament-payments::messages.payments.columns.status'))
                                    ->badge()
                                    ->state(fn($record) => match ($record->status) {
                                        0 => trans('filament-payments::messages.payments.columns.processing'),
                                        1 => trans('filament-payments::messages.payments.columns.completed'),
                                        2 => trans('filament-payments::messages.payments.columns.cancelled'),
                                        default => trans('filament-payments::messages.payments.columns.initiated'),
                                    })
                                    ->icon(fn($record) => match ($record->status) {
                                        0 => 'heroicon-o-clock',
                                        1 => 'heroicon-s-check-circle',
                                        2 => 'heroicon-s-x-circle',
                                        default => 'heroicon-s-x-circle',
                                    })
                                    ->color(fn($record) => match ($record->status) {
                                        0 => 'info',
                                        1 => 'success',
                                        2 => 'danger',
                                        default => 'secondary',
                                    }),
                                TextEntry::make('created_at')
                                    ->label(trans('filament-payments::messages.payments.columns.date'))
                                    ->dateTime(),
                                TextEntry::make('trx')
                                    ->label(trans('filament-payments::messages.payments.columns.transaction_number')),
                                TextEntry::make('account.username')
                                    ->label(trans('filament-payments::messages.payments.columns.username')),
                                TextEntry::make('method_name')
                                    ->label(trans('filament-payments::messages.payments.columns.method_name')),
                                TextEntry::make('method_code')
                                    ->label(trans('filament-payments::messages.payments.columns.method_code')),
                                TextEntry::make('amount')
                                    ->label(trans('filament-payments::messages.payments.columns.amount'))
                                    ->money(function($record){
                                        return $record->method_currency ?? 'USD';
                                    }, locale: 'en'),
                                TextEntry::make('charge')
                                    ->label(trans('filament-payments::messages.payments.columns.charge'))
                                    ->money(function($record){
                                        return $record->method_currency ?? 'USD';
                                    }, locale: 'en'),
                                TextEntry::make('rate')
                                    ->label(trans('filament-payments::messages.payments.columns.rate'))
                                    ->formatStateUsing(function (Payment $record) {
                                        return Number::currency(1, in: 'USD') . " = " .
                                            Number::currency($record->rate, in: $record->method_currency);
                                    })
                                    ->html(),
                                TextEntry::make('final_amount')
                                    ->label(trans('filament-payments::messages.payments.columns.after_rate_conversion'))
                                    ->money(function($record){
                                        return $record->method_currency ?? 'USD';
                                    }, locale: 'en'),
                            ])
                            ->columns(2),
                        Tab::make(trans('filament-payments::messages.payments.columns.customer'))
                            ->schema([
                                TextEntry::make('customer.name')
                                    ->label(trans('filament-payments::messages.payments.columns.name'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $customerData = $record->customer;
                                        return $customerData['name'] ?? 'N/A';
                                    }),
                                TextEntry::make('customer.email')
                                    ->label(trans('filament-payments::messages.payments.columns.email'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $customerData = $record->customer;
                                        return $customerData['email'] ?? 'N/A';
                                    }),
                                TextEntry::make('customer.mobile')
                                    ->label(trans('filament-payments::messages.payments.columns.mobile'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $customerData = $record->customer;
                                        return $customerData['mobile'] ?? 'N/A';
                                    }),
                            ])
                            ->columns(2),
                        Tab::make(trans('filament-payments::messages.payments.columns.shipping'))
                            ->schema([
                                TextEntry::make('shipping_info.address_one')
                                    ->label(trans('filament-payments::messages.payments.columns.address_one'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $shippingInfoData = $record->shipping_info;
                                        return $shippingInfoData['address_one'] ?? 'N/A';
                                    }),
                                TextEntry::make('shipping_info.address_two')
                                    ->label(trans('filament-payments::messages.payments.columns.address_two'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $shippingInfoData = $record->shipping_info;
                                        return $shippingInfoData['address_two'] ?? 'N/A';
                                    }),
                                TextEntry::make('shipping_info.area')
                                    ->label(trans('filament-payments::messages.payments.columns.area'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $shippingInfoData = $record->shipping_info;
                                        return $shippingInfoData['area'] ?? 'N/A';
                                    }),
                                TextEntry::make('shipping_info.city')
                                    ->label(trans('filament-payments::messages.payments.columns.city'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $shippingInfoData = $record->shipping_info;
                                        return $shippingInfoData['city'] ?? 'N/A';
                                    }),
                                TextEntry::make('shipping_info.sub_city')
                                    ->label(trans('filament-payments::messages.payments.columns.sub_city'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $shippingInfoData = $record->shipping_info;
                                        return $shippingInfoData['sub_city'] ?? 'N/A';
                                    }),
                                TextEntry::make('shipping_info.state')
                                    ->label(trans('filament-payments::messages.payments.columns.state'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $shippingInfoData = $record->shipping_info;
                                        return $shippingInfoData['state'] ?? 'N/A';
                                    }),
                                TextEntry::make('shipping_info.postcode')
                                    ->label(trans('filament-payments::messages.payments.columns.postcode'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $shippingInfoData = $record->shipping_info;
                                        return $shippingInfoData['postcode'] ?? 'N/A';
                                    }),
                                TextEntry::make('shipping_info.country')
                                    ->label(trans('filament-payments::messages.payments.columns.country'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $shippingInfoData = $record->shipping_info;
                                        return $shippingInfoData['country'] ?? 'N/A';
                                    }),
                            ])
                            ->columns(2),
                        Tab::make(trans('filament-payments::messages.payments.columns.billing'))
                            ->schema([
                                TextEntry::make('billing_info.address_one')
                                    ->label(trans('filament-payments::messages.payments.columns.address_one'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $billingInfoData = $record->billing_info;
                                        return $billingInfoData['address_one'] ?? 'N/A';
                                    }),
                                TextEntry::make('billing_info.address_two')
                                    ->label(trans('filament-payments::messages.payments.columns.address_two'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $billingInfoData = $record->billing_info;
                                        return $billingInfoData['address_two'] ?? 'N/A';
                                    }),
                                TextEntry::make('billing_info.area')
                                    ->label(trans('filament-payments::messages.payments.columns.area'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $billingInfoData = $record->billing_info;
                                        return $billingInfoData['area'] ?? 'N/A';
                                    }),
                                TextEntry::make('billing_info.city')
                                    ->label(trans('filament-payments::messages.payments.columns.city'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $billingInfoData = $record->billing_info;
                                        return $billingInfoData['city'] ?? 'N/A';
                                    }),
                                TextEntry::make('billing_info.sub_city')
                                    ->label(trans('filament-payments::messages.payments.columns.sub_city'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $billingInfoData = $record->billing_info;
                                        return $billingInfoData['sub_city'] ?? 'N/A';
                                    }),
                                TextEntry::make('billing_info.state')
                                    ->label(trans('filament-payments::messages.payments.columns.state'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $billingInfoData = $record->billing_info;
                                        return $billingInfoData['state'] ?? 'N/A';
                                    }),
                                TextEntry::make('billing_info.postcode')
                                    ->label(trans('filament-payments::messages.payments.columns.postcode'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $billingInfoData = $record->billing_info;
                                        return $billingInfoData['postcode'] ?? 'N/A';
                                    }),
                                TextEntry::make('billing_info.country')
                                    ->label(trans('filament-payments::messages.payments.columns.country'))
                                    ->formatStateUsing(function (Payment $record) {
                                        $billingInfoData = $record->billing_info;
                                        return $billingInfoData['country'] ?? 'N/A';
                                    }),
                            ])
                            ->columns(2),
                    ])
                    ->contained(false)
            ])
            ->columns(1);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
        ];
    }
}
