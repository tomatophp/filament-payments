<?php

namespace TomatoPHP\FilamentPayments\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Models\PaymentGateway;

class PaymentProcess extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithFormActions;
    use InteractsWithForms;

    public $payment;
    public $gateways;
    public $userIp;
    public $selectedGateway;
    public $viewToRender;
    public $data;
    public $response;


    public function mount($trx)
    {
        $this->payment = Payment::where('trx', $trx)->where('status', 0)->firstOrFail();
        $this->userIp = request()->ip();

        $gateways = PaymentGateway::where('status', 1)->orderBy('sort_order', 'asc')->get();

        $this->gateways = $gateways->filter(function ($gateway) {
            $supportedCurrencies = collect($gateway->supported_currencies);
            return $supportedCurrencies->contains('currency', $this->payment->method_currency);
        });

        $this->form->fill([
            'gateway' => $this->gateways->first()?->id,
        ]);

        $this->calculateFee();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make(trans('filament-payments::messages.view.choose_payment_method'))
                ->schema([
                    Radio::make('gateway')
                        ->default($this->gateways->first()?->id)
                        ->live()
                        ->hiddenLabel()
                        ->afterStateUpdated(function (){
                            $this->calculateFee();
                        })
                        ->descriptions($this->gateways->pluck('description', 'id')->toArray())
                        ->options($this->gateways->pluck('name', 'id')->toArray())
                        ->view('filament-payments::forms.radio', ['gateways' => $this->gateways])
                        ->required(),
                ])
        ])->statePath('data');
    }

    public function paymentAction()
    {
        return Action::make('paymentAction')
            ->icon('heroicon-o-credit-card')
            ->label(trans('filament-payments::messages.view.choose_payment_method'))
            ->action(function(){
                $this->process();
            });
    }

    public function calculateFee()
    {
        if (!$this->data['gateway']) {
            return;
        }

        $this->selectedGateway = $this->data['gateway'];

        $selectedGateway = PaymentGateway::where('status', 1)->find($this->selectedGateway);

        if ($selectedGateway) {
            $supportedCurrencies = $selectedGateway->supported_currencies;
            $currencyCode = $this->payment->method_currency;
            $currencyData = collect($supportedCurrencies)->firstWhere('currency', $currencyCode);

            if ($currencyData) {
                $fixedFee = (float)$currencyData['fixed_charge'];
                $percentageFee = (float)$currencyData['percent_charge'];
                $feeAmount = $fixedFee + ($this->payment->amount * $percentageFee / 100);

                $this->payment->charge = $feeAmount;
                $this->payment->final_amount = $this->payment->amount + $feeAmount;
            }
        }
    }

    public function process()
    {
        $this->validate([
            'selectedGateway' => 'required',
        ]);

        $gateway = PaymentGateway::where('id', $this->selectedGateway)->where('status', 1)->firstOrFail();

        $supportedCurrencies = $gateway->supported_currencies;
        $currencyCode = $this->payment->method_currency;
        $currencyData = collect($supportedCurrencies)->firstWhere('currency', $currencyCode);

        if (!$currencyData) {
            Notification::make()
                ->title('Currency not supported')
                ->danger()
                ->send();
            return;
        }

        $fixedFee = (float)$currencyData['fixed_charge'];
        $percentageFee = (float)$currencyData['percent_charge'];
        $feeAmount = $fixedFee + ($this->payment->amount * $percentageFee / 100);

        $this->payment->update([
            'method_id' => $this->selectedGateway,
            'method_name' => $gateway->name,
            'charge' => $feeAmount,
            'rate' => $currencyData['rate'],
            'final_amount' => $this->payment->amount + $feeAmount,
        ]);

        $dirName = $gateway->alias;
        $drivers = config('filament-payments.drivers');
        $new = false;
        foreach ($drivers as $driver){
            if(str($driver)->contains($dirName)){
                $new = $driver;
                break;
            }
        }
        if(!$new){
            $new = "TomatoPHP\\FilamentPayments\\Services\\Drivers\\{$dirName}";
        }


        try {
            $data = $new::process($this->payment);
            $this->response = json_decode($data);

            if (isset($this->response->error)) {
                Log::error($this->response->message);

                Notification::make()
                    ->title('Something is wrong try again later')
                    ->danger()
                    ->send();
                return;
            }

            if (@$this->response->session) {
                $this->payment->method_code = $this->response->session;
                $this->payment->save();
            }

            if (isset($this->response->redirect)) {
                return redirect($this->response->redirect);
            } else {
                $this->viewToRender = $this->response->view;
            }
        }catch (\Exception $e) {
            dd($e);
            Log::error($e->getMessage());

            Notification::make()
                ->title('Something is wrong try again later')
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        if ($this->viewToRender) {
            return view($this->viewToRender)
                ->with([
                    'payment' => $this->payment,
                    'data' => $this->data,
                ]);
        }

        return view('filament-payments::livewire.payment-process')
            ->extends('filament-payments::layouts.payment')
            ->section('content');
    }
}
