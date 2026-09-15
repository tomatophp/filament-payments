<?php

namespace TomatoPHP\FilamentPayments\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Stores gateway parameters (API keys, secrets) encrypted with the app key.
 *
 * Rows saved before encryption was introduced hold plain JSON; they are still read as-is
 * and get encrypted the next time the gateway is saved.
 *
 * @implements CastsAttributes<array<string, mixed>, array<string, mixed>>
 */
class EncryptedGatewayParameters implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (blank($value)) {
            return [];
        }

        $plain = json_decode($value, true);

        if (is_array($plain)) {
            return $plain;
        }

        try {
            $decrypted = json_decode(Crypt::decryptString($value), true);
        } catch (DecryptException) {
            Log::warning('filament-payments: gateway parameters could not be decrypted (APP_KEY changed?). Re-enter the gateway keys.', [
                'payment_gateway_id' => $model->getKey(),
            ]);

            return [];
        }

        return is_array($decrypted) ? $decrypted : [];
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = json_decode($value, true) ?? [];
        }

        return Crypt::encryptString(json_encode($value, JSON_THROW_ON_ERROR));
    }
}
