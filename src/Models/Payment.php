<?php

namespace TomatoPHP\FilamentPayments\Models;

use App\Models\Team;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

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
 * @property PaymentGateway gateway
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
        'updated_at',
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

    public function account(): MorphTo
    {
        return $this->morphTo('account');
    }

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'method_id', 'id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'model_id');
    }

    /**
     * Signed link to the payment page. Always use this (or the package URL
     * generators) instead of building `/pay/{trx}` by hand.
     */
    public function payUrl(): string
    {
        return static::signedLink('payment.index', $this->trx, config('filament-payments.links.pay_expires'));
    }

    /**
     * Signed link a gateway can send the customer back to when the payment is
     * abandoned. Extra query parameters are part of the signature.
     */
    public function cancelUrl(array $query = []): string
    {
        return static::signedLink('payment.cancel', $this->trx, config('filament-payments.links.cancel_expires'), $query);
    }

    /**
     * URL of the CSRF protected form action that cancels the payment from the pay page.
     */
    public function cancelSubmitUrl(): string
    {
        return route('payment.cancel.submit', $this->trx);
    }

    protected static function signedLink(string $name, string $trx, mixed $minutes, array $query = []): string
    {
        $parameters = ['trx' => $trx, ...$query];

        return $minutes !== null
            ? URL::temporarySignedRoute($name, now()->addMinutes((int) $minutes), $parameters)
            : URL::signedRoute($name, $parameters);
    }

    /**
     * Ownership gate used by the pay page and the cancel actions. Can be turned
     * off with `filament-payments.enforce_owner` when payment links are meant
     * to be paid by someone other than the payment owner.
     */
    public function allowsAccessBy(?Authenticatable $user): bool
    {
        if (! $user) {
            return false;
        }

        return ! config('filament-payments.enforce_owner', true) || $this->isOwnedBy($user);
    }

    /**
     * Whether the given user may open or cancel this payment: the payment's
     * customer is the user, or the payment belongs to the user or to a
     * team/tenant the user can access. Payments with no owner information are open.
     */
    public function isOwnedBy(?Authenticatable $user): bool
    {
        if (! $user) {
            return false;
        }

        $email = data_get($this->customer, 'email');

        if ($this->model_type === null && $this->model_id === null && blank($email)) {
            return true;
        }

        if (filled($email) && filled($user->email ?? null) && strcasecmp((string) $email, (string) $user->email) === 0) {
            return true;
        }

        if ($this->model_type === null || $this->model_id === null) {
            return false;
        }

        $owner = rescue(fn () => $this->model, null, false);

        if (! $owner instanceof Model) {
            return false;
        }

        if ($owner->is($user)) {
            return true;
        }

        $ownerUserId = $owner->getAttribute('user_id');

        if ($ownerUserId !== null && (string) $ownerUserId === (string) $user->getAuthIdentifier()) {
            return true;
        }

        if ($user instanceof HasTenants && $user->canAccessTenant($owner)) {
            return true;
        }

        return method_exists($user, 'belongsToTeam') && (bool) $user->belongsToTeam($owner);
    }
}
