<?php

namespace TomatoPHP\FilamentPayments\Services\Drivers;

use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use TomatoPHP\FilamentPayments\Models\Payment;

class Paytabs extends Driver
{
    public static function process(Payment $payment): false|string
    {
        return false;
    }

    public static function verify(Request $request): Application|RedirectResponse|Redirector
    {
        return redirect()->to('/');
    }

    public static function secretKeys(): array
    {
        return ['server_key'];
    }

    public function integration(): array
    {
        return [];
    }
}
