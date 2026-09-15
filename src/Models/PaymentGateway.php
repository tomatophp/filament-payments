<?php

namespace TomatoPHP\FilamentPayments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use TomatoPHP\FilamentPayments\Casts\EncryptedGatewayParameters;
use TomatoPHP\FilamentPayments\Services\Drivers\Driver;

class PaymentGateway extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;

    public $translatable = [
        'name',
        'description',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'alias',
        'status',
        'gateway_parameters',
        'supported_currencies',
        'crypto',
        'configurations',
        'description',
        'sort_order',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'crypto' => 'boolean',
        'name' => 'json',
        'description' => 'json',
        'gateway_parameters' => EncryptedGatewayParameters::class,
        'supported_currencies' => 'json',
        'configurations' => 'json',
        'sort_order' => 'integer',
    ];

    /**
     * Secret gateway parameter keys, as declared by the gateway's driver.
     *
     * @return array<int, string>
     */
    public function secretKeys(): array
    {
        $driver = filled($this->alias) ? Driver::resolve((string) $this->alias) : null;

        return $driver ? $driver::secretKeys() : [];
    }

    /**
     * Merge submitted parameters into the stored ones: a blank secret keeps the stored value.
     *
     * @param  array<string, mixed>  $submitted
     * @return array<string, mixed>
     */
    public function mergeGatewayParameters(array $submitted): array
    {
        $parameters = $this->gateway_parameters ?? [];
        $secretKeys = $this->secretKeys();

        foreach ($submitted as $key => $value) {
            if (in_array($key, $secretKeys, true) && blank($value)) {
                continue;
            }

            $parameters[$key] = $value ?? '';
        }

        return $parameters;
    }
}
