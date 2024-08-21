<?php

namespace TomatoPHP\FilamentPayments\Services\Contracts;

class PaymentGateway
{
    public string $name;
    public string $alias;
    public bool $status = false;
    public bool $crypto = false;
    public array $gateway_parameters = [];
    public array $supported_currencies = [];
    public array $configurations = [];
    public ?string $description=null;
    public int $sort_order = 0;

    public static function make(string $name): static
    {
        return (new static())->name($name)->alias(str($name)->slug());
    }

    public function name(string $name):static
    {
        $this->name = $name;
        return $this;
    }

    public function alias(string $alias):static
    {
        $this->alias = $alias;
        return $this;
    }

    public function status(bool $status):static
    {
        $this->status = $status;
        return $this;
    }

    public function crypto(bool $crypto):static
    {
        $this->crypto = $crypto;
        return $this;
    }

    public function gateway_parameters(array $gateway_parameters):static
    {
        $this->gateway_parameters = $gateway_parameters;
        return $this;
    }

    public function supported_currencies(array $supported_currencies):static
    {
        $this->supported_currencies = $supported_currencies;
        return $this;
    }

    public function configurations(array $configurations):static
    {
        $this->configurations = $configurations;
        return $this;
    }

    public function description(string $description):static
    {
        $this->description = $description;
        return $this;
    }

    public function sort_order(int $sort_order):static
    {
        $this->sort_order = $sort_order;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'alias' => $this->alias,
            'status' => $this->status,
            'crypto' => $this->crypto,
            'gateway_parameters' => $this->gateway_parameters,
            'supported_currencies' => $this->supported_currencies,
            'configurations' => $this->configurations,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
        ];
    }
}
