<?php

namespace TomatoPHP\FilamentPayments\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use TomatoPHP\FilamentPayments\Facades\FilamentPayments;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource;
use TomatoPHP\FilamentPayments\Models\PaymentGateway as PaymentGatewayModel;
use TomatoPHP\FilamentTranslationComponent\Components\Translation;

class PaymentGateway extends Page implements HasTable
{
    use InteractsWithTable;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected ?string $status = null;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog';

    protected string $view = 'filament-payments::pages.payment-gateway';

    public array $data = [];

    public function getTitle(): string
    {
        return trans('filament-payments::messages.payment_gateways.title');
    }

    public function mount(): void
    {
        FilamentPayments::loadDrivers();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->url(fn (): string => PaymentResource::getUrl('index'))
                ->color('danger')
                ->label(trans('filament-payments::messages.payment_gateways.back')),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(PaymentGatewayModel::query())
            ->paginated(false)
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.name')),
                TextColumn::make('alias')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.alias')),
                ToggleColumn::make('status')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.status')),
                IconColumn::make('crypto')
                    ->boolean()
                    ->label(trans('filament-payments::messages.payment_gateways.columns.crypto')),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label(trans('filament-payments::messages.payment_gateways.edit'))
                    ->tooltip(trans('filament-payments::messages.payment_gateways.edit'))
                    ->icon('heroicon-s-pencil')
                    ->iconButton()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(trans('filament-payments::messages.payment_gateways.sections.payment_gateway_data.columns.image'))
                            ->collection('image')
                            ->visibility('public')
                            ->columnSpanFull(),
                        Translation::make('name')
                            ->label(trans('filament-payments::messages.payment_gateways.sections.payment_gateway_data.columns.name'))
                            ->required()
                            ->columnSpanFull(),
                        Translation::make('description')
                            ->label(trans('filament-payments::messages.payment_gateways.sections.payment_gateway_data.columns.description'))
                            ->columnSpanFull(),
                        KeyValue::make('gateway_parameters')
                            ->label(trans('filament-payments::messages.payment_gateways.sections.gateway_parameters_data.title'))
                            ->keyLabel(trans('filament-payments::messages.payment_gateways.sections.gateway_parameters_data.columns.key'))
                            ->valueLabel(trans('filament-payments::messages.payment_gateways.sections.gateway_parameters_data.columns.value'))
                            ->editableKeys(false)
                            ->addable(false)
                            ->deletable(false),
                        Repeater::make('supported_currencies')
                            ->reorderable(false)
                            ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.title'))
                            ->schema([
                                TextInput::make('currency')
                                    ->columnSpanFull()
                                    ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.columns.currency')),
                                TextInput::make('symbol')
                                    ->label(trans('filament-payments::messages.payment_gateways.sections.supported_currencies.columns.symbol')),
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
                    ->fillForm(fn (PaymentGatewayModel $record): array => $record->toArray())
                    ->action(function (array $data, PaymentGatewayModel $record): void {
                        $record->update($data);

                        Notification::make()
                            ->title(trans('filament-payments::messages.view.gateway_updated.title'))
                            ->body(trans('filament-payments::messages.view.gateway_updated.body'))
                            ->success()
                            ->send();
                    }),
            ])
            ->searchable();
    }
}
