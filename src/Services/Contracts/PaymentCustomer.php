<?php

namespace TomatoPHP\FilamentPayments\Services\Contracts;

class PaymentCustomer
{
    public ?string $name=null;
    public ?string $email=null;
    public ?string $mobile=null;

    public static function make(string $name): self
    {
        return (new static())->name($name);
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function email(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function mobile(string $mobile): self
    {
        $this->mobile = $mobile;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
        ];
    }
}
