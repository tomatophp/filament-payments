# Changelog

### v5.0.1

- point `homepage` at the plugin page on tomatophp.com

All notable changes to `filament-payments` will be documented in this file.

## v5.0.0

- Support Filament v5, Livewire 4 and Laravel 12 / 13 (PHP 8.2+).
- Filament v3 code is kept on the `v3` branch.
- Merged community fixes from #14 (N3XT0R): gateway fee and PayPal amounts rounded to 2 decimals, `verify` typo fixes, no more `dd()` in the payment page, translatable messages (en / ar / de), configurable auth guard (`filament-payments.guard`) and optional Filament Subscriptions middleware.
- Payment gateway edit form, custom radio view and Livewire payment page ported to the Filament v5 schema APIs.
- The payment controller no longer depends on the host app's `App\Http\Controllers\Controller`.
- Added a Pest test suite (Testbench) and a GitHub Actions workflow.
- **Breaking:** the payment page (`/pay/{trx}`) is now protected by `auth:` + `config('filament-payments.guard')`, which defaults to your app's default guard. It used to be hard-coded to `auth:accounts`; set `'guard' => 'accounts'` in `config/filament-payments.php` to keep the old behaviour.
- Gateway secrets are no longer sent to the browser: secret parameters (declared per driver with `Driver::secretKeys()`) are write-only password inputs in the gateway edit modal, and leaving one blank keeps the stored value.
- Gateway parameters are stored encrypted with the app key. Existing plain rows are still read and get encrypted on the next save; rotating `APP_KEY` means re-entering the gateway keys.
- Fixes on top of #14: the fee saved when a payment is processed is rounded too, PayPal amounts are sent as 2-decimal strings, unknown or abstract callback gateways return 404 instead of a 500, a verified payment no longer crashes when the paid model was deleted, and `pay/info` / `pay/initiate` are no longer shadowed by `pay/{trx}`.
