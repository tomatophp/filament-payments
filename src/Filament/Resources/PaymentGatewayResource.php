<?php

namespace TomatoPHP\FilamentPayments\Filament\Resources;

use Filament\Forms\Components\Grid;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentGatewayResource\Pages;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentGatewayResource\RelationManagers;
use TomatoPHP\FilamentPayments\Models\PaymentGateway;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use TomatoPHP\FilamentTranslationComponent\Components\Translation;

class PaymentGatewayResource extends Resource
{
    protected static ?string $model = PaymentGateway::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return trans('filament-payments::messages.title');
    }

    public static function getNavigationLabel(): string
    {
        return trans('filament-payments::messages.payment_gateways.title');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('filament-payments::messages.payment_gateways.title');
    }

    public static function getLabel(): ?string
    {
        return trans('filament-payments::messages.payment_gateways.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'lg' => 12,
                ])->schema([
                    Section::make(trans('filament-payments::messages.payment_gateways.sections.payment_gateway_data.title'))
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('image')
                                ->label(trans('filament-payments::messages.payment_gateways.sections.payment_gateway_data.columns.image'))
                                ->collection('image')
                                ->columnSpanFull(),
                            Translation::make('name')
                                ->label(trans('filament-payments::messages.payment_gateways.sections.payment_gateway_data.columns.name'))
                                ->required()
                                ->columnSpanFull(),
                            Textarea::make('description')
                                ->label(trans('filament-payments::messages.payment_gateways.sections.payment_gateway_data.columns.description'))
                                ->autosize()
                                ->columnSpanFull(),
                            Toggle::make('status')
                                ->label(trans('filament-payments::messages.payment_gateways.sections.payment_gateway_data.columns.status'))
                                ->default(true),
                        ])
                        ->columnSpan(6)
                        ->collapsible()
                        ->collapsed(fn($record) => $record),

                    Section::make(trans('filament-payments::messages.payment_gateways.sections.gateway_parameters_data.title'))
                        ->schema([
                            KeyValue::make('gateway_parameters')
                                ->label(trans('filament-payments::messages.payment_gateways.sections.gateway_parameters_data.title'))
                                ->keyLabel(trans('filament-payments::messages.payment_gateways.sections.gateway_parameters_data.columns.key'))
                                ->valueLabel(trans('filament-payments::messages.payment_gateways.sections.gateway_parameters_data.columns.value'))
                                ->editableKeys(false)
                                ->addable(false)
                                ->deletable(false),
                        ])
                        ->columnSpan(6)
                        ->collapsible()
                        ->collapsed(fn($record) => $record),
                ]),
                Section::make(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.title'))
                    ->schema([
                        Repeater::make('supported_currencies')
                            ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.title'))
                            ->schema([
                                TextInput::make('currency')
                                    ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.columns.currency'))
                                    ->required(),
                                TextInput::make('symbol')
                                    ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.columns.symbol'))
                                    ->required(),
                                TextInput::make('rate')
                                    ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.columns.rate'))
                                    ->required(),
                                TextInput::make('minimum_amount')
                                    ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.columns.minimum_amount'))
                                    ->required(),
                                TextInput::make('maximum_amount')
                                    ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.columns.maximum_amount'))
                                    ->required(),
                                TextInput::make('fixed_charge')
                                    ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.columns.fixed_charge'))
                                    ->required(),
                                TextInput::make('percent_charge')
                                    ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.columns.percent_charge'))
                                    ->required(),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible()
                    ->collapsed(fn($record) => $record),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.name')),
                Tables\Columns\TextColumn::make('alias')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.alias')),
                Tables\Columns\BooleanColumn::make('status')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.status')),
                Tables\Columns\BooleanColumn::make('crypto')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.crypto')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make(trans('filament-payments::messages.payment_gateways.columns.toggle_status'))
                    ->accessSelectedRecords()
                    ->action(function (Model $record) {
                        $record->status = !$record->status;
                        $record->save();
                    })
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                //
            ])
            ->searchable();
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
            'index' => Pages\ListPaymentGateways::route('/'),
            'edit' => Pages\EditPaymentGateway::route('/{record}/edit'),
        ];
    }
}
