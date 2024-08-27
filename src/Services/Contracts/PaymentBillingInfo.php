<?php

namespace TomatoPHP\FilamentPayments\Services\Contracts;

class PaymentBillingInfo
{
    public ?string $address_one=null;
    public ?string $address_two=null;
    public ?string $area=null;
    public ?string $city=null;
    public ?string $sub_city=null;
    public ?string $state=null;
    public ?string $postcode=null;
    public ?string $country=null;
    public ?string $others=null;

    public static function make(string $address_one): self
    {
        return (new static())->address_one($address_one);
    }

    public function address_one(string $address_one): self
    {
        $this->address_one = $address_one;
        return $this;
    }

    public function address_two(string $address_two): self
    {
        $this->address_two = $address_two;
        return $this;
    }

    public function area(string $area): self
    {
        $this->area = $area;
        return $this;
    }

    public function city(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function sub_city(string $sub_city): self
    {
        $this->sub_city = $sub_city;
        return $this;
    }

    public function state(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function postcode(string $postcode): self
    {
        $this->postcode = $postcode;
        return $this;
    }

    public function country(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function others(string $others): self
    {
        $this->others = $others;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'address_one' => $this->address_one,
            'address_two' => $this->address_two,
            'area' => $this->area,
            'city' => $this->city,
            'sub_city' => $this->sub_city,
            'state' => $this->state,
            'postcode' => $this->postcode,
            'country' => $this->country,
            'others' => $this->others,
        ];
    }
}
