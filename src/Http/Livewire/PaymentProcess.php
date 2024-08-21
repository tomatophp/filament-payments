<?php

namespace TomatoPHP\FilamentPayments\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Models\PaymentGateway;

class PaymentProcess extends Component
{
    public $payment;
    public $gateways;
    public $userIp;
    public $selectedGateway;

    public function mount($trx)
    {
        $this->payment = Payment::where('trx', $trx)->where('status', 0)->firstOrFail();
        $this->userIp = request()->ip();

        $gateways = PaymentGateway::where('status', 1)->orderBy('sort_order', 'asc')->get();

        $this->gateways = $gateways->filter(function ($gateway) {
            $supportedCurrencies = collect($gateway->supported_currencies);
            return $supportedCurrencies->contains('currency', $this->payment->method_currency);
        });
    }

    public function calculateFee()
    {
        if (!$this->selectedGateway) {
            return;
        }

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

        $new = app(config('filament-payments.path'). "\\".$gateway->alias);

        $data = $new::process($this->payment);
        $data = json_decode($data);

        if (isset($data->error)) {
            Log::error($data->message);

            Notification::make()
                ->title('Something is wrong try again later')
                ->danger()
                ->send();
            return;
        }

        if (@$data->session) {
            $this->payment->method_code = $data->session;
            $this->payment->save();
        }

        if (isset($data->redirect)) {
            return redirect($data->redirect);
        }

        $payment = $this->payment;

        return view("$data->view", compact('data', 'payment'));
    }

    public function render()
    {
        return view('filament-payments::livewire.payment-process')
            ->extends('filament-payments::layouts.payment')
            ->section('content');
    }
}
