### v1.0.21

- Payment page and cancel links are now signed; upgrade recommended
- **Breaking:** `/pay/{trx}` and `GET /pay/{trx}/cancel` require a valid signature, so links must come from `FilamentPayments::pay()`, `$payment->payUrl()` or `$payment->cancelUrl()`. Links expire (`filament-payments.links`)
- Cancelling from the payment page is a CSRF protected `POST /pay/{trx}/cancel` (`payment.cancel.submit`); the signed GET is kept for gateway redirects
- The payment page and cancel actions are limited to the payment owner (`filament-payments.enforce_owner`)
- Gateway drivers use signed cancel links (PayPal keeps its `session` parameter inside the signature)
