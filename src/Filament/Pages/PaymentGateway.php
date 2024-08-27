<?php

namespace TomatoPHP\FilamentPayments\Filament\Pages;


use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Filament\Pages\SettingsPage;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use TomatoPHP\FilamentIcons\Components\IconPicker;
use TomatoPHP\FilamentPayments\Facades\FilamentPayments;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource;
use TomatoPHP\FilamentPayments\Models\PaymentGateway as PaymentGatewayModel;
use TomatoPHP\FilamentTranslationComponent\Components\Translation;

class PaymentGateway extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected ?string $status = null;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = "filament-payments::pages.payment-gateway";

    public array $data = [];

    public function mount(): void
    {
        FilamentPayments::loadDrivers();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->action(fn()=> redirect()->to(PaymentResource::getUrl('index')))
                ->color('danger')
                ->label("Back"),
        ];
    }

    public function getTitle(): string
    {
        return trans("Payment Gateway");
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(PaymentGatewayModel::query())
            ->paginated(false)
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.name')),
                Tables\Columns\TextColumn::make('alias')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.alias')),
                Tables\Columns\ToggleColumn::make('status')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.status')),
                Tables\Columns\BooleanColumn::make('crypto')
                    ->label(trans('filament-payments::messages.payment_gateways.columns.crypto')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Edit Gateway')
                    ->tooltip('Edit Gateway')
                    ->icon('heroicon-s-pencil')
                    ->iconButton()
                    ->form([
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
                        KeyValue::make('gateway_parameters')
                            ->label(trans('filament-payments::messages.payment_gateways.sections.gateway_parameters_data.title'))
                            ->keyLabel(trans('filament-payments::messages.payment_gateways.sections.gateway_parameters_data.columns.key'))
                            ->valueLabel(trans('filament-payments::messages.payment_gateways.sections.gateway_parameters_data.columns.value'))
                            ->editableKeys(false)
                            ->addable(false)
                            ->deletable(false),
                        Repeater::make('supported_currencies')
                            ->addable(false)
                            ->deletable(false)
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
                    ->fillForm(fn($record) => $record->toArray())
                    ->action(function (array $data, $record){
                        $record->update($data);
                        Notification::make()
                            ->title('Gateway Updated')
                            ->body('Gateway has been updated successfully')
                            ->send();
                    }),
            ])
            ->bulkActions([
                //
            ])
            ->searchable();
    }
}
