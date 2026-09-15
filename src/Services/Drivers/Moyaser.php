<?php

namespace TomatoPHP\FilamentPayments\Services\Drivers;

use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use TomatoPHP\FilamentPayments\Models\Payment;

class Moyaser extends Driver
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
        return ['secret_key'];
    }

    public function integration(): array
    {
        return [];
    }
}
