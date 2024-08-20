<?php

namespace TomatoPHP\FilamentPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Team;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_id',
        'model_type',
        'method_id',
        'method_name',
        'method_code',
        'method_currency',
        'amount',
        'charge',
        'rate',
        'final_amount',
        'detail',
        'trx',
        'payment_try',
        'status',
        'from_api',
        'admin_feedback',
        'success_url',
        'failed_url',
        'customer',
        'shipping_info',
        'billing_info',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'customer' => 'array',
        'shipping_info' => 'array',
        'billing_info' => 'array',
        'from_api' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->trx)) {
                $model->trx = Str::random(22);
            }
        });
    }

    /**
     * @return MorphTo
     */
    public function account(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('account');
    }

    /**
     * @return MorphTo
     */
    public function model(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('model');
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'method_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'model_id');
    }
}
