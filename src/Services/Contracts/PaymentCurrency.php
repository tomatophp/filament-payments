<?php

namespace TomatoPHP\FilamentPayments\Services\Contracts;

class PaymentCurrency
{
    public string $name;
    public string $symbol;
    public float $rate = 0;
    public float $minimum_amount = 0;
    public float $maximum_amount = 0;
    public float $fixed_charge = 0;
    public float $percent_charge = 0;

    public static function make(string $name): static
    {
        return (new static())->name($name);
    }

    public function name(string $name):static
    {
        $this->name = $name;
        return $this;
    }

    public function symbol(string $symbol):static
    {
        $this->symbol = $symbol;
        return $this;
    }

    public function rate(float $rate):static
    {
        $this->rate = $rate;
        return $this;
    }

    public function minimum_amount(float $minimum_amount):static
    {
        $this->minimum_amount = $minimum_amount;
        return $this;
    }

    public function maximum_amount(float $maximum_amount):static
    {
        $this->maximum_amount = $maximum_amount;
        return $this;
    }

    public function fixed_charge(float $fixed_charge):static
    {
        $this->fixed_charge = $fixed_charge;
        return $this;
    }

    public function percent_charge(float $percent_charge):static
    {
        $this->percent_charge = $percent_charge;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'currency' => $this->name,
            'symbol' => $this->symbol,
            'rate' => $this->rate,
            'minimum_amount' => $this->minimum_amount,
            'maximum_amount' => $this->maximum_amount,
            'fixed_charge' => $this->fixed_charge,
            'percent_charge' => $this->percent_charge,
        ];
    }
}
