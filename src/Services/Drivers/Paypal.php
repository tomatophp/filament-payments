<?php

namespace TomatoPHP\FilamentPayments\Services\Drivers;

use Illuminate\Http\Request;
use TomatoPHP\FilamentPayments\Models\Payment;

class Paypal extends Driver
{
    public static function process(Payment $payment): false|string
    {
        return false;
    }

    public static function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        return redirect()->to('/');
    }

    public function integration(): array
    {
        return [];
    }
}
