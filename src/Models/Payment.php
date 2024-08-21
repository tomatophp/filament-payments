<?php

namespace TomatoPHP\FilamentPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Team;

/**
 * @property int model_id
 * @property string model_type
 * @property int method_id
 * @property string method_name
 * @property string method_code
 * @property string method_currency
 * @property float amount
 * @property float charge
 * @property float rate
 * @property float final_amount
 * @property string detail
 * @property string trx
 * @property string payment_try
 * @property string status
 * @property bool from_api
 * @property string admin_feedback
 * @property string success_url
 * @property string failed_url
 * @property array customer
 * @property array shipping_info
 * @property array billing_info
 * @property string created_at
 * @property string updated_at
 *
 */
class Payment extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
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

    /**
     * @var string[]
     */
    protected $casts = [
        'customer' => 'array',
        'shipping_info' => 'array',
        'billing_info' => 'array',
        'from_api' => 'boolean',
    ];

    /**
     * @return void
     */
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gateway(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'method_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class, 'model_id');
    }
}
